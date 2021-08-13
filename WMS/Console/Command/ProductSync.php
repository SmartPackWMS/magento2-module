<?php

namespace SmartPack\WMS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use SmartPack\Framework\Product;
use SmartPack\WMSApi\Items;

/**
 * Class SomeCommand
 */
class ProductSync extends Command
{
    const NAME = 'name';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('smartpack:product:sync');
        $this->setDescription('This is my first console command.');
        $this->addOption(
            self::NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Name'
        );

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
        $limit = 50;

        $products = new Product();
        $product_data = $products->getProducts(1, $limit);
        $pages = $product_data->getLastPageNumber();
        $page = 1;

        while ($page <= $pages) {
            $output->writeln('<bg=yellow;options=bold,underscore>Loading page ' . $page . ' of ' . $pages . '</>');

            foreach ($products->getProducts($page, $limit) as $val) {
                $output->writeln('<info>Import product with sku: ' . $val->getSku() . ' | title: ' . $val->getName() . '</info>');
                $itemProduct = new Items();
                $itemProduct->import([
                    "sku" => $val->getSku(),
                    "description" => $val->getName(),
                ]);
            }

            $page++;
        }
    }
}
