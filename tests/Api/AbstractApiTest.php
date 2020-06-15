<?php

namespace Altapay\ApiTest\Api;

use Altapay\Request\Address;
use Altapay\Request\Customer;
use Altapay\Request\OrderLine;
use Altapay\ApiTest\AbstractTest;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

abstract class AbstractApiTest extends AbstractTest
{

    protected function getClient(MockHandler $mock)
    {
        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    protected function getExceptedUri($uri)
    {
        return '/merchant/API/' . $uri;
    }

    /**
     * @return Customer
     */
    protected function getCustomerInfo()
    {
        $billing = new Address();
        $billing->Firstname = 'First name';
        $billing->Lastname = 'Last name';
        $billing->Address = 'my address';
        $billing->City = 'Somewhere';
        $billing->PostalCode = '2000';
        $billing->Region = '0';
        $billing->Country = 'DK';

        $shipping = new Address();
        $shipping->Firstname = 'First name';
        $shipping->Lastname = 'Last name';
        $shipping->Address = 'my address';
        $shipping->City = 'Somewhere';
        $shipping->PostalCode = '2000';
        $shipping->Region = '0';
        $shipping->Country = 'DK';

        $customer = new Customer($billing);
        $customer->setShipping($shipping);
        $customer->setCreatedDate(new \DateTime('2016-11-25'));
        return $customer;
    }

    /**
     * @return OrderLine[]
     */
    protected function getOrderLines()
    {
        $orderlines = [];
        $orderline = new OrderLine('White sugar', 'productid', 1.5, 5.75);
        $orderline->taxPercent = 20;
        $orderline->unitCode = 'kg';
        $orderlines[] = $orderline;

        $orderline = new OrderLine('Brown sugar', 'productid2', 2.5, 8.75);
        $orderline->unitCode = 'kg';
        $orderline->taxPercent = 20;
        $orderlines[] = $orderline;
        return $orderlines;
    }

}
