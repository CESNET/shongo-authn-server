<?php

return array(
    
    'data_connectors' => array(
    
        'perun-fake' => array(
            'class' => '\ShongoAuthn\User\DataConnector\PerunFake',
            'options' => array(
                'adapter' => array(
                    'driver' => 'Pdo_Mysql',
                    // 'driver' => 'Mysqli',
                    'host' => 'localhost',
                    'dbname' => 'shongo',
                    'username' => 'admin',
                    'password' => 'passwd',
                    'charset' => 'utf8'
                )
            )
        ),
        
        'perun-aa' => array(
            'class' => 'ShongoAuthn\User\DataConnector\PerunAa',
            'options' => array(
                'perun_id_var_name' => 'perunUserId'
            )
        )
    )
);