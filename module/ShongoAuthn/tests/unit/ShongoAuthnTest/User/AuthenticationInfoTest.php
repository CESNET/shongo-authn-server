<?php

namespace ShongoAuthn\User;


class AuthenticationInfoTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructorWithImplicitLoa()
    {
        $provider = 'foo';
        $instant = 'yesterday';
        $loa = 12;
        
        $info = new AuthenticationInfo($provider, $instant);
        
        $this->assertSame($provider, $info->getProvider());
        $this->assertSame($instant, $info->getInstant());
        $this->assertSame(0, $info->getLoa());
    }


    public function testConstructor()
    {
        $provider = 'foo';
        $instant = 'yesterday';
        $loa = 12;
        
        $info = new AuthenticationInfo($provider, $instant, $loa);
        
        $this->assertSame($provider, $info->getProvider());
        $this->assertSame($instant, $info->getInstant());
        $this->assertSame($loa, $info->getLoa());
    }
}