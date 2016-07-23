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
    const CONFIG_DIR = '.phpmq';
    const CONFIG_FILE = 'configuration.yml';

    /**
     * @var Yaml
     */
    private $params;

    /**
     * @var EntityManager
     */
    private $entityManager;


    public static function load()
    {
        return new self();
    }

    public static function create($params)
    {

        if (file_exists(self::CONFIG_DIR.'/'.self::CONFIG_FILE)
            || is_dir(self::CONFIG_DIR)
        ) {
            throw new \RuntimeException("configuration file already exists !!!");
        }

        mkdir(self::CONFIG_DIR);
        $yamlString = Yaml::dump($params);

        file_put_contents(self::CONFIG_DIR.'/'.self::CONFIG_FILE, $yamlString);
    }

    private function __construct()
    {
        if (file_exists(self::CONFIG_DIR.'/'.self::CONFIG_FILE)) {

            $this->params = Yaml::parse(file_get_contents(self::CONFIG_DIR.'/'.self::CONFIG_FILE));

            // doctrine configuration
            $isDev = true;
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Repository"), $isDev);

            $connection = $this->params['db'];

            // obtaining the entity manager
            $this->entityManager = $entityManager = EntityManager::create($connection, $config);
        }
    }

    /**
     * @return Yaml
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}