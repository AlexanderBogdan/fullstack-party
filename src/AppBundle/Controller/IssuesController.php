<?php

namespace AppBundle\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class IssuesController extends Controller implements TokenAuthenticatedController
{
    /**
     * @Route("/issues/{state}/{page}", name="issues", defaults={"page": 1},
     *     requirements= {"state": "open|closed", "page": "\d+"})
     **/
    public function getIssuesAction(ServerRequestInterface $request, $state, $page)
    {
        return $this->render('issues/list.html.twig', [
            'data' => $this->container->get('app.githib.api.client')->getIssues($state, $page)
        ]);
    }


    /**
     * @Route("/issue/{number}", name="issue",
     *     requirements= {"number": "\d+"})
     **/
    public function getIssuesByIdAction(ServerRequestInterface $request, $number)
    {
        return $this->render('issues/entry.html.twig', [
            'data' => $this->container->get('app.githib.api.client')->getIssuesById($number)
        ]);
    }

}


