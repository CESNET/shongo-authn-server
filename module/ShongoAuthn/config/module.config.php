<?php
return array(
    
    'user_factory' => array(
        'user_class' => '\ShongoAuthn\User\User'
    ),
    
    'data_connectors' => array(
        
        /*
        'perun-aa' => array(
            'class' => 'ShongoAuthn\User\DataConnector\PerunAa',
            'options' => array(
                'perun_id_var_name' => 'perunUserId',
                'perun_vo_name_var_name' => 'perunVoName'
            )
        ),
        */
        
        'perun-ws' => array(
            'class' => 'ShongoAuthn\User\DataConnector\PerunWs',
            'options' => array(
                'base_url' => 'https://perun-ws.example.org/',
                'client_id' => 'client_id',
                'client_secret' => 'client_secret',
                'users_resource' => 'users',
                'principal_resource' => 'principal',
                'http_client_config' => array(
                    'adapter' => 'Zend\Http\Client\Adapter\Curl',
                    'useragent' => 'Perun Client',
                    'curloptions' => array(
                        /*
                        CURLOPT_SSL_VERIFYPEER => true,
                        CURLOPT_SSL_VERIFYHOST => 2,
                        CURLOPT_CAINFO => '/etc/ssl/certs/ca-bundle.pem'
                        */
                    )
                )
            )
        )
    ),
    
    'user_validators' => array(
        'perun_user' => array(
            'class' => 'ShongoAuthn\User\Validator\PerunUser',
            'options' => array(
                'registration_uri' => 'https://perun.example.org/perun-registrar-fed/'
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
                        'validators' => array()
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