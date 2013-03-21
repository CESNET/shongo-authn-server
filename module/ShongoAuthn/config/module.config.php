<?php

return array(
    
    'user_factory' => array(
        'user_class' => '\ShongoAuthn\User\User'
    ), 
    
    'data_connectors' => array(
        'perun' => array(
            'class' => '\ShongoAuthn\User\DataConnector\PerunFake', 
            'options' => array(
                'adapter' => array(
                    'driver' => 'Pdo_Mysql', 
                    'host' => 'localhost', 
                    'dbname' => 'shongo', 
                    'username' => 'user', 
                    'password' => 'passwd'
                )
            )
        )
    )
);