<?php

namespace ShongoAuthnTest\User\DataConnector;

use ShongoAuthn\User\AuthenticationInfo;
use ShongoAuthn\User\DataConnector\PerunWs;
use ShongoAuthn\User\User;


class PerunWsTest extends \PHPUnit_Framework_TestCase
{

    protected $dataConnector;


    public function setUp()
    {
        $this->dataConnector = new PerunWs();
    }


    public function testPopulateShongoUserData()
    {
        $loa = 13;
        $authenticationInfo = new AuthenticationInfo('fooProvider', 'yesterday');
        $expectedAuthenticationInfo = clone $authenticationInfo;
        $expectedAuthenticationInfo->setLoa($loa);
        
        $user = new User(array(
            'id' => 'foo@bar.com',
            'given_name' => 'Foo',
            'family_name' => 'Bar',
            'name' => 'Foo Bar',
            'email' => 'foobar@foo.com',
            'organization' => 'Foo Inc.',
            'phone_number' => '123456',
            'locale' => 'en',
            'zoneinfo' => 'UTC',
            'perun_id' => 123,
            'principal_names' => array(
                'foo@example.org',
                'bar@example.org'
            ),
            'authentication_info' => $authenticationInfo
        ));
        
        $expected = array(
            'id' => 'foo@bar.com',
            'given_name' => 'FooP',
            'family_name' => 'BarP',
            'name' => 'Foo Bar P',
            'email' => 'foobarP@foo.com',
            'organization' => 'FooP Inc.',
            'phone_number' => '123456P',
            'locale' => 'enP',
            'zoneinfo' => 'UTCP',
            'perun_id' => 123,
            'principal_names' => array(
                'foo@example.org',
                'bar@example.org'
            ),
            'authentication_info' => $expectedAuthenticationInfo->toArray()
        );
        
        $perunUserData = array(
            'id' => $expected['perun_id'],
            'first_name' => $expected['given_name'],
            'last_name' => $expected['family_name'],
            'display_name' => $expected['name'],
            'mail' => $expected['email'],
            'organization' => $expected['organization'],
            'phone' => $expected['phone_number'],
            'language' => $expected['locale'],
            'timezone' => $expected['zoneinfo'],
            'principal_names' => $expected['principal_names'],
            'sources' => array(
                array(
                    'name' => 'fooProvider',
                    'loa' => $loa
                )
            )
        );
        
        $dataConnector = $this->getMockBuilder('ShongoAuthn\User\DataConnector\PerunWs')
            ->setMethods(array(
            'getPerunUserData'
        ))
            ->getMock();
        $dataConnector->expects($this->once())
            ->method('getPerunUserData')
            ->with($user)
            ->will($this->returnValue($perunUserData));
        
        $dataConnector->populateShongoUser($user);
        $this->assertEquals($expected, $user->toArray());
    }


    public function testResolveUserLoAWithMissingSources()
    {
        $this->setExpectedException('ShongoAuthn\User\DataConnector\Exception\InvalidServerDataException');
        
        $user = $this->getUserMock();
        $dataConnector = new PerunWs();
        $dataConnector->resolveUserLoa($user, array());
    }


    public function testResolveUserLoAWithMissingSourcesData()
    {
        $this->setExpectedException('ShongoAuthn\User\DataConnector\Exception\InvalidServerDataException');
        
        $authenticationInfo = new AuthenticationInfo('fooProvider', 'yesterday');
        
        $user = $this->getUserMock();
        $user->expects($this->once())
            ->method('getAuthenticationInfo')
            ->will($this->returnValue($authenticationInfo));
        
        $dataConnector = new PerunWs();
        $dataConnector->resolveUserLoa($user, array(
            'sources' => array()
        ));
    }


    public function testResolveUserLoAWithMissingLoa()
    {
        $this->setExpectedException('ShongoAuthn\User\DataConnector\Exception\InvalidServerDataException');
        
        $authenticationInfo = new AuthenticationInfo('fooProvider', 'yesterday');
        
        $user = $this->getUserMock();
        $user->expects($this->once())
            ->method('getAuthenticationInfo')
            ->will($this->returnValue($authenticationInfo));
        
        $dataConnector = new PerunWs();
        $dataConnector->resolveUserLoa($user, array(
            'sources' => array(
                array(
                    'name' => 'fooProvider'
                )
            )
        ));
    }


    public function testResolveUserLoA()
    {
        $loa = 13;
        
        $authenticationInfo = new AuthenticationInfo('fooProvider', 'yesterday');
        $this->assertSame(0, $authenticationInfo->getLoa());
        
        $user = $this->getUserMock();
        $user->expects($this->once())
            ->method('getAuthenticationInfo')
            ->will($this->returnValue($authenticationInfo));
        
        $dataConnector = new PerunWs();
        $dataConnector->resolveUserLoa($user, array(
            'sources' => array(
                array(
                    'name' => 'fooProvider',
                    'loa' => $loa
                )
            )
        ));
        
        $this->assertSame($loa, $authenticationInfo->getLoa());
    }
    
    /*
     * 
     */
    
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getUserMock()
    {
        $user = $this->getMockBuilder('ShongoAuthn\User\User')
            ->setMethods(array(
            'getAuthenticationInfo'
        ))
            ->getMock();
        return $user;
    }
}