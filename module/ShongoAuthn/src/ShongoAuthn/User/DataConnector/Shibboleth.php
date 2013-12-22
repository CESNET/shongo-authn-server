<?php

namespace ShongoAuthn\User\DataConnector;

use InoOicServer\User\DataConnector\AbstractDataConnector;
use InoOicServer\User\UserInterface;
use ShongoAuthn\User\User;


class Shibboleth extends AbstractDataConnector implements ShongoDataConnectorInterface
{

    /**
     * @var array
     */
    protected $serverVars;

    /**
     * @var array
     */
    protected $attributeMapping = array(
        'Shib-Identity-Provider' => 'provider',
        'Shib-Authentication-Instant' => 'instant'
    );


    /**
     * @return array
     */
    public function getServerVars()
    {
        if (null === $this->serverVars) {
            $this->serverVars = $_SERVER;
        }
        return $this->serverVars;
    }


    /**
     * @param array $serverVars
     */
    public function setServerVars(array $serverVars)
    {
        $this->serverVars = $serverVars;
    }


    /**
     * @return array
     */
    public function getAttributeMapping()
    {
        return $this->attributeMapping;
    }


    /**
     * @param array $attributeMapping
     */
    public function setAttributeMapping($attributeMapping)
    {
        $this->attributeMapping = $attributeMapping;
    }


    /**
     * {@inhertidoc}
     * @see \InoOicServer\User\DataConnector\DataConnectorInterface::populateUser()
     */
    public function populateUser(UserInterface $user)
    {
        $this->populateShongoUser($user);
    }


    /**
     * {@inhertidoc}
     * @see \ShongoAuthn\User\DataConnector\ShongoDataConnectorInterface::populateShongoUser()
     */
    public function populateShongoUser(User $user)
    {
        $authenticationInfo = array();
        foreach ($this->getAttributeMapping() as $serverVarName => $internalVarName) {
            if ($value = $this->getServerVar($serverVarName)) {
                $authenticationInfo[$internalVarName] = $value;
            }
        }
        
        $user->setAuthenticationInfo($authenticationInfo);
    }


    /**
     * Returns the server variable with the required name.
     * 
     * @param string $varName
     * @return mixed|null
     */
    protected function getServerVar($varName)
    {
        $serverVars = $this->getServerVars();
        if (isset($serverVars[$varName])) {
            return $serverVars[$varName];
        }
        
        return null;
    }
}