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
     * The purpose of a unique placeholder element is use in a column like
     * `emailCanonical` which is mapped as `unique` and thus cannot be set
     * to NULL. Since this application implements both a traditional and a
     * social login, the user must have the choice to not provide an email
     * address.
     *
     * By means of overriding the setters for both the `emailCanonical` and
     * `email` properties, this is handled transparently by the entity class.
     */
    const UNIQUE_PLACEHOLDER_SUFFIX = '@not.set';

    protected function createUniquePlaceholder()
    {
        return md5( time() . rand( 0, 99999 )) . self::UNIQUE_PLACEHOLDER_SUFFIX;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\OneToMany(targetEntity="UserIdentity", mappedBy="user") */
    protected $identities;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {
        if ( !$email ) $this->setEmailCanonical( NULL );
        parent::setEmail($email);
    }

    /**
     * {@inheritDoc}
     */
    public function setEmailCanonical( $emailCanonical )
    {
        $emailCanonical = $emailCanonical ?: $this->createUniquePlaceholder();
        parent::setEmailCanonical( $emailCanonical );
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