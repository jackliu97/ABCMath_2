<?php
// populate.php
require_once(__DIR__ . DIRECTORY_SEPARATOR . "..".DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR."Autoload.php");
use ABCMath\Command\Vocabulary\PopulateDefinition,
	Symfony\Component\Console\Application;

$application = new Application();
$application->add(new PopulateDefinition);
$application->run();