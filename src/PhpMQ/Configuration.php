<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 12:57 PM
 */

namespace PhpMQ;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    /**
     * @var Yaml
     */
    private $params;

    /**
     * @var EntityManager
     */
    private $entityManager;


    public static function load($configurationFile)
    {
        return new self($configurationFile);
    }

    private function __construct($configurationFile)
    {
        if (file_exists($configurationFile)) {
            $params = Yaml::parse(file_get_contents($configurationFile));
            // doctrine configuration
            $isDev = true;
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../Repository"), $isDev);

            // database configuration parameters
            $conn = array(
                'driver' => 'pdo_sqlite',
                'path' => __DIR__.'/db.sqlite',
                'uesr' => '',
                'password' => '',
                'dbname' => '',
            );

            // obtaining the entity manager
            $this->entityManager = $entityManager = EntityManager::create($conn, $config);
        }
    }

    /**
     * @return Yaml
     */
    public function getParams()
    {
        return $this->params;
    }
}