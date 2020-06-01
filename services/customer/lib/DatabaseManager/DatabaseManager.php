<?php
namespace Lib\DatabaseManager;

class DatabaseManager
{
    private static $databaseConnection;
    private static $databaseManager;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$databaseManager === null) {
            self::$databaseManager = new DatabaseManager();
            self::connect_mysql();
        }
        return self::$databaseManager;
    }

    public static function getConnection()
    {
        if (self::$databaseConnection === null) {
            self::connect_mysql();
        }
        return self::$databaseConnection;
    }

    private static function connect_mysql()
    {
        self::$databaseConnection = mysqli_connect(
            "localhost:3307",
            "root",
            "milli",
            "broomy_db"
        );
    }

    public function connect_mysql_defaultDB()
    {
        self::$databaseConnection = self::connect_mysql();
        if (!self::$databaseConnection) {
            die('Could not connect: ' . mysqli_error(self::$databaseConnection));
        }
        return self::$databaseConnection;
    }

    public static function mysql_query($query)
    {
        if (self::$databaseConnection === null) {
            self::$databaseConnection = self::getConnection();
        }
        return mysqli_query(self::$databaseConnection, $query);
    }

    public static function mysql_num_rows($result)
    {
        return mysqli_num_rows($result);
    }

    public static function mysql_fetch_array($result)
    {
        return mysqli_fetch_array($result);
    }

    public static function mysql_error()
    {
        if (self::$databaseConnection === null) {
            self::$databaseConnection = self::getConnection();
        }
        return mysqli_error(self::$databaseConnection);
    }
}
