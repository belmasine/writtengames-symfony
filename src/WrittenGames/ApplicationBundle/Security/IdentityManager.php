<?php

namespace WrittenGames\ApplicationBundle\Security;

use WrittenGames\ApplicationBundle\Entity\User;
use WrittenGames\ApplicationBundle\Entity\UserIdentity;
use Doctrine\Common\Persistence\ObjectManager;

class IdentityManager
{
    protected $objectManager;

    public function __construct( ObjectManager $om )
    {
        $this->objectManager = $om;
    }

    public function findIdentityBy( $criteria )
    {
        if ( !array_key_exists( 'identifier', $criteria ) || !array_key_exists( 'type', $criteria ))
        {
            throw new \InvalidArgumentException( 'Identity objects must be retrieved by identifier and type' );
        }
        if ( !is_int( $criteria['type'] ))
        {
            $criteria['type'] = $this->getTypeForResourceOwnerName( $criteria['type'] );
        }
        $repo = $this->objectManager->getRepository( 'WrittenGamesApplicationBundle:UserIdentity' );
        return $repo->findOneBy( $criteria );
    }

    public function createIdentity()
    {
        return new UserIdentity();
    }

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    public function updateIdentity( UserIdentity $identity, $andFlush = true )
    {
        if ( !is_int( $identity->getType() ))
        {
            $identity->setType( $this->getTypeForResourceOwnerName( $identity->getType() ));
        }
        $this->objectManager->persist( $identity );
        if ( $andFlush ) $this->objectManager->flush();
    }

    public function getTypeForResourceOwnerName( $name )
    {
        switch ( $name )
        {
            case 'facebook': return UserIdentity::TYPE_FACEBOOK;
            case 'twitter': return UserIdentity::TYPE_TWITTER;
            case 'google': return UserIdentity::TYPE_GOOGLE;
            case 'yahoo': return UserIdentity::TYPE_YAHOO;
        }
    }
}
