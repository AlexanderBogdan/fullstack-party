<?php

namespace AppBundle\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Zend\Diactoros\Response;
use \GuzzleHttp\Client;

class AuthController extends Controller
{

    /**
     * @Route("/login", name="login")
     **/
    public function loginAction(ServerRequestInterface $request)
    {
        $session = new Session();

        $query = $request->getQueryParams();

        $response = new Response;

        if (!isset($query['code'])) {
            $requestQuery = [
                'client_id' => $this->container->getParameter('client_id'),
                'scope' => 'repo,user'
                ];
            $uri = 'https://github.com/login/oauth/authorize?' . http_build_query($requestQuery);
            return $response->withStatus(302)->withHeader('Location', $uri);
        } else {
            $client = new Client(['base_uri' => 'https://github.com/']);
            $response = $client->post('login/oauth/access_token', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'client_id' => $this->container->getParameter('client_id'),
                    'client_secret' => $this->container->getParameter('client_secret'),
                    'code' => $query['code'],
                    ])
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['access_token'])) {
                throw new \Exception('Access do not exist');
            }

            $session->set('token', $data['access_token']);

            return $response->withStatus(302)->withHeader('Location', '/issues/open');
        }
    }

    /**
     * @Route("/logout", name="logout")
     **/
    public function logoutAction(ServerRequestInterface $request)
    {
        $response = new Response;
        unset($_SESSION['token']);
        return $response->withStatus(302)->withHeader('Location', '/');
    }
}


