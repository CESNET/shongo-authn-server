<?php

namespace ShongoAuthnTest\User\DataConnector;

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
        $user = new User(
            array(
                'id' => 'foo@bar.com',
                'given_name' => 'Foo',
                'family_name' => 'Bar',
                'name' => 'Foo Bar',
                'email' => 'foobar@foo.com',
                'organization' => 'Foo Inc.',
                'phone_number' => '123456',
                'locale' => 'en',
                'zoneinfo' => 'UTC',
                'perun_id' => 123
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
            'perun_id' => 123
        );
        
        $perunUserData = array(
            'id' => $expected['id'],
            'first_name' => $expected['given_name'],
            'last_name' => $expected['family_name'],
            'display_name' => $expected['name'],
            'mail' => $expected['email'],
            'organization' => $expected['organization'],
            'phone' => $expected['phone_number'],
            'language' => $expected['locale'],
            'timezone' => $expected['zoneinfo']
        );
        
        $dataConnector = $this->getMockBuilder('ShongoAuthn\User\DataConnector\PerunWs')
            ->setMethods(array(
            'getPerunUserData'
        ))
            ->getMock();
        $dataConnector->expects($this->once())
            ->method('getPerunUserData')
            ->with($expected['perun_id'])
            ->will($this->returnValue($perunUserData));
        
        $dataConnector->populateShongoUser($user);
        $this->assertEquals($expected, $user->toArray());
    }
    
    /*
     * 
     */
    protected function getUserMock()
    {
        $user = $this->getMockBuilder('ShongoAuthn\User\User')->getMock();
        return $user;
    }
}