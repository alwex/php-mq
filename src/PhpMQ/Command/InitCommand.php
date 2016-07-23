<?php
/**
 * Created by PhpStorm.
 * User: aguidet
 * Date: 23/07/16
 * Time: 12:33 PM
 */

namespace PhpMQ\Command;


use PhpMQ\Configuration;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class InitCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('mq:init')
            ->setDescription('Initialise the MQ system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $drivers = pdo_drivers();

        /** @var $questions QuestionHelper */
        $questions = $this->getHelperSet()->get('question');


        $driverQuestion = new ChoiceQuestion("Please chose your pdo driver", $drivers);
        $driver = $questions->ask($input, $output, $driverQuestion);

        $dbNameQuestion = new Question("Please enter the database name (or the database file location): ", "~");
        $dbName = $questions->ask($input, $output, $dbNameQuestion);

        $dbHostQuestion = new Question("Please enter the database host (if needed): ", "~");
        $dbHost = $questions->ask($input, $output, $dbHostQuestion);

        $dbPortQuestion = new Question("Please enter the database port (if needed): ", "~");
        $dbPort = $questions->ask($input, $output, $dbPortQuestion);

        $dbUserNameQuestion = new Question("Please enter the database user name (if needed): ", "~");
        $dbUserName = $questions->ask($input, $output, $dbUserNameQuestion);

        $dbUserPasswordQuestion = new Question("Please enter the database user password (if needed): ", "~");
        $dbUserPassword = $questions->ask($input, $output, $dbUserPasswordQuestion);

        $confirmationQuestion = new ConfirmationQuestion("are you sure ? <info>(yes/no, default no)</info>: ", false);
        $confirmation = $questions->ask($input, $output, $confirmationQuestion);

        $params = array(
            'db' => array(
                'driver' => 'pdo_'.$driver,
                'path' => $dbName,
                'user' => $dbUserName,
                'password' => $dbUserPassword,
                'dbname' => $dbName,
                'port' => $dbPort,
                'host' => $dbHost,
            ),
        );

        Configuration::create($params);
    }
}