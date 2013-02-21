<?php

namespace WrittenGames\ApplicationBundle\Entity;

use WrittenGames\ApplicationBundle\Entity\UserIdentity;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user__user")
 */
class User extends BaseUser
{
    /**
     * The purpose of a unique placeholder element is use in a column
     * like `email` which is mapped as `unique` and thus cannot be set
     * to NULL. Since this application implements both traditional and
     * social login, users must have the choice to not provide an email
     * address.
     *
     * By means of overriding the getter and setter for the `email`
     * property, this is handled transparently by the entity class.
     */
    const UNIQUE_PLACEHOLDER_PREFIX = 'notset';

    protected function createUniquePlaceholder()
    {
        return self::UNIQUE_PLACEHOLDER_PREFIX . md5( time() . rand( 0, 99999 ));
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\OneToMany(targetEntity="UserIdentity", mappedBy="user") */
    protected $identities;

    public function __construct()
    {
        parent::__construct();
        $this->email = $this->createUniquePlaceholder();
        $this->emailCanonical = $this->email;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        $email = parent::getEmail();
        if ( false !== strpos( $email, self::UNIQUE_PLACEHOLDER_PREFIX ))
        {
            return NULL;
        }
        return $email;
    }

    public function setEmail( $email )
    {
        if ( NULL === $email || '' === $email )
        {
            $email = $this->createUniquePlaceholder();
            $this->emailCanonical = $email;
        }
        parent::setEmail( $email );
    }

    /**
     * Add identities
     *
     * @param \WrittenGames\ApplicationBundle\Entity\UserIdentity $identity
     * @return User
     */
    public function addIdentity( UserIdentity $identity )
    {
        $this->identities[] = $identity;
        return $this;
    }

    /**
     * Remove identities
     *
     * @param \WrittenGames\ApplicationBundle\Entity\UserIdentity $identity
     */
    public function removeIdentity( UserIdentity $identity )
    {
        $this->identities->removeElement( $identity );
    }

    /**
     * Get identities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdentities()
    {
        return $this->identities;
    }
}