<?php

namespace WrittenGames\ApplicationBundle\Util;

use FOS\UserBundle\Util\Canonicalizer as BaseCanonicaliser;

class Canonicaliser extends BaseCanonicaliser
{
    /**
     * Correctly spelled method name... bloody Yanks <.<
     *
     * @param type $string
     */
    public function canonicalise( $string )
    {
        $string = parent::canonicalize( $string );
        return str_replace( ' ', '.', $string );
    }

    /**
     * {@inheritdoc}
     */
    public function canonicalize( $string )
    {
        return $this->canonicalise( $string );
    }
}
