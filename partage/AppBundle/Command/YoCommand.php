<?php

namespace Bdloc\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class YoCommand extends ContainerAwareCommand
{
    protected function configure(){
        $this
            ->setName('bdloc:yo')
            ->setDescription('Yo some dude')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        //$doctrine = $this->getContainer()->get("doctrine");
        $output->writeln("yo dude.");
        $logger = $this->getContainer()->get("logger");
        $logger->info('Some dude have been yoed');
    }
}