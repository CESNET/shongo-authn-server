<?php

namespace ShongoAuthn\User\DataConnector;

use ShongoAuthn\User\User;
use InoOicServer\User\UserInterface;
use InoOicServer\User\DataConnector\AbstractDataConnector;


/**
 * Data connector retrieving attributes set by the Perun Attribute Authority.
 */
class PerunAa extends AbstractDataConnector implements ShongoDataConnectorInterface
{

    const OPT_PERUN_ID_VAR_NAME = 'perun_id_var_name';

    const OPT_PERUN_VO_NAME_VAR_NAME = 'perun_vo_name_var_name';

    const MULTI_VALUE_DELIMITER = ';';

    /**
     * The server system variables ($_SERVER).
     * @var array
     */
    protected $serverVars = array();


    /**
     * Constructor.
     * 
     * @param array $options
     * @param array $serverVars
     */
    public function __construct(array $options = array(), array $serverVars = null)
    {
        parent::__construct($options);
        
        if (null === $serverVars) {
            $serverVars = $_SERVER;
        }
        
        $this->setServerVars($serverVars);
    }


    /**
     * @param array $serverVars
     */
    public function setServerVars(array $serverVars)
    {
        $this->serverVars = $serverVars;
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
        if (($perunId = $this->extractPerunId()) && $perunId) {
            $user->setPerunId($perunId);
        }
        
        if (($perunVos = $this->extractPerunVos())) {
            $user->setPerunVos($perunVos);
        }
    }


    /**
     * Extract the user's Perun ID from the server variables.
     * 
     * @throws Exception\MissingOptionException
     * @return integer
     */
    protected function extractPerunId()
    {
        $varName = $this->getOption(self::OPT_PERUN_ID_VAR_NAME);
        if (null === $varName) {
            throw new Exception\MissingOptionException(self::OPT_PERUN_ID_VAR_NAME);
        }
        
        return intval($this->getServerVar($varName));
    }


    /**
     * Extracts the list of Perun VOs, the user is member of.
     * 
     * @return array
     */
    protected function extractPerunVos()
    {
        $varName = $this->getOption(self::OPT_PERUN_VO_NAME_VAR_NAME);
        if (null === $varName) {
            throw new Exception\MissingOptionException(self::OPT_PERUN_VO_NAME_VAR_NAME);
        }
        
        $rawValue = $this->getServerVar($varName);
        
        return explode(self::MULTI_VALUE_DELIMITER, $rawValue);
    }


    /**
     * Return a specific server variable value.
     * 
     * @param string $varName
     * @return string|null
     */
    protected function getServerVar($varName)
    {
        if (isset($this->serverVars[$varName])) {
            return $this->serverVars[$varName];
        }
        
        return null;
    }
}