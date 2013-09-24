<?php

namespace ShongoAuthnTest\User\DataConnector;

use ShongoAuthn\User\DataConnector\PerunAa;


class PerunAaTest extends \PHPUnit_Framework_Testcase
{

    protected $dataConnector;


    public function setUp()
    {
        $this->dataConnector = new PerunAa();
    }


    public function testPopulateUserWithMissingOption()
    {
        $this->setExpectedException('ShongoAuthn\User\DataConnector\Exception\MissingOptionException');
        
        $user = $this->getUserMock();
        $this->dataConnector->populateUser($user);
    }


    public function testPopulateUserWithMissingValue()
    {
        $this->dataConnector->setOptions(array(
            PerunAa::OPT_PERUN_ID_VAR_NAME => 'perunId'
        ));
        $this->dataConnector->setServerVars(array());
        
        $user = $this->getUserMock();
        $user->expects($this->never())
            ->method('setPerunId');
        
        $this->dataConnector->populateUser($user);
    }


    public function testPopulateUserWithNonInteger()
    {
        $key = 'perunId';
        $value = 'foo';
        
        $this->dataConnector->setOptions(array(
            PerunAa::OPT_PERUN_ID_VAR_NAME => $key
        ));
        $this->dataConnector->setServerVars(array(
            $key => $value
        ));
        
        $user = $this->getUserMock();
        $user->expects($this->never())
            ->method('setPerunId');
        
        $this->dataConnector->populateUser($user);
    }


    public function testPopulateUserWithInteger()
    {
        $key = 'perunId';
        $value = 123;
        
        $this->dataConnector->setOptions(array(
            PerunAa::OPT_PERUN_ID_VAR_NAME => $key
        ));
        $this->dataConnector->setServerVars(array(
            $key => $value
        ));
        
        $user = $this->getUserMock();
        $user->expects($this->once())
            ->method('setPerunId')
            ->with($value);
        
        $this->dataConnector->populateUser($user);
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
            'setPerunId'
        ))
            ->getMock();
        return $user;
    }
}