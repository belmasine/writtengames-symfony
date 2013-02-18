<?php

namespace WrittenGames\ApplicationBundle\Entity;

use WrittenGames\ApplicationBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user__identity")
 */
class UserIdentity
{
    const TYPE_GOOGLE   = 1;
    const TYPE_FACEBOOK = 2;
    const TYPE_YAHOO    = 3;
    const TYPE_TWITTER  = 4;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="integer") */
    protected $type;

    /** @ORM\ManyToOne(targetEntity="User", cascade={"persist"}, fetch="EAGER") */
    protected $user;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $identifier;

    /** @ORM\Column(type="string", length=255, nullable=true, name="access_token") */
    protected $accessToken;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $name;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    protected $email;

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
     * Set type
     *
     * @param integer $type
     * @return UserIdentity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return UserIdentity
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set accessToken
     *
     * @param string $accessToken
     * @return UserIdentity
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set user
     *
     * @param \WrittenGames\ApplicationBundle\Entity\User $user
     * @return UserIdentity
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \WrittenGames\ApplicationBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return UserIdentity
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return UserIdentity
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
}