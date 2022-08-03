<?php

namespace Valitor\ApiTest\Functional;

use DateInterval;
use DateTime;
use Valitor\Authentication;
use Valitor\Request\Card;
use Valitor\ApiTest\AbstractTest;

abstract class AbstractFunctionalTest extends AbstractTest
{

    const VALID_VISA_CARD_NUMBER = '4140000000001466';

    protected function setUp(): void
    {
        if (! file_exists(__DIR__ . '/../../.env.php')) {
            $this->markTestSkipped(
                'Can not test functional because .env.php file does not exists'
            );
        }
    }

    protected function getAuth(): Authentication
    {
        return new Authentication($_ENV['USERNAME'], $_ENV['PASSWORD'], $this->getBaseUrl());
    }

    protected function getBaseUrl(): string
    {
        return $_ENV['BASEURL'];
    }

    protected function getTerminal(): string
    {
        return $_ENV['TERMINAL'];
    }

    protected function getValidCard(): Card
    {
        return $this->generateCard(self::VALID_VISA_CARD_NUMBER);
    }

    protected function generateCard(int $number): Card
    {
        return new Card(
            $number,
            (new DateTime())->format('m'),
            (new DateTime())->add(new DateInterval('P1Y'))->format('Y'),
            123
        );
    }

}
