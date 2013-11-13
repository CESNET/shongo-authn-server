<?php

namespace ShongoAuthn\Util;

use Zend\Http;


/**
 * Simple manager for deleting cookies.
 */
class CookieManager
{

    /**
     * @var array
     */
    protected $cookies = array();


    /**
     * Constructor.
     * 
     * @param array $cookies
     */
    public function __construct(array $cookies = null)
    {
        if (null === $cookies) {
            $cookies = $this->getCookiesFromRequest(new Http\PhpEnvironment\Request());
        }
        
        $this->cookies = $cookies;
    }


    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }


    /**
     * Clears all cookies.
     */
    public function clearCookies()
    {
        foreach ($this->cookies as $name => $value) {
            setcookie($name, null, null, '/');
        }
    }


    /**
     * Extracts cookies from arequest.
     * 
     * @param Http\Request $request
     * @return array
     */
    public function getCookiesFromRequest(Http\Request $request)
    {
        $cookies = array();
        $cookieHeaders = $request->getCookie();
        if ($cookieHeaders) {
            $cookies = $cookieHeaders->getArrayCopy();
        }
        
        return $cookies;
    }
}