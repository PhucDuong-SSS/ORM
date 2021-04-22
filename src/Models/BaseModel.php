<?php

namespace App\Models;

class BaseModel
{
    protected static $table = '';
     /**

     * @var string sql is stored here

     */
    public static $sql = '';
    // public $id = null;
    
    public static function __callStatic($name, $arguments)
    {
        switch ($name) {
            case 'where':
                $where = '';
                if (count($arguments) == 2) { //example  whre('id',2)
                    $where = "$arguments[0] = $arguments[1]";
                } elseif (count($arguments) == 3) { // IN [] query
                    $where = "$arguments[0] $arguments[1] $arguments[2]";
                    if (is_array($arguments[2])) {
                        $where = "$arguments[0] $arguments[1] (";
                        foreach ($arguments[2] as $key => $value) {
                            if ($key > 0) {
                                $where .= ", ";
                            }
                            $where .= "$value";
                        }
                        $where .= ")";
                    }
                }
                $sql = "SELECT * FROM " . static::$table . " WHERE $where";
                $result = new static;
                static::$sql = $sql;
                
                return $result;
                break;       
        }
    }

    /**

     * handles calls to non-existant  methods,

     *  Obj::Where()->Where()->get();

     * @param string $name

     * @param string $arguments

     *

     * @return mixed object

     * @throws \Exception

     */
    
    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'where':
                $where = '';
                if (count($arguments) == 2) {
                    $where = "$arguments[0] = $arguments[1]";
                } elseif (count($arguments) == 3) {
                    $where = "$arguments[0] $arguments[1] $arguments[2]";
                    if (is_array($arguments[2])) {
                        $where = "$arguments[0] $arguments[1] (";
                        foreach ($arguments[2] as $key => $value) {
                            if ($key > 0) {
                                $where .= ", ";
                            }
                            $where .= "$value";
                        }
                        $where .= ")";
                    }
                }
                
                $sql = "SELECT * FROM " . static::$table . " WHERE $where";
                $result = new static;
                static::$sql .= " AND ".$where;
                
                return $result;
                break;
            
            case 'get':
                return $this->getObjectsFromSQL(static::$sql);
                break;         
         
        }
        throw new \Exception(__CLASS__ . ' not such  method[' . $name . ']');
    }
    
    
    /**

     * handle table name

     *  plus s
    
     *

     * @return string 

     */
    protected static function setTableName()
    {
        if (static::$table != '') return;
        $object = new static;
        static::$table = strtolower(static::get_class_name(get_class($object)) . 's');
    }

    protected static function get_class_name($className)
    {
        if ($pos = strrpos($className, '\\')) return substr($className, $pos + 1);
        return $pos;
    }
    /**

     * get all ()
    
     *

     * @return [object]

     */
    public static function all(): array
    {
        $sql = "SELECT * FROM " . static::$table;
        return static::getObjectsFromSQL($sql);
    }
 
    public static function getObjectsFromSQL($sql): array
    {
        $db = Database::getInstance();
        $assoc = json_decode(json_encode($db->fetch_array_sql($sql)), true);

        $result = array();
        foreach ($assoc as $key => $values) {
            $object = new static;
            foreach ($values as $key => $value) {
                $object->$key = $value;
            }
            $result[count($result)] = $object;
        }
        return $result;
    }

    public static function find($id): BaseModel
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . static::$table . " WHERE id = $id LIMIT 1";

        $result = new static;
        $assoc = json_decode(json_encode($db->fetch_array_sql($sql)), true);
        foreach ($assoc[0] as $key => $value) {
            $result->$key = $value;
        }

        return $result;
    }

    public function hasMany($model, $foreign_key, $primary_key = 'id'): array
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . $model::$table . " where $foreign_key = " . $this->$primary_key;
        $assoc = json_decode(json_encode($db->fetch_array_sql($sql)), true);

        $result = array();
        foreach ($assoc as $values) {
            $object = new $model();
            foreach ($values as $key => $value) {
                $object->$key = $value;
            }
            $result[count($result)] = $object;
        }

        return $result;
    }

    public function belongsTo($model, $foreign_key, $primary_key = 'id')
    {
        $result = new $model();
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . $model::$table . " where $primary_key = " . $this->$foreign_key;

        $assoc = json_decode(json_encode($db->fetch_array_sql($sql)), true);

        foreach ($assoc[0] as $key => $value) {
            $result->$key = $value;
        }

        return $result;
    }
}
