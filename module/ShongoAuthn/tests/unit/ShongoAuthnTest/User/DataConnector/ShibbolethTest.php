<?php

namespace ShongoAuthnTest\User\DataConnector;

use ShongoAuthn\User\User;
use ShongoAuthn\User\DataConnector\Shibboleth;


class ShibbolethTest extends \PHPUnit_Framework_Testcase
{

    protected $dataConnector;


    public function setUp()
    {
        $this->dataConnector = new Shibboleth();
    }


    public function testSetServerVars()
    {
        $serverVars = array(
            'foo' => 'bar'
        );
        
        $this->dataConnector->setServerVars($serverVars);
        
        $this->assertSame($serverVars, $this->dataConnector->getServerVars());
    }


    public function testPopulateShongoUser()
    {
        $serverVars = array(
            'var1' => 'value1',
            'var2' => 'value2',
            'var3' => 'value3'
        );
        
        $mapping = array(
            'var1' => 'foo',
            'var3' => 'bar'
        );
        
        $expected = array(
            'foo' => 'value1',
            'bar' => 'value3'
        );
        
        $this->dataConnector->setServerVars($serverVars);
        $this->dataConnector->setAttributeMapping($mapping);
        
        $user = new User(array(
            'id' => 'testuser'
        ));
        
        $this->dataConnector->populateShongoUser($user);
        
        $this->assertEquals($expected, $user->getAuthenticationInfo());
    }
}