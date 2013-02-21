<?php

namespace WrittenGames\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{

    public function editAction( Request $request )
    {
        $user = $this->getUserByCanonicalUsername( $request->get( 'canonical_username' ));
        if ( !$user ) throw new NotFoundHttpException();
        return $this->render( 'WrittenGamesApplicationBundle:Profile:edit.html.twig', array(
            'user' => $user,
        ));
    }

    public function showAction( Request $request )
    {
        $user = $this->getUserByCanonicalUsername( $request->get( 'canonical_username' ));
        if ( !$user ) throw new NotFoundHttpException();
        return $this->render( 'WrittenGamesApplicationBundle:Profile:show.html.twig', array(
            'user' => $user,
        ));
    }

    public function saveUsernameAction( Request $request )
    {
        return $this->redirect( $this->generateUrl( 'wg_profile_edit' ));
    }

    public function saveEmailAction( Request $request )
    {
        return $this->redirect( $this->generateUrl( 'wg_profile_edit' ));
    }

    public function savePasswordAction( Request $request )
    {
        return $this->redirect( $this->generateUrl( 'wg_profile_edit' ));
    }

    /**
     * AJAX action
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usernameAvailableAction( Request $request )
    {
        $response = array(
            'class' => 'success',
            'text' => 'username is available',
        );
        $repo = $this->getDoctrine()->getRepository( 'WrittenGamesApplicationBundle:User' );
        $user = $repo->findOneByUsernameCanonical( $request->get( 'canonical_username' ));
        $currentUser = $this->get( 'security.context' )->getToken()->getUser(); // check if admin or same user
        $requestedUsername = $request->get( 'username' );
        if ( $requestedUsername != $user->getUsername() )
        {
            $users = $repo->findByUsername( $requestedUsername );
            if ( count( $users ) > 0 )
            {
                $response['class'] = 'error';
                $response['text'] = 'username not available';
            }
        }
        return new Response( json_encode( $response ));
    }

    /**
     * Private methods for use in this Controller's public methods
     */
    private function getUserByCanonicalUsername( $canonicalUsername )
    {
        return $this->getDoctrine()
                    ->getRepository( 'WrittenGamesApplicationBundle:User' )
                    ->findOneByUsernameCanonical( $canonicalUsername );
    }

}
