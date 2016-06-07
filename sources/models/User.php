<?php
namespace Engine\Models;
use Engine\Components\Model;
/**
 * User model class
 * Declares Signup/Signin and other user management tools
 */
class User extends Model
{
    /**
     * Status codes
     */
    const ERROR_LOGIN_EMPTY = 0x0;
    const ERROR_LOGIN_INVALID = 0x1;
    const LOGIN_VALIDATE_OK = 0x2;
    const REGISTER_EMAIL_EMPTY = 0x3;
    const REGISTER_PASSWORD_EMPTY = 0x4;
    const REGISTER_EMAIL_INVALID = 0x5;
    const REGISTER_PASSWORD_NVALID = 0x6;
    const REGISTER_REPEAT_INVALID = 0x7;
    const REGISTER_EMAIL_EXISTS = 0x8;
    const REGISTER_VALIDATE_OK = 0x9;
    
    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * Table name
     * @var string
     */
    protected $tableName = '{{prefix}}users';
    
    /**
     * Validate sign in input
     * @return int status code
     */
    public function validateLogin() {
        
        if ($this->email == '' || $this->password == '') {
            return self::ERROR_LOGIN_EMPTY;
        } else {
            $model = $this->findFirst(array(
                array(
                    'placeholder' => ':email',
                    'compare' => '=',
                    'value' => $this->email,
                    'column' => 'email'
                )
            ));
            if (!password_verify($this->password, $model->password))
                return self::ERROR_LOGIN_INVALID;
            return self::LOGIN_VALIDATE_OK;
        }
    }
    
    /**
     * Hardcore input filtering
     * @return int validate status
     */
    public function validateRegister() {

        if ($this->email == '')
            return self::REGISTER_EMAIL_EMPTY;
        if ($this->password == '' || $this->repeat == '')
            return self::REGISTER_PASSWORD_EMPTY;
        if (!preg_match('(^[a-zA-Z0-9+&*-]+(?:\.[a-zA-Z0-9_+&*-]+)*@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,7}$)i', $this->email))
            return self::REGISTER_EMAIL_INVALID;
        if (!preg_match('(^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$)i', $this->password))
            return self::REGISTER_PASSWORD_NVALID;
        if ($this->password !== $this->repeat)
            return self::REGISTER_REPEAT_INVALID;
        else {
            $model = $this->findFirst(array(
                array(
                    'placeholder' => ':email',
                    'compare' => '=',
                    'value' => $this->email,
                    'column' => 'email'
                )
            ));
            if ($this->email == $model->email)
                return self::REGISTER_EMAIL_EXISTS;
        }
        return self::REGISTER_VALIDATE_OK;
    }
    
    /**
     * Save current model in database
     * Unsets 'repeat' param, as it is not a part of db schema
     * Generates and stores in model password hash
     */
    public function save() {
        if (isset($this->params['repeat']))
            unset($this->params['repeat']);
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        parent::save();
    }
}