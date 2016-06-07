<?php
namespace Engine\Components;

/**
 * Application class
 * This is basic factory for all components.
 */
class Application
{
    /**
     * Current application instance
     * @var Engine\Components\Application 
     */
    public static $app;
    
    private $_database;
    private $_route;
    private $_request;
    private $_acc;
    private $_settings = [];
    private $_routeClass = 'Route';
    private $_dbClass = 'Database';
    private $_accClass = 'AccessControl';
    private $_controller;
    
    /**
     * Class constructor
     * @param array $settings
     */
    public function __construct(array $settings) {
        static::$app = $this;
        $this->_settings = $settings;
        if (!empty($settings['application']['routeClass']))
            $this->_routeClass = $settings['application']['routeClass'];
    }
    
    /**
     * Run application
     * @return void
     * @throws \Exception if components are not the correct instances
     */
    public function run() {
        header('Content-type:text/html; charset=utf-8');
        session_start();
        
        $dbClass = $this->_settings['application']['dbClass'] ?? $this->_dbClass;
        $routeClass = $this->_settings['application']['routeClass'] ?? $this->_routeClass;
        $accClass = $this->_settings['application']['accClass'] ?? $this->_accClass;
        $requestClass = $this->_settings['application']['requestClass'] ?? $this->_request;
        
        $this->_database = new $dbClass($this->_settings['db'] ?? []);
        if (!$this->_database instanceof Database)
            throw new \Exception('Database class should be an instance of Engine\\Components\\Database');
        
        $this->_route = new $routeClass(empty($this->_settings['route']) ? [] : $this->_settings['route']);
        if (!$this->_route instanceof Route)
            throw new \Exception('Route class should be an instance of Engine\\Components\\Route');
        
        $this->_request = new $requestClass($this->_settings['request'] ?? []);
        if (!$this->_request instanceof Request)
            throw new \Exception('Request class should be an instance of Engine\\Components\\Request');
        
        $this->_acc = new $accClass($this->_settings['accessControl'] ?? []);
        if (!$this->_acc instanceof AccessControl)
            throw new \Exception('Access control class should be an instance of Engine\\Components\\AccessControl');
        
        $this->_route->route();
    }
    
    /**
     * Set current controller
     * @param \Engine\Components\Controller $controller controller instance
     */
    public function setController(Controller $controller) {
        $this->_controller = $controller;
    }
    
    /**
     * Get current controller
     * @return \Engine\Components\Controller controller instance
     */
    public function getController() {
        return $this->_controller;
    }
    
    /**
     * Returns view settings
     * @return mixed settings
     */
    public function viewSettings() {
        return empty($this->_settings['view']) ? [] : $this->_settings['view'];
    }
    
    /**
     * Returns application settings
     * @return mixed settings
     */
    public function appSettings() {
        return empty($this->_settings['application']) ? [] : $this->_settings['application'];
    }
    
    /**
     * Returns route object
     * @return \Engine\Components\Route route
     */
    public function getRoute() {
        return $this->_route;
    }
    
    /**
     * Returns database wrapper object
     * @return \Engine\Components\Database db
     */
    public function getDb() {
        return $this->_database;
    }
    
    /**
     * Returns request wrapper
     * @return \Engine\Components\Request request
     */
    public function getRequest() {
        return $this->_request;
    }
    
    /**
     * Returns access control object
     * @return type \Engine\Components\AccessControl acc object
     */
    public function getAcc() {
        return $this->_acc;
    }
}