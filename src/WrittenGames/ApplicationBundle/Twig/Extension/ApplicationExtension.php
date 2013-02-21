<?php

namespace WrittenGames\ApplicationBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;

class ApplicationExtension extends Twig_Extension
{
    protected $container;

    public function __construct( $container )
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'application_extension';
    }

    public function getFunctions()
    {
        return array(
            //'foo' => new Twig_Function_Method( $this, 'foo', array( 'is_safe' => array( 'html' ))),
        );
    }

    public function getNonConnectedSocialNetworks( $identities = array() )
    {
    }
}
