<?php
use Zend\Loader\AutoloaderFactory;
require __DIR__ . '/../../../init_autoloader.php';

define('TESTS_ROOT_DIR', __DIR__);

AutoloaderFactory::factory(
    array(
        'Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                'ShongoAuthn' => __DIR__ . '/../src/ShongoAuthn/'
            )
        )
    ));

// ----------
function _dump($value)
{
    error_log(print_r($value, true));
}