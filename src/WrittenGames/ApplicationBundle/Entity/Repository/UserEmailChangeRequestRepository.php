<?php

namespace WrittenGames\ApplicationBundle\Entity\Repository;

use WrittenGames\ApplicationBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserEmailChangeRequestRepository extends EntityRepository
{
    /**
     * Prevent abuse of the email address change request feature
     *
     * @param \WrittenGames\ApplicationBundle\Entity\User $user
     * @return boolean
     */
    public function isAllowedRequest( User $user )
    {
        // TODO: prevent the same request from being sent twice
        // TODO: prevent a user from making too many change requests
        return true;
    }

    /**
     * For use in a cron job, removes the tokens from
     * requests older than configured number of days
     *
     * @param integer $days
     */
    public function cancelOrphanedRequests( $days = 3 )
    {
        // TODO
    }
}