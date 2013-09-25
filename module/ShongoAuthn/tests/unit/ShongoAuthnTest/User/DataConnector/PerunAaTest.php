<?php

namespace ShongoAuthnTest\User\DataConnector;

use ShongoAuthn\User\DataConnector\PerunAa;


class PerunAaTest extends \PHPUnit_Framework_Testcase
{

    protected $dataConnector;


    public function setUp()
    {
        $this->dataConnector = new PerunAa();
        $this->dataConnector->setOptions(
            array(
                PerunAa::OPT_PERUN_ID_VAR_NAME => 'perunId',
                PerunAa::OPT_PERUN_VO_NAME_VAR_NAME => 'voName'
            ));
    }


    public function testPopulateUserWithMissingOption()
    {
        $this->setExpectedException('ShongoAuthn\User\DataConnector\Exception\MissingOptionException');
        
        $this->dataConnector->setOptions(array());
        $user = $this->getUserMock();
        $this->dataConnector->populateUser($user);
    }


    public function testPopulateUserWithMissingValue()
    {
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
        
        $this->dataConnector->setServerVars(array(
            $key => $value
        ));
        
        $user = $this->getUserMock();
        $user->expects($this->once())
            ->method('setPerunId')
            ->with($value);
        
        $this->dataConnector->populateUser($user);
    }


    public function testPopulateUserWithVoName()
    {
        $key = 'voName';
        $value = 'foo;bar';
        
        $vos = array(
            'foo',
            'bar'
        );
        
        $this->dataConnector->setServerVars(array(
            $key => $value
        ));
        
        $user = $this->getUserMock();
        $user->expects($this->once())
            ->method('setPerunVos')
            ->with($vos);
        
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
            'setPerunId',
            'setPerunVos'
        ))
            ->getMock();
        return $user;
    }
}