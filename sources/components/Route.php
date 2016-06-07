<?php
namespace Engine\Components;
use Engine\Exceptions\HttpException;

/**
 * Basic routing class
 * Allows controllers get their requests
 */
class Route
{
    private $_defaultController = 'base';
    private $_defaultErrorAction = 'error';
    private $_defaultAction = 'index';
    private $_controllersPath = 'controllers';
    private $_controllersNamespace = 'Engine\\Controllers';
    private $_currentRoute;
    
    /**
     * Class constructor
     * Inits class with configuration
     * @param array $settings configuration
     */
    public function __construct(array $settings) {
        $this->_controllersPath = $settings['controllersPath'] ?? $this->_controllersPath;
        $this->_defaultController = $settings['defaultController'] ?? $this->_defaultController;
        $this->_defaultAction = $settings['defaultAction'] ?? $this->_defaultAction;
        $this->_controllersNamespace = $settings['controllersNamespace'] ?? $this->_controllersNamespace;
        $this->_defaultErrorAction = $settings['defaultErrorAction'] ?? $this->_defaultErrorAction;
    }
    
    /**
     * Begins routing
     * It also handleds HttpExceptions
     */
    public function route() {
        $route = explode('/', strtok($_SERVER['REQUEST_URI'], '?'));
        $controller = empty($route[1]) ? $this->_defaultController : $route[1];
        $action = empty($route[2]) ? $this->_defaultAction : $route[2];
        $this->_currentRoute = array($controller, $action);
        try {
            $this->runRoute($controller, $action);
        } catch (HttpException $e) {
            $this->runRoute(
                $this->_defaultController,
                $this->_defaultErrorAction,
                $e
            );
        }
    }
    
    /**
     * Run single controller/action
     * @param string $controller constroller name
     * @param string $action action name
     * @param mixed $initContent data to init the controller with to
     * @throws HttpException if controller/action not found
     * @throws \Exception if invalid cpntroller given
     */
    public function runRoute($controller, $action, $initContent = '') {
        $controller = ucfirst(strtolower($controller)) . 'Controller';
        $action = 'action' . ucfirst(strtolower($action));
        $path = $this->_controllersPath . '/' . $controller . '.php';
        
        if (!file_exists($path))
            throw new HttpException('Page not found', 404);
        
        require_once($path);
        
        $controller = $this->_controllersNamespace . '\\' . $controller;
        $controllerObject = new $controller($initContent);
        
        if (!$controllerObject instanceof Controller)
            throw new \Exception('Controller should inherit the Engine\\Components\\Controller interface');
        
        if (!method_exists($controllerObject, $action))
            throw new HttpException('Page not found', 404);
        
        $controllerObject->$action($_REQUEST);
    }
    
    /**
     * Returns current route pair (controller/action)
     * @return string route pair
     */
    public function getCurrentRoute() {
        return $this->_currentRoute;
    }

}