<?php

namespace WrittenGames\ApplicationBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class UserAccountMergedEvent extends Event
{
    const ID = 'event.security.user_accounts_merged';
}
