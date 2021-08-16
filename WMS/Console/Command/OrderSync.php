<?php

namespace SmartPack\WMS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use SmartPack\Framework\Order;
use SmartPack\WMSApi\Shipments;

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

            $order_lines = [];
            $orderItems = $order->getAllItems();
            foreach ($orderItems as $item) {
                $order_lines[] = [
                    'qty' => (int) $item->getQtyOrdered(),
                    'sku' => $item->getSku()
                ];
            }

            $shipment_address = $order->getShippingAddress()->getData();

            $shipment = new Shipments();
            $shipment_data = [
                'orderNo' => (string) $order->getId(),
                'referenceNo' => (string) $order->getId(),
                'uniqueReferenceNo' => (string) $order->getId(),
                'description' => '',
                'printDeliveryNote' => true,
                'sender' => [
                    'name' => 'Warehouse ABC',
                    'street1' => 'Address 23',
                    'zipcode' => '5432',
                    'city' => 'Treeville',
                    'country' => 'DK',
                    'phone' => '+4588888888',
                    'email' => 'contact@company.com',
                ],
                'recipient' => [
                    'name' => $shipment_address['firstname'] . ' ' . $shipment_address['middlename'] . ' ' . $shipment_address['lastname'],
                    'attention' => '',
                    'street1' => $shipment_address['street'],
                    'zipcode' => $shipment_address['postcode'],
                    'city' => $shipment_address['city'],
                    'country' =>  $shipment_address['country_id'],
                    'phone' => $shipment_address['telephone'],
                    'email' => $shipment_address['email'],
                ],
                "deliveryMethod" => 'custom_pickup',
                "droppointId" => '5743321',
                "items" => $order_lines
            ];
            $response = $shipment->create($shipment_data);

            if ($response->getStatusCode() === 200) {
                $orders->changeWmsState('demo', $order);
            }
        }
    }
}
