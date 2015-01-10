<?php
namespace ABCMath;

use ABCMath\Db\Connection;
abstract class Base
{
    protected $_conn;
    protected $_db_name;
    public function __construct()
    {
        $this->_db_name = ADMIN_DB;
        $this->conn();
    }

    /**
     * sets singleton database connection
     */
    protected function conn()
    {
        $this->_conn = Connection::getConnection();
    }

    public function get_data()
    {
        return get_object_vars($this);
    }

    /**
     * converts this object into array.
     */
    public function export()
    {
        $array = get_object_vars($this);

        return $array;
    }

    /**
     * logs errors and stuff.
     */
    public function log($message)
    {
        if (defined('UNITTEST') && UNITTEST === true) {
            print("$message\n");
        }
    }
}
