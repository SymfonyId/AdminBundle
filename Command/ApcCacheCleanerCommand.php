<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Command;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class ApcCacheCleanerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('siab:cache:clear-apc')
            ->addArgument('host', InputArgument::REQUIRED, 'Server host')
            ->setDescription('Clear apc cache command')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $response = $client->delete(sprintf('%s/apc/cache-clear', $input->getArgument('host')));

        $output->writeln('<info>Try to clearing cache.</info>');
        if (200 === $response->getStatusCode()) {
            $output->writeln('<info>Apc Cache Cleared.</info>');
        }

        $output->writeln('<info>Finish.</info>');
    }
}