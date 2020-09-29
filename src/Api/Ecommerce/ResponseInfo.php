<?php

namespace Altapay\Api\Ecommerce;

use Altapay\Api\Ecommerce\Callback;

class ResponseInfo extends Callback
{
    public function __construct($postedData)
    {
        parent::__construct($postedData);
    }

    /**
     * @return Address|null
     */
    public function getRegisteredAddress()
    {
        $response          = $this->call();
        $registeredAddress = null;
        if (isset($response->Transactions[0]->CustomerInfo->RegisteredAddress)) {
            $registeredAddress = $response->Transactions[0]->CustomerInfo->RegisteredAddress;
        }

        return $registeredAddress;
    }
}
