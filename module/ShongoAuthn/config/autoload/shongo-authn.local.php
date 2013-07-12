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
                    'dbname' => 'shongo_devel',
                    'username' => 'admin',
                    'password' => 'passwd',
                    'charset' => 'utf8'
                )
            )
        )
    ),
    
    'user_info_mapper' => array(
        'class' => '\ShongoAuthn\User\UserInfo\Mapper\Shongo'
    ),
    
    'authentication_handlers' => array(
        
        'shibboleth' => array(
            'options' => array(
                'attribute_filter' => array(
                    'REMOTE_USER' => array(
                        'name' => 'eppn',
                        'required' => true,
                        'validators' => array(
/*
 'email' => array(
 'name' => 'email_address'
 )
*/
)
                    ),
                    'mail' => array(
                        'name' => 'mail',
                        'required' => true,
                        'filters' => array(
                            'shibboleth_serialized_value' => array(
                                'name' => 'shibboleth_serialized_value',
                                'options' => array()
                            )
                        ),
                        'validators' => array(
                            'email' => array(
                                'name' => 'email_address'
                            )
                        )
                    ),
                    'givenName' => array(
                        'name' => 'givenName',
                        'required' => true
                    ),
                    'sn' => array(
                        'name' => 'sn',
                        'required' => true
                    )
                )
            )
        )
    )
);