<?php

namespace ShongoAuthn\User\Validator;

use Zend\Uri;
use Zend\Http;
use InoOicServer\User\Validator\AbstractValidator;
use InoOicServer\User\UserInterface;
use InoOicServer\User\Validator\Exception\InvalidUserException;


class PerunUser extends AbstractValidator
{

    const OPT_REGISTRATION_URI = 'registration_uri';

    const VO_SHONGO_NAME = 'shongo';

    const GET_PARAM_VO = 'vo';

    const GET_PARAM_RETURN_URL = 'targetnew';


    public function validate(UserInterface $user)
    {
        /* @var $user \ShongoAuthn\User\User */
        if ($user->getPerunId() && in_array(self::VO_SHONGO_NAME, $user->getPerunVos())) {
            return;
        }
        
        // FIXME - more elegant way
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, null, null, '/');
        }
        
        $e = new InvalidUserException(sprintf("User '%s' is not registered"));
        $redirectUri = $this->getRedirectUri();
        $e->setRedirectUri($redirectUri);
        throw $e;
    }


    protected function getRedirectUri()
    {
        $uri = new Uri\Http($this->getOption(self::OPT_REGISTRATION_URI));
        $uri->setQuery(
            array(
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