<?php

namespace Altapay\ApiTest\Request;

use Altapay\Request\OrderLine;

class OrderLineRequestTestSerializer extends OrderLine
{
    public function serialize()
    {
        $result = $this->get($this, 'foobar');
        if ($this->get($this, 'foobar') === false) {
            throw new \Exception('Got false');
        }

        return $result;
    }
}
