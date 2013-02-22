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
        return $this->saveUserDataAndGuardAgainstNonPermittedEdit( $request, 'username' );
    }

    public function requestChangeEmailAction( Request $request )
    {
    }

    public function saveEmailAction( Request $request )
    {
        return $this->saveUserDataAndGuardAgainstNonPermittedEdit( $request, 'email' );
    }

    public function savePasswordAction( Request $request )
    {
        return $this->saveUserDataAndGuardAgainstNonPermittedEdit( $request, 'password' );
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
        $user = $repo->findOneByUsernameSlug( $request->get( 'username_slug' ));
        $currentUser = $this->get( 'security.context' )->getToken()->getUser();
        if ( $currentUser->getId() != $user->getId() && !$this->get( 'security.context' )->isGranted( 'ROLE_ADMIN' ))
        {
            throw new NotFoundHttpException();
        }
        $requestedUsername = $request->get( 'username' );
        if ( $requestedUsername != $user->getUsername() )
        {
            // TODO: query using slug instead
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

    private function showAndEdit( Request $request, $action )
    {
        $user = $this->getDoctrine()
                     ->getRepository( 'WrittenGamesApplicationBundle:User' )
                     ->findOneByUsernameSlug( $request->get( 'username_slug' ));
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

    private function saveUserDataAndGuardAgainstNonPermittedEdit( Request $request, $key )
    {
        // Get the User object in question
        $repo = $this->getDoctrine()->getRepository( 'WrittenGamesApplicationBundle:User' );
        $user = $repo->findOneByUsernameSlug( $request->get( 'username_slug' ));
        if ( !$user ) throw new NotFoundHttpException();
        // Make sure the current user has editing rights
        $currentUser = $this->get( 'security.context' )->getToken()->getUser();
        if ( $currentUser->getId() != $user->getId() && !$this->get( 'security.context' )->isGranted( 'ROLE_ADMIN' ))
        {
            throw new AccessDeniedException();
        }
        switch ( $key )
        {
            case 'username':
                $user->setUsername( $request->get( $key ));
                break;
            case 'email':
                $user->setEmail( $request->get( $key ));
                break;
            case 'password':
                $user->setPlainPassword( $request->get( $key ));
                break;
        }
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->redirect(
                    $this->generateUrl( 'wg_profile_edit', array(
                        'username_slug' => $user->getUsernameSlug()
                    )));
    }

}
