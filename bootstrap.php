<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 21/07/16
 * Time: 7:24 PM
 */
require_once "vendor/autoload.php";

// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src/PhpMQ/Repository"), $isDevMode);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__.'/db.sqlite',
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);