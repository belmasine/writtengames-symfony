<?php

namespace WrittenGames\ApplicationBundle\Util;

use \Swift_Message;

class Email
{
    protected $mailer;
    protected $systemEmailAdress;

    public function __construct( $mailer, $systemEmailAccount, $websiteDomain )
    {
        $this->mailer = $mailer;
        $this->systemEmailAdress = $systemEmailAccount . '@' . $websiteDomain;
}

    public function send( $to, $subject, $body, $from = null, $replyTo = null )
    {
        if ( !$from ) $from = $this->systemEmailAdress;
        if ( !$replyTo ) $replyTo = $from;
        $message = Swift_Message::newInstance()
                                    ->setContentType( 'text/html; charset=UTF-8' )
                                    ->setSubject( $subject )
                                    ->setFrom( $from )
                                    ->setReplyTo( $replyTo )
                                    ->setTo( $to )
                                    ->setBody( $body );
        $this->mailer->send( $message );
    }
}
