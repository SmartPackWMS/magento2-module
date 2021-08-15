<?php

namespace SmartPack\WMS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use SmartPack\Framework\Order;

/**
 * Class SomeCommand
 */
class OrderSync extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('smartpack:order:sync');
        $this->setDescription('Sync all orders to SmartPack WMS');

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Success Message.</info>');

        $orders = new Order();

        foreach ($orders->getOrders() as $order) {
            echo "OrderID: " . $order->getId() . "\n";
            echo "Status: " . $order->getStatus() . "\n";
            echo "State: " . $order->getState() . "\n";
        }
    }
}
