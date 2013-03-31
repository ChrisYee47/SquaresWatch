<?php

namespace DrawSmooth\ScoreGetterBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Goutte\Client;

class DefaultController extends Controller
{
    public function indexAction() {
    	$logger=$this->get('logger');

    	$crawler = $this->establishConnection();
    	$gamesList = $this->getGamesList($crawler);

    	return $this->render('DrawSmoothScoreGetterBundle:Default:index.html.twig', array('name' => 'Test Two'));
    }


    public function gamesListAction() {
        $crawler = $this->establishConnection();
        $gamesList = $this->getGamesList($crawler);

        $retVal = json_encode($gamesList);
        $response = new Response($retVal);
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }


    private function establishConnection($siteName = 'ESPN', $date = '20130330') {
        switch ($siteName) {
            case 'ESPN':
                $url = "http://scores.espn.go.com/ncb/scoreboard?date=" . $date;
                break;
            default:
                return NULL;
        }

        $client = new Client();
        $crawler = $client->request('GET', $url);
        if (!$crawler) {
            return NULL;
        } else {
            return $crawler; 
        }
    }


    private function getGamesList($crawler) {
        $logger = $this->get('logger');
        $gameContainer = $crawler->filter('.gameDay-Container');

        $gameBoxes = $gameContainer->filterXPath('//*[contains(@class, "gameCount")]');
        $gameHeaders = $gameBoxes->filterXPath('//*[contains(@id, "gameHeader")]');
        $headersClone = $gameBoxes->filterXPath('//*[contains(@id, "gameHeader")]');

        $gamesList = array();

        $getGameInfoFunc = function($item) use (&$gamesList, $headersClone, $logger) {
            $idString = $item->getAttribute('id');
            $id = strtok($idString, "-");

            $statusQuery = '#' . $id . '-statusLine1';
            $statusLine = $headersClone->filter($statusQuery);
            $gameStatus = 'Final';
            $statusLine->each(function($statusItem) use (&$gameStatus, $logger) {
                $textContent = $statusItem->textContent;
                switch ($textContent) {
                    case 'Final':
                        $gameStatus = 'Final';
                        break;
                    default:
                        $gameStatus = 'In Progress';
                        break;
                }
            });

            $awayNameQuery = '#' . $id . '-aTeamName';
            $awayTeamNameLine = $headersClone->filter($awayNameQuery);
            $awayTeamName = "";
            $awayTeamNameLine->each(function($teamNameItem) use (&$awayTeamName, $logger) {
                $awayTeamName = $teamNameItem->nodeValue;
            });

            $homeNameQuery = '#' . $id . '-hTeamName';
            $homeTeamNameLine = $headersClone->filter($homeNameQuery);
            $homeTeamName = "";
            $homeTeamNameLine->each(function($teamNameItem) use (&$homeTeamName, $logger) {
                $homeTeamName = $teamNameItem->nodeValue;
            });

            $gameArray = array(
                'id' => $id,
                'status' => $gameStatus,
                'away' => $awayTeamName,
                'home' => $homeTeamName
            );
            $gamesList[] = $gameArray;
        };
        $gameHeaders->each($getGameInfoFunc);

        return $gamesList;
    }
}
