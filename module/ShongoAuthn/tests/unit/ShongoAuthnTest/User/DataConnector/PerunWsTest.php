<?php

namespace ShongoAuthnTest\User\DataConnector;

use ShongoAuthn\User\DataConnector\PerunWs;


class PerunWsTest extends \PHPUnit_Framework_TestCase
{

    protected $dataConnector;


    public function setUp()
    {
        $this->dataConnector = new PerunWs();
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