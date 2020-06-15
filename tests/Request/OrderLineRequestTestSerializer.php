<?php

namespace Altapay\ApiTest\Request;

use Altapay\Request\OrderLine;

class OrderLineRequestTestSerializer extends OrderLine
{
    public function serialize()
    {
        return $this->get($this, 'foobar');
    }
}
