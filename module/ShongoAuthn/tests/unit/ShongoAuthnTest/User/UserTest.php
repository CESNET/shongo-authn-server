<?php

namespace ShongoAuthnTest\User;

use ShongoAuthn\User\AuthenticationInfo;
use ShongoAuthn\User\User;


class UserTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructor()
    {
        $id = 123;
        $name = 'Foo Bar';
        $givenName = 'Foo';
        $familyName = 'Bar';
        $nickname = 'fb';
        $email = 'foo@example.org';
        $phone = '123456789';
        $organization = 'Example Ltd.';
        $locale = 'en';
        $zoneinfo = 'CET';
        $originalId = 'foo@example.org';
        $perunId = 456;
        $perunUrl = 'https://perun/user/456';
        $perunVos = array(
            'vo1',
            'vo2'
        );
        $principalNames = array(
            'foo',
            'bar'
        );
        
        $provider = 'fooProvider';
        $instant = 'yesterday';
        $loa = 13;
        $authenticationInfo = new AuthenticationInfo($provider, $instant, $loa);
        
        $userData = array(
            User::FIELD_ID => $id,
            User::FIELD_NAME => $name,
            User::FIELD_GIVEN_NAME => $givenName,
            User::FIELD_FAMILY_NAME => $familyName,
            User::FIELD_NICKNAME => $nickname,
            User::FIELD_EMAIL => $email,
            User::FIELD_PHONE_NUMBER => $phone,
            User::FIELD_ORGANIZATION => $organization,
            User::FIELD_LOCALE => $locale,
            User::FIELD_ZONEINFO => $zoneinfo,
            User::FIELD_ORIGINAL_ID => $originalId,
            User::FIELD_PERUN_ID => $perunId,
            User::FIELD_PERUN_URL => $perunUrl,
            User::FIELD_PERUN_VOS => $perunVos,
            User::FIELD_PRINCIPAL_NAMES => $principalNames,
            User::FIELD_AUTHENTICATION_INFO => $authenticationInfo
        );
        
        $user = new User($userData);
        
        $this->assertSame($id, $user->getId());
        $this->assertSame($name, $user->getName());
        $this->assertSame($givenName, $user->getGivenName());
        $this->assertSame($familyName, $user->getFamilyName());
        $this->assertSame($nickname, $user->getNickname());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($phone, $user->getPhoneNumber());
        $this->assertSame($organization, $user->getOrganization());
        $this->assertSame($locale, $user->getLocale());
        $this->assertSame($zoneinfo, $user->getZoneinfo());
        $this->assertSame($originalId, $user->getOriginalId());
        $this->assertSame($perunId, $user->getPerunId());
        $this->assertSame($perunUrl, $user->getPerunUrl());
        $this->assertSame($perunVos, $user->getPerunVos());
        $this->assertSame($principalNames, $user->getPrincipalNames());
        $this->assertSame($authenticationInfo, $user->getAuthenticationInfo());
        
        $expectedUserData = $userData;
        $expectedUserData['authentication_info'] = array(
            'provider' => $provider,
            'instant' => $instant,
            'loa' => $loa
        );
        
        $this->assertEquals($expectedUserData, $user->toArray());
    }
}