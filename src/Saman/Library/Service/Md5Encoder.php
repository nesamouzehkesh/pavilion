<?php

namespace Saman\Library\Service;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class Md5Encoder implements PasswordEncoderInterface
{
    private $saltmain;

    public function __construct($saltmain) {
        //$this->saltmain = $saltmain;
    }

    public function encodePassword($raw, $salt)
    {
        // we use one global salt at the moment, so ignore $salt
        //return md5($raw.$this->saltmain);
        return md5($raw);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }

}
