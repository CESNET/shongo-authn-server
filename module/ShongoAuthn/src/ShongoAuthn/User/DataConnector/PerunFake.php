<?php

namespace ShongoAuthn\User\DataConnector;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use ShongoAuthn\User\User;
use PhpIdServer\User\UserInterface;
use PhpIdServer\User\DataConnector\AbstractDataConnector;
use PhpIdServer\User\DataConnector\Exception as DataConnectorException;


class PerunFake extends AbstractDataConnector
{

    const OPT_ADAPTER = 'adapter';

    /**
     * DB adapter.
     * 
     * @var Adapter
     */
    protected $_db = null;


    /**
     * (non-PHPdoc)
     * @see \PhpIdServer\User\DataConnector\DataConnectorInterface::populateUser()
     */
    public function populateUser (UserInterface $user)
    {
        $this->_populateShongoUser($user);
    }


    /**
     * Populates the Shongo specific user entity.
     * 
     * @param User $user
     */
    protected function _populateShongoUser (User $user)
    {
        $userData = $this->_loadUserData($user);
        if (null === $userData) {
            $userData = $this->_saveUserData($user);
        }
        
        $user->populate((array) $userData);
    }


    protected function _loadUserData (User $user)
    {
        return $this->_loadUserDataBy(array(
            'original_id' => $user->getId()
        ));
    }


    protected function _loadUserDataBy (array $conds)
    {
        $db = $this->_getDbHandler();
        $sql = new Sql($db);
        $select = $sql->select();
        
        $select->from('user')
            ->where($conds);
        
        $result = $db->query($sql->getSqlStringForSqlObject($select), $db::QUERY_MODE_EXECUTE);
        $count = $result->count();
        if (! $count) {
            return null;
        }
        
        if ($count > 1) {
            throw new DataConnectorException\InvalidResponseException(sprintf("User query returned more than one results: %d records", $count));
        }
        
        return $result->current();
    }


    protected function _saveUserData (User $user)
    {
        $db = $this->_getDbHandler();
        $sql = new Sql($db);
        $insert = $sql->insert();
        
        $insert->into('user')
            ->values(array(
            'given_name' => $user->getGivenName(), 
            'family_name' => $user->getFamilyName(), 
            'email' => $user->getEmail(), 
            'original_id' => $user->getId(), 
            'register_time' => new Expression('NOW()')
        ));
        
        $result = $db->query($sql->getSqlStringForSqlObject($insert), $db::QUERY_MODE_EXECUTE);
        
        return $this->_loadUserDataBy(array(
            'id' => $result->getGeneratedValue()
        ));
    }


    /**
     * @return \Zend\Db\Adapter\Adapter
     */
    protected function _getDbHandler ()
    {
        if (! ($this->_db instanceof Adapter)) {
            $this->_db = new Adapter($this->getOption(self::OPT_ADAPTER));
        }
        
        return $this->_db;
    }
}