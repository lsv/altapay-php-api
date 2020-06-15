<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Authentication;
use Altapay\Request\Card;
use Altapay\ApiTest\AbstractTest;

abstract class AbstractFunctionalTest extends AbstractTest
{

    const VALID_VISA_CARD_NUMBER = '4140000000001466';

    protected function setUp()
    {
        if (! file_exists(__DIR__ . '/../../.env.php')) {
            $this->markTestSkipped(
                'Can not test functional because .env.php file does not exists'
            );
        }
    }

    /**
     * @return Authentication
     */
    protected function getAuth()
    {
        return new Authentication($_ENV['USERNAME'], $_ENV['PASSWORD'], $this->getBaseUrl());
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        return $_ENV['BASEURL'];
    }

    /**
     * @return string
     */
    protected function getTerminal()
    {
        return $_ENV['TERMINAL'];
    }

    /**
     * @return Card
     */
    protected function getValidCard()
    {
        return $this->generateCard(self::VALID_VISA_CARD_NUMBER);
    }

    protected function generateCard($number)
    {
        return new Card(
            $number,
            (new \DateTime())->format('m'),
            (new \DateTime())->add(new \DateInterval('P1Y'))->format('Y'),
            123
        );
    }

}
