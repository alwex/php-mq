<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 1:05 PM
 */

namespace PhpMQ\Command;


use PhpMQ\Configuration;
use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractCommand
 * @package PhpMQ\Command
 */
abstract class AbstractCommand extends Command
{

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration, $name = null)
    {
        parent::__construct($name);
        $this->configuration = $configuration;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}