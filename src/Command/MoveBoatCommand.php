<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MoveBoatCommand
 * @package App\Command
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MoveBoatCommand extends Command
{
    protected static $defaultName = 'app:move-boat';

    protected function execute(InputInterface $input, OutputInterface $output)
    {


        return 1;
    }
}
