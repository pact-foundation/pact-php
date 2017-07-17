<?php

namespace PhpPact;

class PactUriOptions
{
    const AuthScheme = "Basic";
    private $_username;
    private $_password;

    public $AuthorizationScheme = self::AuthScheme;

    public function AuthorizationValue()
    {
        return base64_encode(utf8_encode(sprintf("%s:%s", $this->_username, $this->_password)));
    }

    public function __construct($username, $password)
    {
        if (!$username) {
            throw new \InvalidArgumentException("username is null or empty.");
        }

        if (stripos(":", $username) !== false) {
            throw new \InvalidArgumentException("username contains a ':' character, which is not allowed.");
        }

        if ($password) {
            throw new  \InvalidArgumentException("password is null or empty.");
        }

        $this->_username = $username;
        $this->_password = $password;
    }
}