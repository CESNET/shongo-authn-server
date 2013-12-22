<?php

namespace ShongoAuthnTest\User\UserInfo\Mapper;

use ShongoAuthn\User\UserInfo\Mapper\Shongo;
use ShongoAuthn\User\User;


class ShongoTest extends \PHPUnit_Framework_TestCase
{

    protected $mapper;


    public function setUp()
    {
        $this->mapper = new Shongo();
    }


    public function testGetUserInfoData()
    {
        $id = 'foobar';
        $perunId = 123;
        $firstName = 'Foo';
        $lastName = 'Bar';
        $displayName = 'Foo Bar';
        $email = 'foobar@email.com';
        $organization = 'Foo Inc.';
        $language = 'en';
        $phone = '123456';
        $zoneInfo = 'CET';
        $perunUrl = 'http://perun/url';
        $principalNames = array(
            'foo',
            'bar'
        );
        $authenticationInfo = array(
            'provider' => 'fooProvider',
            'instant' => 'yesterday',
            'loa' => 13
        );
        
        $user = new User();
        $user->setId($id);
        $user->setPerunId($perunId);
        $user->setName($displayName);
        $user->setGivenName($firstName);
        $user->setFamilyName($lastName);
        $user->setEmail($email);
        $user->setOrganization($organization);
        $user->setLocale($language);
        $user->setPhoneNumber($phone);
        $user->setZoneinfo($zoneInfo);
        $user->setPerunUrl($perunUrl);
        $user->setPrincipalNames($principalNames);
        $user->setAuthenticationInfo($authenticationInfo);
        
        $expected = array(
            'id' => $perunId,
            'original_id' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'display_name' => $displayName,
            'mail' => $email,
            'organization' => $organization,
            'language' => $language,
            'phone' => $phone,
            'zoneinfo' => $zoneInfo,
            'perun_url' => $perunUrl,
            'principal_names' => array(
                'foo',
                'bar'
            ),
            'authentication_info' => $authenticationInfo
        );
        
        $this->assertEquals($expected, $this->mapper->getUserInfoData($user));
    }
}