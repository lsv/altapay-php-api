<?php

namespace Altapay\ApiTest\Request;

use Altapay\ApiTest\AbstractTest;
use Altapay\Request\OrderLine;

class OrderLineTest extends AbstractTest
{

    public function test_orderline(): void
    {
        $line = new OrderLine('description', '12', 2, 12.50);
        $line->setGoodsType('item');
        $line->taxAmount = 4.75;
        $line->taxPercent = 38;
        $line->unitCode = 'code';
        $line->discount = 1;
        $line->imageUrl = 'https://image.com';
        $line->productUrl = 'https://image.com';

        $serialized = $line->serialize();

        $this->assertArrayHasKey('description', $serialized);
        $this->assertArrayHasKey('itemId', $serialized);
        $this->assertArrayHasKey('quantity', $serialized);
        $this->assertArrayHasKey('unitPrice', $serialized);
        $this->assertArrayHasKey('taxAmount', $serialized);
        $this->assertArrayHasKey('taxPercent', $serialized);
        $this->assertArrayHasKey('unitCode', $serialized);
        $this->assertArrayHasKey('discount', $serialized);
        $this->assertArrayHasKey('goodsType', $serialized);
        $this->assertArrayHasKey('imageUrl', $serialized);
        $this->assertArrayHasKey('productUrl', $serialized);

        $this->assertEquals('description', $serialized['description']);
        $this->assertEquals(12, $serialized['itemId']);
        $this->assertEquals(2, $serialized['quantity']);
        $this->assertEquals(12.50, $serialized['unitPrice']);
        $this->assertEquals(4.75, $serialized['taxAmount']);
        $this->assertEquals(38, $serialized['taxPercent']);
        $this->assertEquals('code', $serialized['unitCode']);
        $this->assertEquals(1, $serialized['discount']);
        $this->assertEquals('item', $serialized['goodsType']);
        $this->assertEquals('https://image.com', $serialized['imageUrl']);
        $this->assertEquals('https://image.com', $serialized['productUrl']);

    }

    /**
     * @return array<int, array<int, mixed>>
     */
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
    public function test_can_not_set_goodstypes($type, $exception = false): void
    {
        if ($exception) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage(
                'goodsType should be one of "shipment|handling|item" you have selected "' . $type . '"'
            );
        }

        $line = new OrderLine('description', '12', 2, 12.50);
        $line->setGoodsType($type);
        $s = $line->serialize();

        $this->assertEquals($type, $s['goodsType']);
    }

    public function test_serializer(): void
    {
        $line = new OrderLineRequestTestSerializer('description', 12, 2, 12.50);
        $this->assertFalse($line->serialize());
    }

}
