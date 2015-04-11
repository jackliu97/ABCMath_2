<?php
require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."Autoload.php";

use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use ABCMath\Db\Connection;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\DialogHelper;

use Doctrine\Common\ClassLoader;

$helperSet = new HelperSet(array(
    'db' => new ConnectionHelper(Connection::getConnection()),
    'em' => new EntityManagerHelper(Connection::getEm()),
    'dialog' => new DialogHelper(),
));

$cli = new Application('Doctrine Command Line Interface', \Doctrine\ORM\Version::VERSION);
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);


ConsoleRunner::addCommands($cli);
$cli->run();

