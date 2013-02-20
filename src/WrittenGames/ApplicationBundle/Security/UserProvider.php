<?php

namespace WrittenGames\ApplicationBundle\Security;

use WrittenGames\ApplicationBundle\Security\IdentityManager;
use WrittenGames\ApplicationBundle\Event\UserAccountMergedEvent;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

class UserProvider implements OAuthAwareUserProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var IdentityManager
     */
    protected $identityManager;

    /**
     * @var ContainerAwareEventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager FOSUB user provider.
     * @param IdentityManager      $identityManager  Identity manager
     */
    public function __construct( UserManagerInterface $userManager, IdentityManager $identityManager, ContainerAwareEventDispatcher $eventDispatcher )
    {
        $this->userManager = $userManager;
        $this->identityManager = $identityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function connect( $user, UserResponseInterface $response )
    {
        /**
         * TODO: rewrite this method according to new model
         */
        $this->eventDispatcher->dispatch(
                                    UserAccountMergedEvent::ID,
                                    new UserAccountMergedEvent( 'User accounts merged' )
                                );
        /**
         *  tag your event listener like so:
         *  - { name: kernel.event_listener, event: security.user_accounts_merged, method: onEvent }
         */
        die( 'Method connect() not implemented yet' );
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse( UserResponseInterface $response )
    {
        // try to retrieve Identity object for response
        $resourceOwnerName = $response->getResourceOwner()->getName();
        $accessToken = $response->getAccessToken();
        if ( 'twitter' == $resourceOwnerName )
        {
            $accessToken = $accessToken['oauth_token'];
            //echo "<pre>" . print_r( $accessToken, true ) . "</pre>"; die(); exit;
        }
        $identifier = $response->getUsername();
        $criteria = array(
            'identifier' => $identifier,
            'type' => $resourceOwnerName,
        );
        $existingIdentity = $this->identityManager->findIdentityBy( $criteria );
        if ( $existingIdentity )
        {
            $existingIdentity->setAccessToken( $accessToken );
            // return User object for Identity
            return $existingIdentity->getUser();
        }
        // Otherwise create User and Identity objects
        $responseArray = $response->getResponse();
        #echo '<pre>' . print_r( $responseArray, true ) . '</pre>'; die(); exit;
        ////
        $fh = fopen( __DIR__ . '/../../../../app/logs/oauth.log', 'w' );
        fwrite( $fh, print_r( $responseArray, true ));
        fclose( $fh );
        ////
        $user = $this->userManager->createUser();
        $username = $this->createUniqueUsername(
                        array_key_exists( 'name', $responseArray )
                            ? $responseArray['name']
                            : $identifier
                    );
        $user->setUsername( $username );
        if ( array_key_exists( 'email', $responseArray ))
        {
            $user->setEmail( $responseArray['email'] );
        }
        else $user->setEmail( $this->createUniquePlaceholder() );
        $user->setPassword( 'not set' );
        $user->setEnabled( true );
        $this->userManager->updateUser( $user );
        $identity = $this->identityManager->createIdentity();
        $identity->setAccessToken( $accessToken );
        $identity->setIdentifier( $identifier );
        $identity->setType( $resourceOwnerName );
        $identity->setUser( $user );
        if ( array_key_exists( 'name', $responseArray ))
        {
            $identity->setName( $responseArray['name'] );
        }
        if ( array_key_exists( 'email', $responseArray ))
        {
            $identity->setEmail( $responseArray['email'] );
        }
        $this->identityManager->updateIdentity( $identity );
        return $user;
    }

    protected function createUniqueUsername( $username )
    {
        $originalName = $username;
        $existingUser = $this->userManager->findUserByUsername( $username );
        $suffix = 0;
        while ( $existingUser )
        {
            $suffix++;
            $username = $originalName . $suffix;
            $existingUser = $this->userManager->findUserByUsername( $username );
        }
        return $username;
    }

    protected function createUniquePlaceholder()
    {
        return 'notset' . md5( time() );
    }

}