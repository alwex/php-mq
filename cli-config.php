<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 21/07/16
 * Time: 7:25 PM
 */

// cli-config.php
$configuration = \PhpMQ\Configuration::load();
$entityManager = $configuration->getEntityManager();
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);