<?php

namespace Altapay\ApiTest\Request;

use Altapay\ApiTest\AbstractTest;
use Altapay\Exceptions\Exception;
use Altapay\Request\Address;
use Altapay\Request\Customer;

class CustomerTest extends AbstractTest
{
    public function test_customer(): void
    {
        $billingAddress  = new Address();
        $shippingAddress = new Address();

        $customer = new Customer($billingAddress);
        $customer->setShipping($shippingAddress);
        $customer->setOrganisationNumber('123');
        $customer->setPersonalIdentifyNumber('20304050');
        $customer->setGender('f');
        $serialized = $customer->serialize();

        $this->assertArrayHasKey('organisationNumber', $serialized);
        $this->assertArrayHasKey('personalIdentifyNumber', $serialized);
        $this->assertArrayHasKey('gender', $serialized);

        $this->assertSame('123', $serialized['organisationNumber']);
        $this->assertSame('20304050', $serialized['personalIdentifyNumber']);
        $this->assertSame('F', $serialized['gender']);

        $customer->setGender('m');
        $serialized = $customer->serialize();
        $this->assertSame('M', $serialized['gender']);

        $customer->setGender('female');
        $serialized = $customer->serialize();
        $this->assertSame(Customer::FEMALE, $serialized['gender']);

        $customer->setGender('male');
        $serialized = $customer->serialize();
        $this->assertSame(Customer::MALE, $serialized['gender']);
    }

    public function test_gender_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('setGender() only allows the value (m, male, f or female)');
        $billingAddress = new Address();
        $customer       = new Customer($billingAddress);
        $customer->setGender('foo');
    }
}
