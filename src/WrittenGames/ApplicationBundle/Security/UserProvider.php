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
        $identifier = $response->getUsername();
        $criteria = array(
            'identifier' => $identifier,
            'type' => $resourceOwnerName,
        );
        $existingIdentity = $this->identityManager->findIdentityBy( $criteria );
        if ( $existingIdentity )
        {
            $existingIdentity->setAccessToken( $response->getAccessToken() );
            // return User object for Identity
            return $existingIdentity->getUser();
        }
        // Otherwise create User and Identity objects
        $responseArray = $response->getResponse();
        ////
        $fh = fopen( __DIR__ . '/../../../../app/logs/oauth.log', 'w' );
        fwrite( $fh, print_r( $response->getResponse(), true ));
        fclose( $fh );
        ////
        $user = $this->userManager->createUser();
        $user->setUsername( $responseArray['name'] );
        $user->setEmail( $responseArray['email'] );
        $user->setPassword( 'not set' );
        $user->setEnabled( true );
        $this->userManager->updateUser( $user );
        $identity = $this->identityManager->createIdentity();
        $identity->setAccessToken( $response->getAccessToken() );
        $identity->setIdentifier( $identifier );
        $identity->setType( $resourceOwnerName );
        $identity->setUser( $user );
        $identity->setName( $responseArray['name'] );
        $identity->setEmail( $responseArray['email'] );
        $this->identityManager->updateIdentity( $identity );
        return $user;
    }

}