<?php

namespace AppBundle\Service;

use \GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class GithubApiClient {

    use ContainerAwareTrait;

    const GITHUB_API_URL = "https://api.github.com/";

    private $client;
    public function __construct() {
        $this->client = new Client(['base_uri' => self::GITHUB_API_URL]);
    }

    /**
     * @param $state
     * @param $page
     * @return array
     */
    public function getIssues($state, $page)
    {

        $session = $this->container->get('session');
        $token = $session->get('token');

        $closedNum = $openNum = 0;
        if (isset($state)){
            $open = $this->client->get('issues', ['query' => [
                'access_token' => $token,
                'state' => 'open'
            ]]);
            $closed = $this->client->get('issues', ['query' => [
                'access_token' => $token,
                'state' => 'closed'
            ]]);
            $openNum = count(json_decode($open->getBody()->getContents(), true));
            $closedNum = count(json_decode($closed->getBody()->getContents(), true));
        }

        $response = $this->client->get('issues', ['query' => [
            'access_token' => $token,
            'state' => $state,
            'per_page' => $this->container->getParameter('issues_per_page'),
            'page' => (int)$page
        ]]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $this->container->get('app.issues.manager')->getIssuesInfo($data, $openNum, $closedNum);
    }

    /**
     * @param $number
     * @return mixed
     */
    public function getIssuesById($number)
    {
        $session = $this->container->get('session');
        $token = $session->get('token');

        $response = $this->client->get('issues', ['query' => [
            'access_token' => $token,
            'state' => 'all',
        ]]);

        $data = json_decode($response->getBody()->getContents(), true);

        $key = array_search($number, array_column($data, 'id'));


        $urlParams = $data[$key];

        $issue = $this->client->get('repos/'.$urlParams['repository']['full_name'].'/issues/'.$urlParams['number'], ['query' => [
            'access_token' => $token,
        ]]);
        $data_issue = json_decode($issue->getBody()->getContents(), true);


        $comments = $this->client->get('repos/'.$urlParams['repository']['full_name'].'/issues/'.$urlParams['number'].'/comments', ['query' => [
            'access_token' => $token,
        ]]);
        $data_comments = json_decode($comments->getBody()->getContents(), true);

        return $this->container->get('app.issues.manager')->getSingleIssueInfo($data_issue, $data_comments);
    }
}