<?php

namespace App\Models;

require 'config.php';

use PDO;
use PDOException;

class Database
{
    protected static $sql;
    protected static $result;
    protected $row;
    private static $conn = null;
    private static $database = null;


     /**

     * set the db connection 

     * params are as new PDO(...)

     * set PDO to throw exceptions on error

     *

     * @param string DB_TYPE_CONNECTION

     * @param string DB_HOST

     * @param string $DB_DATABASE

     * @param string $DB_USERNAME

     * @param string DB_PASSWORD
     
     *

     * @throws \Exception

     */
    public function __construct()
    {
        try {
            self::$conn = new PDO(DB_TYPE_CONNECTION . ":host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
            // set the PDO error mode to exception
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

     /**

     * get instance
    
     *

     * @return object(App\Models\Database)

     */

    public static function getInstance(): Database
    {
        if (self::$database === null) {
            self::$database = new Database();
        }
        return self::$database;
    }

    public static function setSQL($sql)
    {
        self::$sql = $sql;
    }

    public static function query()
    {
        self::$result = self::$conn->query(self::$sql);
    }

    public static function querySQL($sql)
    {
        self::$sql = $sql;
        self::query();
    }

    public static function fetch_array()
    {
        self::query();
        return self::$result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function fetch_array_sql($sql)
    {
        self::querySQL($sql);
        return self::fetch_array();
    }

    public static function db_close()
    {
        mysqli_close(self::$conn);
    }

    public static function countNumRows()
    {
        self::query();
        return self::$result->rowCount();
    }

    public static function countNumRowsSQL($sql)
    {
        self::querySQL($sql);
        return self::$result->rowCount();
    }
}
