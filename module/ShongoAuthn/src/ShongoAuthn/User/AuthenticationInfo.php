<?php

namespace ShongoAuthn\User;


class AuthenticationInfo
{

    /**
     * @var string
     */
    protected $provider;

    /**
     * @var string
     */
    protected $instant;

    /**
     * @var integer
     */
    protected $loa;


    /**
     * Constructor.
     * 
     * @param string $provider
     * @param string $instant
     */
    public function __construct($provider, $instant, $loa = 0)
    {
        $this->setProvider($provider);
        $this->setInstant($instant);
        $this->setLoa($loa);
    }


    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }


    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }


    /**
     * @return string
     */
    public function getInstant()
    {
        return $this->instant;
    }


    /**
     * @param string $instant
     */
    public function setInstant($instant)
    {
        $this->instant = $instant;
    }


    /**
     * @return number
     */
    public function getLoa()
    {
        return $this->loa;
    }


    /**
     * @param number $loa
     */
    public function setLoa($loa)
    {
        $this->loa = $loa;
    }
}