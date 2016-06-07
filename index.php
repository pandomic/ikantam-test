<?php
/**
 * Ikantam test application
 * @author Vlad Gramuzov <vlad.gramuzov@gmail.com>
 */

// Load base application class
require_once(__DIR__ . "/sources/components/Application.php");
// Create and init with configuration new app instance
$app = new Engine\Components\Application(
    require(__DIR__ . "/sources/settings/settings.php")
);
// Run application
$app->run();