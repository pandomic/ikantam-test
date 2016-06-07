<?php
namespace Engine\Components;

/**
 * Access control basic class.
 * The class may be inherited and extended.
 */
class AccessControl
{
    
    /**
     * Class constructor
     * @param array $settings init settings
     */
    public function __construct($settings = []) {
        $this->_loadCookie();
    }
    
    /**
     * Check if user is authenticated.
     * @return boolean is authenticated
     */
    public function getAccess() {
        return Application::$app->getRequest()->session('logged');
    }
    
    /**
     * Get current user id
     * @return mixed user id
     */
    public function getUserid() {
        return Application::$app->getRequest()->session('userid');
    }
    
    /**
     * Login helper. Lets controllers to authenticate user
     * @param string $email user email
     * @param string $password user password
     * @return boolean login successfull
     */
    public function login($email, $password) {
        $db = Application::$app->getDb();
        $stmt = $db->prepare("SELECT `id`, `password` FROM {{prefix}}users WHERE `email`=:email");
        $stmt->execute(array(':email' => $email));
        $user = $db->fetchObject($stmt);
        
        if (
            $email != ''
            && $password != ''
            && password_verify($password, $user->password)
        ) {
            $token = password_hash($_SERVER['REMOTE_ADDR'] . $user->id, PASSWORD_DEFAULT);
            Application::$app->getRequest()
                ->setSession('logged',true)
                ->setSession('userid',$user->id)
                ->clearCookie()
                ->addCookie(array(
                    'userid' => $user->id,
                    'auth_token' => $token
                ))
                ->saveCookie();
            
            $stmt = $db->prepare("UPDATE {{prefix}}users SET `token`=:token WHERE `email`=:email");
            $stmt->execute(array(':email' => $email, ':token' => $token));
            
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Let the users log out
     * @return void
     */
    public function logout() {
        if ($this->getAccess()) {
            Application::$app->getRequest()
                ->unsetSession('logged')
                ->unsetSession('userid')
                ->clearCookie();
        }
    }
    
    /**
     * Cookie-based authentication
     */
    private function _loadCookie() {
        $token = Application::$app->getRequest()->cookie('auth_token');
        $userid = Application::$app->getRequest()->cookie('userid');
        if (!empty($token) && !empty($userid) && !$this->getAccess()) {
            if (password_verify($_SERVER['REMOTE_ADDR'] . $userid, $token)) {
                $db = Application::$app->getDb();
                $stmt = $db->prepare("SELECT `id` FROM {{prefix}}users WHERE `token`=:token");
                $stmt->execute(array(':token' => $token));
                $findToken = $stmt->rowCount();
                
                if ($findToken === 1) {
                    Application::$app->getRequest()
                        ->setSession('logged',true);
                }
            }
        }
    }
    
    
}