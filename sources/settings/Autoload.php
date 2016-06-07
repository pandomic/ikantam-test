<?php
namespace Engine\Sysutils;
/**
 * Main autoload class
 * Provides psr-4 like class autoloading
 */
final class Autoload
{
    private static $_basePath = __DIR__;
    private static $_classmap = [];
    
    /**
     * Main autoload method, that is called for class loading
     * @param string $classname class to load
     */
    public static function load($classname) {
        $matches = explode('\\',$classname);
        $classname = array_pop($matches);
        $namespace = implode('\\',$matches);
        
        if (in_array($namespace, array_keys(static::$_classmap)))
            $namespace = static::$_classmap[$namespace];
        
        require static::$_basePath . '/' . str_replace('\\', '/', $namespace) . '/' . $classname . '.php';
    }
    
    /**
     * Add classes to classmap
     * @param array $classmap class map
     */
    public static function addClassmap(array $classmap) {
        static::$_classmap = array_merge(static::$_classmap, $classmap);
    }
    
    /**
     * Set autoloader base path
     * @param string $basePath path
     * @throws \Exception if invalid path given
     */
    public static function setBasePath($basePath) {
        if (!is_dir($basePath))
            throw new \Exception('Invalid base path given');
        static::$_basePath = $basePath;
    }
}