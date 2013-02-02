<?php

namespace WrittenGames\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $message = 'I am a Symfony thingy. Wooohooo!';

        return $this->render( 'WrittenGamesApplicationBundle:Default:index.html.twig', array(
            'message' => $message,
        ));
    }
}
