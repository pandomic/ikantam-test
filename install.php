<?php
namespace Engine\Sysutils;
/**
 * Installation helper class
 */
final class Installator
{
    // Place the database host here
    protected static $dbHost = 'localhost';
    // Place the database username here
    protected static $dbUser = 'root';
    // Database password
    protected static $dbPassword = '';
    // Database name
    protected static $dbName = 'test';
    // Database prefix
    protected static $dbPrefix = 'app_';
    
    public static function testDependencies() {
        echo("Checking dependencies...\n");
        if (!(version_compare(PHP_VERSION, '7.0.0') >= 0)) {
            die('PHP version 7.0.0+ required!');
        }
        if (!defined('PDO::ATTR_DRIVER_NAME')) {
            die('PDO need to be avaliable!');
        }
        echo("Dependencies ok\n");
    }
    
    /**
     * Buld settings file
     */
    public static function buildSettingsFile() {
        echo("Building settings file...\n");
        $file = __DIR__ . '/sources/settings/settings.install.php';
        if (!file_exists($file))
            die('Could not find settings.install.php');
        $content = file_get_contents($file);
        $content = str_replace(array(
            '{{dbname}}',
            '{{dbhost}}',
            '{{dbuser}}',
            '{{dbpassword}}',
            '{{dbprefix}}'
        ), array(
            static::$dbName,
            static::$dbHost,
            static::$dbUser,
            static::$dbPassword,
            static::$dbPrefix
        ), $content);
        file_put_contents(
            __DIR__ . '/sources/settings/settings.php', 
            $content
        );
        echo("Settings file ok\n");
    }
    
    /**
     * Build database schema
     */
    public static function buildDatabaseSchema() {
        echo("Building database schema...\n");
        $file = __DIR__ . '/sources/settings/schema.sql';
        if (!file_exists($file))
            die('Could not find schema.sql');
        $content = file_get_contents($file);
        $content = str_replace('{{prefix}}', static::$dbPrefix, $content);
        try {
            $db = new \PDO(
                'mysql:dbname=' . static::$dbName . ';host=' . static::$dbHost, 
                static::$dbUser, static::$dbPassword);
            $stmt = $db->prepare($content);
            $stmt->execute();
        } catch (\PDOException $e) {
            die('Database access error. Check connection parameters');
        }
        echo("Schema ok\n");
    }
    
    /**
     * Complete installation
     */
    public static function completeInstallation() {
        echo('Installation completed. Visit the site index');
    }
}
header('Content-type:text/plain; charset=utf-8');
Installator::testDependencies();
Installator::buildSettingsFile();
Installator::buildDatabaseSchema();
Installator::completeInstallation();