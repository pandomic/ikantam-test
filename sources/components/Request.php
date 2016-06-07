<?php
namespace Engine\Components;

/**
 * Basic request class
 * Provides some basic api accessing the request data
 */
class Request
{
    /**
     * Current request instance
     * @var \Engine\Components\Request
     */
    public static $request;
    
    private $_cookieTime = '1209600';
    private $_csrfToken = 'token';
    private $_csrfName = '_CSRF';
    private $_cookieName = 'cookie';
    private $_cookie = [];
    
    /**
     * Class constructor
     * Inits class with some settings
     * @param array $settings settings
     */
    public function __construct($settings) {
        $this->_cookieTime = $settings['cookieTime'] ?? $this->_cookieTime;
        $this->_cookieName = $settings['cookieName'] ?? $this->_cookieName;
        $this->_csrfToken = $settings['csrfToken'] ?? $this->_csrfToken;
        $this->_csrfName = $settings['csrfName'] ?? $this->_csrfName;
        if (isset($_COOKIE[$this->_cookieName]))
            $this->_cookie = unserialize($_COOKIE[$this->_cookieName]);
        static::$request = $this;
    }
    
    /**
     * Get param from cookie
     * @param string $param key
     * @return mixed cookie value
     */
    public function cookie($param) {
        return $this->_cookie[$param] ?? NULL;
    }
    
    /**
     * Get value from session
     * @param string $param key
     * @return mixed session value
     */
    public function session($param) {
        return $_SESSION[$param] ?? NULL;
    }
    
    /**
     * Get value from POST
     * @param string $param key
     * @return mixed POST value
     */
    public function post($param) {
        return $_POST[$param] ?? NULL;
    }
    
    /**
     * Get value from GET
     * @param string $param key
     * @return mixed GET value
     */
    public function get($param) {
        return $_GET[$param] ?? NULL;
    }
    
    /**
     * Add data to cookie
     * @param array $params cookie params
     * @return \Engine\Components\Request
     */
    public function addCookie(array $params) {
        $this->_cookie = array_merge_recursive($this->_cookie, $params);
        return $this;
    }
    
    /**
     * Clear cookie
     * @return \Engine\Components\Request
     */
    public function clearCookie() {
        setcookie($this->_cookieName, '', time()-3600, '/');
        return $this;
    }
    
    /**
     * Save cookie
     */
    public function saveCookie() {
        setcookie(
            $this->_cookieName, 
            serialize($this->_cookie),
            time() + $this->_cookieTime,
            '/'
        );
    }
    
    /**
     * Get value from FILES
     * @param string $name key
     * @return mixed FILES value
     */
    public function file($name) {
        return $_FILES[$name] ?? NULL;
    }
    
    /**
     * Set session value
     * @param string $name session key
     * @param mixed $value value to set
     * @return \Engine\Components\Request
     */
    public function setSession($name, $value) {
        $_SESSION[$name] = $value;
        return $this;
    }
    
    /**
     * Add flash message
     * @param string $flashName flash name
     * @param string $flashText flash content
     */
    public function setFlash($flashName, $flashText) {
        $this->setSession('flash\\' . $flashName, $flashText);
    }
    
    /**
     * Get flash message
     * @param string $flashName flash name
     * @return string flash data
     */
    public function getFlash($flashName) {
        $flash = $this->session('flash\\' . $flashName) ?? FALSE;
        $this->unsetSession('flash\\' . $flashName);
        return $flash;
    }
    
    /**
     * Unset session by key
     * @param string $name key
     * @return \Engine\Components\Request
     */
    public function unsetSession($name) {
        if (isset($_SESSION[$name]))
            unset($_SESSION[$name]);
        return $this;
    }
    
    /**
     * Refresh the page
     */
    public function refresh() {
        header("Refresh:0");
    }
    
    /**
     * Redirects to $url
     * @param string $url url to redirect to
     */
    public function redirect($url) {
        header("Refresh:0; url=$url");
    }
    
    /**
     * Get CSRF data
     * @return array CSRF data
     */
    public function getCSRF() {
        return array(
            $this->_csrfName,
            $this->_csrfToken
        );
    }

    /**
     * Check if POST request
     * @return boolean is POST request
     */
    public function isPostRequest() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
    
    /**
     * CSRF validation
     * @return boolean validation passed or failed
     */
    public function checkCSRF() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (
                !empty($this->post($this->_csrfName))
                && $this->post($this->_csrfName) === $this->_csrfToken
            ) {
                return true;
            }
        }
        return false;
    }
        
 
}