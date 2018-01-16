<?php

namespace PhpPact;

class PactUriOptions
{
    const AuthScheme = 'Basic';
    private $_baseUri;
    private $_username;
    private $_password;

    /**
     * PactUriOptions constructor.
     *
     * @param $uri - note that ending / will be stripped
     * @param null $username
     * @param null $password
     */
    public function __construct($uri, $username = null, $password = null)
    {
        if (!$uri) {
            throw new  \InvalidArgumentException('uri is null or empty.');
        }

        if ($username) {
            $this->_username = $username;
        }

        if ($password) {
            $this->_password = $password;
        }

        if (\substr($uri, -1) == '/') {
            $uri = \substr($uri, 0, \strlen($uri) - 1);
        }

        $this->_baseUri = $uri;
    }

    public function getUsername()
    {
        return isset($this->_username)?$this->_username:false;
    }

    /**
     * @param null $username
     *
     * @return PactUriOptions
     */
    public function setUsername($username)
    {
        if (!$username) {
            throw new \InvalidArgumentException('username is null or empty.');
        }

        if (\stripos(':', $username) !== false) {
            throw new \InvalidArgumentException("username contains a ':' character, which is not allowed.");
        }

        $this->_username = $username;

        return $this;
    }

    public function getPassword()
    {
        return isset($this->_password)?$this->_password:false;
    }

    /**
     * @param null $password
     *
     * @return PactUriOptions
     */
    public function setPassword($password)
    {
        if (!$password) {
            throw new  \InvalidArgumentException('password is null or empty.');
        }

        $this->_password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseUri()
    {
        return $this->_baseUri;
    }

    /**
     * @return string
     */
    public function AuthorizationHeader()
    {
        if (!isset($this->_username) || !isset($this->_password)) {
            throw new \RuntimeException('User name or password is not set');
        }

        return  self::AuthScheme . ' ' . \base64_encode(\utf8_encode(\sprintf('%s:%s', $this->_username, $this->_password)));
    }
}
