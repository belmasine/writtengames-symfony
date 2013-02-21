<?php

namespace WrittenGames\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{

    const EDIT = 'edit';
    const SHOW = 'show';

    public function editAction( Request $request )
    {
        return $this->showAndEdit( $request, self::EDIT );
    }

    public function showAction( Request $request )
    {
        return $this->showAndEdit( $request, self::SHOW );
    }

    public function saveUsernameAction( Request $request )
    {
        // Get the User object in question
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WrittenGamesApplicationBundle:User' );
        $user = $repo->findOneByUsernameCanonical( $request->get( 'canonical_username' ));
        // Make sure the current user has editing rights
        $currentUser = $this->get( 'security.context' )->getToken()->getUser();
        if ( $currentUser->getId() != $user->getId() && !$this->get( 'security.context' )->isGranted( 'ROLE_ADMIN' ))
        {
            throw new NotFoundHttpException();
        }
        // Persist the new username and redirect
        $user->setUsername( $request->get( 'username' ));
        $em->flush();
        return $this->redirect(
                    $this->generateUrl( 'wg_profile_edit', array(
                        'canonical_username' => $user->getUsernameCanonical()
                    )));
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
     * AJAX action for checking username availability
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usernameAvailableAction( Request $request )
    {
        $response = array(
            'cssClass' => 'success',
            'text' => 'username is available',
        );
        $repo = $this->getDoctrine()->getRepository( 'WrittenGamesApplicationBundle:User' );
        $user = $repo->findOneByUsernameCanonical( $request->get( 'canonical_username' ));
        $currentUser = $this->get( 'security.context' )->getToken()->getUser();
        if ( $currentUser->getId() != $user->getId() && !$this->get( 'security.context' )->isGranted( 'ROLE_ADMIN' ))
        {
            throw new NotFoundHttpException();
        }
        $requestedUsername = $request->get( 'username' );
        if ( $requestedUsername != $user->getUsername() )
        {
            $users = $repo->findByUsername( $requestedUsername );
            if ( count( $users ) > 0 )
            {
                $response['cssClass'] = 'error';
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

    private function showAndEdit( Request $request, $action )
    {
        $user = $this->getUserByCanonicalUsername( $request->get( 'canonical_username' ));
        if ( $user )
        {
            $currentUser = $this->get( 'security.context' )->getToken()->getUser();
            if (
                    self::SHOW == $action
                    || $currentUser->getId() == $user->getId()
                    || $this->get( 'security.context' )->isGranted( 'ROLE_ADMIN' )
            )
            {
                return $this->render( 'WrittenGamesApplicationBundle:Profile:' . $action . '.html.twig', array(
                    'user' => $user,
                ));
            }
            throw new AccessDeniedException();
        }
        throw new NotFoundHttpException();
    }

}
