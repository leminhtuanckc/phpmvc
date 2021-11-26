<?php
class Connection
{
    private static $instance = null, $conn = null;
    public function __construct($config)
    {
        // connect database
        try {
            // config dsn
            $dsn = 'mysql::dbname=' . $config['db'] . ';host=' . $config['host'];
            // config options
            /**
             * config utf8
             * configure exception when query fails
             *
             */
            $options = [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];
            $con = new PDO($dsn, $config['user'], $config['pass'], $options);
            self::$conn = $con;
        } catch (Exception $exception) {
            $mess = $exception->getMessage();
            App::$app->loadError('database', ['message' => $mess]);
            die();
        }
    }

    public static function getInstance($config)
    {
        if (self::$instance == null) {
            $connection = new Connection($config);
            self::$instance = self::$conn;
        }
        return self::$instance;
    }
}
