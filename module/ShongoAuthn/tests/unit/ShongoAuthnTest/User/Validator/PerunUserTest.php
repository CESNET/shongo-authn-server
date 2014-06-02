<?php

namespace ShongoAuthnTest\User\Validator;

use ShongoAuthn\User\Validator\PerunUser;
use InoOicServer\User\Validator\Exception\InvalidUserException;


class PerunUserTest extends \PHPUnit_Framework_TestCase
{


    public function testGetCookieManagerWithImplicitValue()
    {
        $validator = new PerunUser();
        $cookieManager = $validator->getCookieManager();
        $this->assertInstanceOf('ShongoAuthn\Util\CookieManager', $cookieManager);
    }


    public function testValidateOk()
    {
        $validator = new PerunUser();
        $validator->validate($this->getUserMock(123));
    }


    public function testValidateWithInvalidUser()
    {
        $redirectUri = 'https://redirect/';
        $user = $this->getUserMock(null);
        
        $validator = $this->getMockBuilder('ShongoAuthn\User\Validator\PerunUser')
            ->setMethods(array(
            'getRedirectUri'
        ))
            ->getMock();
        $validator->expects($this->once())
            ->method('getRedirectUri')
            ->will($this->returnValue($redirectUri));
        
        /*
        $cookieManager = $this->getMock('ShongoAuthn\Util\CookieManager');
        $cookieManager->expects($this->once())
            ->method('clearCookies');
        $validator->setCookieManager($cookieManager);
        */
        
        try {
            $validator->validate($user);
        } catch (InvalidUserException $e) {
            $this->assertSame($redirectUri, $e->getRedirectUri());
            return;
        }
        
        $this->fail("Expected exception 'InvalidUserException' was not thrown");
    }
    
    /*
     * 
     */
    protected function getUserMock($perunId = null)
    {
        $user = $this->getMock('ShongoAuthn\User\User');
        $user->expects($this->once())
            ->method('getPerunId')
            ->will($this->returnValue($perunId));
        return $user;
    }
}