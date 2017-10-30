<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 30/10/2017
 * Time: 22:53
 */

namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('app:dump')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}