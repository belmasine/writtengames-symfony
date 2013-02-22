<?php

namespace WrittenGames\ApplicationBundle\Entity;

use WrittenGames\ApplicationBundle\Entity\UserIdentity;
use FOS\UserBundle\Entity\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="WrittenGames\ApplicationBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="user__user")
 */
class User extends BaseUser
{
    /**
     * The purpose of this unique placeholder element is its use in a column
     * like `emailCanonical` which is mapped as `unique` and thus cannot be
     * set to NULL. Since this application implements both a traditional and
     * a social login, the user must have the choice to not provide an email
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

    /**
     * @Gedmo\Slug(fields={"username"}, separator="-")
     * @ORM\Column(name="username_slug", type="string", length=127, unique=true)
     */
    protected $usernameSlug;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /** @ORM\OneToMany(targetEntity="UserIdentity", mappedBy="user") */
    protected $identities;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->identities = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {
        if ( !$email ) $this->setEmailCanonical( NULL );
        if ( NULL === $email ) $email = '';
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

    /**
     * Set usernameSlug
     *
     * @param string $usernameSlug
     * @return User
     */
    public function setUsernameSlug($usernameSlug)
    {
        $this->usernameSlug = $usernameSlug;

        return $this;
    }

    /**
     * Get usernameSlug
     *
     * @return string
     */
    public function getUsernameSlug()
    {
        return $this->usernameSlug;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}