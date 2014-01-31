<?php

namespace ShongoAuthn\User\Validator;

use Zend\Uri;
use Zend\Http;
use InoOicServer\User\Validator\AbstractValidator;
use InoOicServer\User\UserInterface;
use InoOicServer\User\Validator\Exception\InvalidUserException;
use ShongoAuthn\Util\CookieManager;
use ShongoAuthn\User\ShongoUserInterface;


class PerunUser extends AbstractValidator
{

    const OPT_REGISTRATION_URI = 'registration_uri';

    const VO_SHONGO_NAME = 'shongo';

    const GET_PARAM_VO = 'vo';

    const GET_PARAM_RETURN_URL = 'targetnew';

    /**
     * @var CookieManager
     */
    protected $cookieManager;


    /**
     * @return CookieManager
     */
    public function getCookieManager()
    {
        if (! $this->cookieManager instanceof CookieManager) {
            $this->cookieManager = new CookieManager();
        }
        return $this->cookieManager;
    }


    /**
     * @param CookieManager $cookieManager
     */
    public function setCookieManager($cookieManager)
    {
        $this->cookieManager = $cookieManager;
    }


    public function validate(UserInterface $user)
    {
        $this->validateShongoUser($user);
    }


    public function validateShongoUser(ShongoUserInterface $user)
    {
        /* @var $user \ShongoAuthn\User\User */

        if (0 !== intval($user->getPerunId())) {
            return;
        }
        
        /*
         * No need to delete cookies, the session can be re-used.
         */
        //$this->getCookieManager()->clearCookies();
        
        $e = new InvalidUserException(sprintf("User '%s' is not registered", $user->getId()));
        $e->setRedirectUri($this->getRedirectUri());
        
        throw $e;
    }


    protected function getRedirectUri()
    {
        $uri = new Uri\Http($this->getOption(self::OPT_REGISTRATION_URI));
        $uri->setQuery(array(
            self::GET_PARAM_VO => self::VO_SHONGO_NAME,
            self::GET_PARAM_RETURN_URL => $this->getReturnUri()
        ));
        
        return $uri->toString();
    }


    protected function getReturnUri()
    {
        $httpRequest = $this->getSessionContainer()->offsetGet('http_request');
        if (! $httpRequest instanceof Http\Request) {
            return null;
        }
        
        return $httpRequest->getUriString() . $httpRequest->getRequestUri();
    }
}