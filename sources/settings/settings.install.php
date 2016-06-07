<?php
// Load autoloader
require_once(__DIR__ . '/autoload.php');

// Define global variables
define('SRC_PATH', dirname(__DIR__));
define('UPLOADS_DIR', realpath(dirname(__DIR__) . '/..') . '/uploads');
define('VIEWS_PATH', realpath(dirname(__DIR__) . '/..') . '/views');
define('LAYOUTS_PATH', realpath(dirname(__DIR__) . '/..') . '/layouts');
define('ASSETS_PATH', '/assets');

// Init and register autoloader
Engine\Sysutils\Autoload::setBasePath(SRC_PATH);
Engine\Sysutils\Autoload::addClassmap(require(__DIR__ . '/classmap.php'));
spl_autoload_register('\\ENGINE\\Sysutils\\Autoload::load');

// Init components with some settings
return array(
    'application' => array(
        'routeClass' => 'Engine\\Components\\Route',
        'dbClass' => 'Engine\\Components\\Database',
        'requestClass' => 'Engine\\Components\\Request',
        'accClass' => 'Engine\\Components\\AccessControl'
    ),
    'route' => array(
        'controllersPath' => SRC_PATH . '/controllers',
        'defaultController' => 'base',
        'defaultAction' => 'index',
        'defaultErrorAction' => 'error',
        'controllersNamespace' => 'Engine\\Controllers'
    ),
    'view' => array(
        'layoutsPath' => LAYOUTS_PATH,
        'viewsPath' => VIEWS_PATH,
        'baseLayout' => 'main.php'
    ),
    'db' => array(
        'dsn' => 'mysql:dbname={{dbname}};host={{dbhost}}',
        'user' => '{{dbuser}}',
        'password' => '{{dbpassword}}',
        'prefix' => '{{dbprefix}}'
    ),
    'request' => array(
        'cookieTime' => '1209600',
        'cookieName' => 'ikantam_test',
        'csrfToken' => 'CSRF_asd5f8sdf64g453f5sd35f4553sdf5',
        'csrfName' => 'ikantam_CSRF'
    ),
    'accessControl' => array()
);