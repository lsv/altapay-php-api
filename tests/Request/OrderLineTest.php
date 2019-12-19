<?php

namespace Valitor\ApiTest\Request;

use Valitor\ApiTest\AbstractTest;
use Valitor\Request\OrderLine;

class OrderLineTest extends AbstractTest
{

    public function test_orderline()
    {
        $line = new OrderLine('description', 12, 2, 12.50);
        $line->setGoodsType('item');
        $line->taxAmount = 4.75;
        $line->unitCode = 'code';
        $line->discount = 1;
        $line->imageUrl = 'https://image.com';

        $serialized = $line->serialize();

        $this->assertArrayHasKey('description', $serialized);
        $this->assertArrayHasKey('itemId', $serialized);
        $this->assertArrayHasKey('quantity', $serialized);
        $this->assertArrayHasKey('unitPrice', $serialized);
        $this->assertArrayHasKey('taxAmount', $serialized);
        $this->assertArrayHasKey('unitCode', $serialized);
        $this->assertArrayHasKey('discount', $serialized);
        $this->assertArrayHasKey('goodsType', $serialized);
        $this->assertArrayHasKey('imageUrl', $serialized);

        $this->assertEquals('description', $serialized['description']);
        $this->assertEquals(12, $serialized['itemId']);
        $this->assertEquals(2, $serialized['quantity']);
        $this->assertEquals(12.50, $serialized['unitPrice']);
        $this->assertEquals(4.75, $serialized['taxAmount']);
        $this->assertEquals('code', $serialized['unitCode']);
        $this->assertEquals(1, $serialized['discount']);
        $this->assertEquals('item', $serialized['goodsType']);
        $this->assertEquals('https://image.com', $serialized['imageUrl']);

    }

    public function dataProvider()
    {
        return [
            ['shipment'],
            ['handling'],
            ['item'],
            ['no_item', true]
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param string $type
     * @param bool $exception
     */
    public function test_can_not_set_goodstypes($type, $exception = false)
    {
        if ($exception) {
            $this->setExpectedException(
                \InvalidArgumentException::class,
                'goodsType should be one of "shipment|handling|item" you have selected "' . $type . '"'
            );
        }

        $line = new OrderLine('description', 12, 2, 12.50);
        $line->setGoodsType($type);
        $s = $line->serialize();

        $this->assertEquals($type, $s['goodsType']);
    }

    public function test_can_not_set_both_tax_types()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Only one of "taxPercent" and "taxAmount" should be used'
        );

        $line = new OrderLine('description', 12, 2, 12.50);
        $line->taxAmount = 4.75;
        $line->taxPercent = 25;
        $line->serialize();
    }

    public function test_serializer()
    {
        $line = new OrderLineRequestTestSerializer('description', 12, 2, 12.50);
        $this->assertFalse($line->serialize());
    }

}
