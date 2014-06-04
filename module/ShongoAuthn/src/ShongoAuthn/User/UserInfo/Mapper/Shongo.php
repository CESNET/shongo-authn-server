<?php

namespace ShongoAuthn\User\UserInfo\Mapper;

use InoOicServer\User\UserInterface;
use InoOicServer\User\UserInfo\Mapper\AbstractMapper;


/**
 * Maps user fields to output fields.
 */
class Shongo extends AbstractMapper
{

    /**
     * @var array
     */
    protected $fieldMap = array(
        'perun_id' => 'id',
        'id' => 'original_id',
        'name' => 'display_name',
        'given_name' => 'first_name',
        'family_name' => 'last_name',
        'email' => 'mail',
        'phone_number' => 'phone',
        'organization' => 'organization',
        'locale' => 'language',
        'perun_url' => 'perun_url',
        'zoneinfo' => 'zoneinfo',
        'principal_names' => 'principal_names'
    );

    protected $authenticationInfoMap = array(
        'provider' => 'authn_provider',
        'instant' => 'authn_instant',
        'loa' => 'loa'
    );


    /**
     * {@inheritdoc}
     * @see \InoOicServer\User\UserInfo\Mapper\MapperInterface::getUserInfoData()
     */
    public function getUserInfoData(UserInterface $user)
    {
        $userData = $user->toArray();
        
        $mappedData = array();
        foreach ($this->fieldMap as $userField => $outputField) {
            
            if (isset($userData[$userField])) {
                $mappedData[$outputField] = $userData[$userField];
            }
        }
        
        if (isset($userData['authentication_info']) && is_array($userData['authentication_info'])) {
            $authenticationInfoData = $this->extractAuthenticationInfoData($userData['authentication_info']);
            $mappedData = array_merge($mappedData, $authenticationInfoData);
        }
        
        return $mappedData;
    }


    protected function extractAuthenticationInfoData(array $authenticationInfo)
    {
        $mappedData = array();
        foreach ($this->authenticationInfoMap as $inputField => $outputField) {
            if (isset($authenticationInfo[$inputField])) {
                $mappedData[$outputField] = $authenticationInfo[$inputField];
            }
        }
        
        return $mappedData;
    }
}
