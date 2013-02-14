<?php

namespace WrittenGames\ApplicationBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use WrittenGames\ApplicationBundle\Entity\User;

class UserAccountMergedEvent extends Event
{
    const ID = 'security.user_accounts_merged';

    /**
     * @var $mergingUser WrittenGames\ApplicationBundle\Entity\User
     */
    protected $mergingUser;

    /**
     * @var $mergedUser WrittenGames\ApplicationBundle\Entity\User
     */
    protected $mergedUser;

    /**
     * @return WrittenGames\ApplicationBundle\Entity\User
     */
    public function getMergingUser()
    {
        return $this->mergingUser;
    }

    /**
     * @param $user Description WrittenGames\ApplicationBundle\Entity\User
     */
    public function setMergingUser( User $user )
    {
        $this->mergingUser = $user;
    }

    /**
     * @return WrittenGames\ApplicationBundle\Entity\User
     */
    public function getMergedUser()
    {
        return $this->mergedUser;
    }

    /**
     * @param $user Description WrittenGames\ApplicationBundle\Entity\User
     */
    public function setMergedUser( User $user )
    {
        $this->mergedUser = $user;
    }
}
