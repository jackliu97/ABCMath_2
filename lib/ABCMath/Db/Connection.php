<?php
namespace ABCMath\Db;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Connection
{
    protected static $_config = null;
    protected static $_conn = null;
    protected static $_em = null;

    public static function config(){
        return $connectionParams = array(
                'dbname'    => DB_DATABASE,
                'user'      => DB_USERNAME,
                'password'  => DB_PASSWORD,
                'host'      => DB_HOSTNAME,
                'port'      => DB_PORT,
                'driver'    => 'pdo_mysql',
            );
    }

    public static function getConnection()
    {
        if (!is_null(self::$_conn)) {
            return self::$_conn;
        }

        if (is_null(self::$_config)) {
            self::$_config = new Configuration();
            $connectionParams = self::config();
        }
        self::$_conn = DriverManager::getConnection($connectionParams, self::$_config);

        return self::$_conn;
    }

    public static function getEm()
    {

        if (!is_null(self::$_em)) {
            return self::$_em;
        }

        $isDevMode = true;
        //$paths = array(__DIR__ . '/mappings/entities');
        //$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

        $paths = array(__DIR__ . '/mappings/xml');
        $config = Setup::createXMLMetadataConfiguration($paths, $isDevMode);
        self::$_em = EntityManager::create(self::config(), $config);

        return self::$_em;
    }
}
