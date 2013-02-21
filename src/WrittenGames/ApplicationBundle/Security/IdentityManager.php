<?php

namespace WrittenGames\ApplicationBundle\Security;

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
            $criteria['type'] = UserIdentity::getStorableType( $criteria['type'] );
        }
        return $this->objectManager
                    ->getRepository( 'WrittenGamesApplicationBundle:UserIdentity' )
                    ->findOneBy( $criteria );
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
        $this->objectManager->persist( $identity );
        if ( $andFlush ) $this->objectManager->flush();
    }
}
