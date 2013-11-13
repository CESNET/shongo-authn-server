<?php

namespace ShongoAuthnTest\Util;

use ShongoAuthn\Util\CookieManager;
use Zend\Http\Request;


class CookieManagerTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructor()
    {
        $cookies = array(
            'foo' => 'bar'
        );
        $cookieManager = new CookieManager($cookies);
        $this->assertEquals($cookies, $cookieManager->getCookies());
    }


    public function testGetCookiesFromRequest()
    {
        $cookies = array(
            'foo' => 'bar'
        );
        
        $cookieHeaders = $this->getMock('Zend\Http\Header\Cookie');
        $cookieHeaders->expects($this->once())
            ->method('getArrayCopy')
            ->will($this->returnValue($cookies));
        
        $request = $this->getMockBuilder('Zend\Http\Request')->getMock();
        $request->expects($this->once())
            ->method('getCookie')
            ->will($this->returnValue($cookieHeaders));
        
        $cookieManager = new CookieManager();
        $this->assertEquals($cookies, $cookieManager->getCookiesFromRequest($request));
    }
}