<?php

namespace Valitor\ApiTest\Request;

use Valitor\Request\OrderLine;

class OrderLineRequestTestSerializer extends OrderLine
{
    public function serialize()
    {
        return $this->get($this, 'foobar');
    }
}
