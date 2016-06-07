<?php
namespace Engine\Components;
use Engine\Exceptions\DatabaseException;

/**
 * MySQL/PDO Database wrapper class
 * Helps to hide connection settings and to abstract some methods
 */
class Database
{
    /**
     * PDO const wrapper
     */
    const FETCH_OBJ = \PDO::FETCH_OBJ;
    
    /**
     * Current database instance
     * @var \Engine\Components\Database
     */
    public static $db;
    
    private $_dsn;
    private $_user;
    private $_password;
    private $_db;
    private $_prefix = 'app_';
    
    /**
     * Class constructor
     * Lets init database with connection settings
     * @param array $config array with configuration
     * @throws \Engine\Exceptions\DatabaseException on db error
     */
    public function __construct(array $config) {
        if (empty($config['dsn']) || empty($config['user']))
            throw new \DatabaseException('Database cnn string and username are missed');
        $this->_dsn = $config['dsn'];
        $this->_user = $config['user'];
        $this->_password = $config['password'] ?? '';
        $this->_prefix = $config['prefix'] ?? $this->_prefix;
        
        try {
            $this->_db = new \PDO($this->_dsn, $this->_user, $this->_password);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        
        static::$db = $this;
    }
    
    /**
     * PDO::prepare wrapper
     * Lets to use table prefixes
     * @param string $string query string
     * @return \PDOStatement
     */
    public function prepare($string) {
        return $this->pdo()->prepare(str_replace('{{prefix}}', $this->_prefix, $string));
    }
    
    /**
     * PDO::execute wrapper
     * @param \PDOStatement $statement statement prepared with prepare() method
     * @param array $params placeholders
     * @return mixed
     */
    public function execute(\PDOStatement $statement, $params) {
        return $statement->execute($params);
    }
    
    /**
     * Get PDO object
     * @return \PDO
     */
    public function pdo() {
        return $this->_db;
    }
    
    /**
     * Get furst result as object
     * @param \PDOStatement $statement PDO statement
     * @return mixed
     */
    public function fetchObject(\PDOStatement $statement) {
        return $statement->fetch(\PDO::FETCH_OBJ);
    }
    
    /**
     * Get first result as associative array
     * @param \PDOStatement $statement
     * @return array result
     */
    public function fetchAssoc(\PDOStatement $statement) {
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all results as associative array
     * @param \PDOStatement $statement
     * @return array result
     */
    public function fetchAssocAll(\PDOStatement $statement) {
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all result as array of objects
     * @param \PDOStatement $statement PDO statement
     * @return array results
     */
    public function fetchObjectAll(\PDOStatement $statement) {
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }
    
    /**
     * Get row count in result
     * @param \PDOStatement $statement
     * @return int row count
     */
    public function rowCount(\PDOStatement $statement) {
        return $statement->rowCount();
    }
    
    public function __clone() {}
    public function __set($key, $value) {}
    public function __get($key) {}
    
}