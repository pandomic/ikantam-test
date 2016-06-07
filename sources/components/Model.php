<?php
namespace Engine\Components;

/**
 * Basic model class
 * Hides some database hardcore and lets to make something like active record.
 */
abstract class Model
{
    /**
     * Table primary key
     * @var string 
     */
    protected $primaryKey;
    /**
     * Table name
     * @var string table name
     */
    protected $tableName;
    /**
     * Array where the __get method will save the data
     * @var array
     */
    protected $params = [];
    
    /**
     * Class constructor
     * Allows to load data in model
     * @param array $initData model data
     */
    public function __construct($initData = []) {
        $this->params = $initData;
        if (!empty($initData)) {
            foreach ($initData as $key => $value) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * Get new model
     * @param array $initData model data to init
     * @return \self
     */
    public static function model($initData = []) {
        return new self($initData);
    }
    
    /**
     * Fetch first matched record
     * array $conditions (
     *     ['params' => 'ORDER BY `id` DESC',]
     *     array(
     *         'placeholder' => ':placeholder',
     *         'compare' => '=',
     *         'value' => 'Hello world!',
     *         'column' => 'id',
     *         'operator' => 'OR'
     *     )[, array(...)...]
     * )
     * @param array $conditions conditions array
     */
    public function findFirst(array $conditions) {
        return $this->_prepareDbCondition($conditions, 'firstObject');
        
    }
    
    /**
     * Fetch all matched records
     * @see self::findFirst()
     * @param array $conditions conditions array
     */
    public function findAll(array $conditions) {
        return $this->_prepareDbCondition($conditions, 'allObjects');
    }
    
    /**
     * Save current model to database
     */
    public function save() {
        $clause = '';
        $placeholders = [];
        foreach ($this->params as $key => $value) {
            $clause .= '`' . $key . '`=' . $key ;
            $placeholders[':' . $key] = $value;
        }
        $db = Application::$app->getDb();
        if (!empty($this->params[$this->primaryKey])) {
            $stmt = $db->prepare(
                    'UPDATE `' . $this->tableName . '` SET `'
                    . $clause . '` WHERE `'
                    . $this->primaryKey . '`=`'
                    . $this->params[$this->primaryKey] . '`'
            );
        } else {
            $stmt = $db->prepare(
                    'INSERT INTO `' . $this->tableName . '` (`' . implode('`,`' ,array_keys($this->params)) . '`) VALUES('
                    . implode(',', array_keys($placeholders)) . ')'
            );
        }
        $stmt->execute($placeholders);
    }
    
    /**
     * Remove current model from database
     */
    public function remove() {
        $db = Application::$app->getDb();
        $stmt = $db->prepare('DELETE FROM `' . $this->tableName . '` WHERE `' . $this->primaryKey . '`=:param');
        $stmt->execute(array(':param' => $this->{$this->primaryKey}));
    }
    
    /**
     * Object setter, lets to load vars from database
     * @param string $key key
     * @param mixed $value value
     */
    public function __set($key, $value) {
        $this->params[$key] = $value;
    }
    
    /**
     * Object getter
     * @param type $key
     */
    public function __get($key) {
        return $this->params[$key] ?? NULL;
    }
    
    /**
     * Generate placeholder
     * @return string placeholder
     */
    private function _getPlaceholder() {
        return ':' . str_shuffle('abcdefghijklmnopqrstuvwxyz');
    }
    
    /**
     * Condition preparation hardcore
     * @param array $conditions query conditions
     * @param string $returnType return type
     * @return \Engine\Components\this
     */
    private function _prepareDbCondition(array $conditions, $returnType = 'firstObject') {
        $clause = '';
        $placeholders = [];
        $params = '';
        
        if (!empty($conditions['params'])) {
            $params = $conditions['params'];
            unset($conditions['params']);
        }
        
        if (count($conditions) > 0) {
            foreach ($conditions as $condition) {
                $clause .= '`' . $condition['column'] . '`'
                    . $condition['compare']
                    . $condition['placeholder']
                    . (isset($condition['operator']) ? ' ' . $condition['operator'] . ' ' :'');
                $placeholders[$condition['placeholder']] = $condition['value'];
            }
            $clause = 'WHERE ' . $clause;
        }
        
        $db = Application::$app->getDb();
        $stmt = $db->prepare('SELECT * FROM `' . $this->tableName . '` ' . $clause . ' ' . $params);
        $stmt->execute($placeholders);
        
        switch ($returnType) {
            case 'firstObject':
                return new $this($db->fetchAssoc($stmt));
            break;
            case 'allObjects':
                return $db->fetchObjectAll($stmt);
            break;
        }
    }
}