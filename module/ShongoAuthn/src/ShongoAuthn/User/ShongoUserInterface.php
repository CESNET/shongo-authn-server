<?php

namespace ShongoAuthn\User;

use InoOicServer\User\UserInterface;


interface ShongoUserInterface extends UserInterface
{


    /**
     * Returns user's Perun ID.
     * 
     * @return integer
     */
    public function getPerunId();
}