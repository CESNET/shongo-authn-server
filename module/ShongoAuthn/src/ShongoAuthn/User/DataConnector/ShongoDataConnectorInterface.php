<?php

namespace ShongoAuthn\User\DataConnector;

use ShongoAuthn\User\User;
use InoOicServer\User\DataConnector\DataConnectorInterface;


/**
 * Data connector interface specific for Shongo users.
 */
interface ShongoDataConnectorInterface extends DataConnectorInterface
{


    /**
     * Populate a Shongo user entity.
     * @param User $user
     */
    public function populateShongoUser(User $user);
}