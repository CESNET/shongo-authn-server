<?php

namespace ShongoAuthn\User\DataConnector;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use ShongoAuthn\User\User;
use InoOicServer\User\UserInterface;
use InoOicServer\User\DataConnector\AbstractDataConnector;
use InoOicServer\User\DataConnector\Exception as DataConnectorException;
use Zend\Db\Sql\SqlInterface;
use Zend\Db\ResultSet\ResultSet;


class PerunFake extends AbstractDataConnector
{

    const OPT_ADAPTER = 'adapter';

    /**
     * DB adapter.
     * 
     * @var Adapter
     */
    protected $dbAdapter = null;

    /**
     * SQL abstraction object.
     * 
     * @var Sql
     */
    protected $sql = null;


    /**
     * Sets the DB adapter.
     * 
     * @param Adapter $dbAdapter
     */
    public function setDbAdapter(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        
        /*
         * temp fix for https://github.com/zendframework/zf2/pull/4081
        */
        $driver = $this->dbAdapter->getDriver();
        $driver->getConnection()
            ->connect();
        $this->dbAdapter->getPlatform()
            ->setDriver($driver);
        /* --- */
        
        $this->setSql($this->createSql($this->dbAdapter));
    }


    /**
     * Returns the DB handler.
     *
     * @return Adapter
     */
    public function getDbAdapter()
    {
        if (! ($this->dbAdapter instanceof Adapter)) {
            $this->setDbAdapter($this->createDbAdapter());
        }
        
        return $this->dbAdapter;
    }


    /**
     * Sets the SQL abstraction object.
     * 
     * @param Sql $sqlObject
     */
    public function setSql(Sql $sqlObject)
    {
        $this->sql = $sqlObject;
    }


    /**
     * Returns the SQL abstraction object.
     * 
     * @return Sql
     */
    public function getSql()
    {
        return $this->sql;
    }


    /**
     * {@inheritdoc}
     * @see \InoOicServer\User\DataConnector\DataConnectorInterface::populateUser()
     */
    public function populateUser(UserInterface $user)
    {
        $this->populateShongoUser($user);
    }


    /**
     * Populates the Shongo specific user entity.
     * 
     * @param User $user
     */
    protected function populateShongoUser(User $user)
    {
        $userData = $this->loadUserData($user);
        if (null === $userData) {
            $userData = $this->saveUserData($user);
        }
        
        $user->populate((array) $userData);
    }


    /**
     * Loads and returns data about the user.
     * 
     * @param User $user
     * @return ResultSet|null
     */
    protected function loadUserData(User $user)
    {
        return $this->loadUserDataBy(array(
            'original_id' => $user->getId()
        ));
    }


    /**
     * Loads user data based on the provided conditions.
     * 
     * @param array $conds
     * @throws DataConnectorException\InvalidResponseException
     * @return ResultSet|null
     */
    protected function loadUserDataBy(array $conds)
    {
        $db = $this->getDbAdapter();
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


    /**
     * Saves user data to the storage.
     * 
     * @param User $user
     * @return ResultSet|null
     */
    protected function saveUserData(User $user)
    {
        $db = $this->getDbAdapter();
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
        
        $result = $db->query($sql->getSqlStringForSqlObject($insert), Adapter::QUERY_MODE_EXECUTE);
        
        return $this->loadUserDataBy(array(
            'id' => $result->getGeneratedValue()
        ));
    }


    /**
     * Creates and returns new DB adapter.
     * 
     * @param array $options
     * @return Adapter
     */
    protected function createDbAdapter(array $options = null)
    {
        if (null === $options) {
            $options = $this->getOption(self::OPT_ADAPTER);
        }
        
        $adapter = new Adapter($options);
        if (isset($options['charset'])) {
            $adapter->query(sprintf("SET NAMES '%s'", $options['charset']), Adapter::QUERY_MODE_EXECUTE);
        }
        
        return $adapter;
    }


    /**
     * Creates and returns new SQL abstraction object.
     * 
     * @param Adapter $dbAdapter
     * @return Sql
     */
    protected function createSql(Adapter $dbAdapter)
    {
        return new Sql($dbAdapter);
    }
}