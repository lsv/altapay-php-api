<?php

namespace Valitor\ApiTest;

use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Valitor\Authentication;
use Faker\Factory;

abstract class AbstractTest extends TestCase
{

    protected function getFaker(): Generator
    {
        return Factory::create('da_DK');
    }

    public function setExpectedException($class, $message = '', $code = null)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($class);

            if ($message) {
                $this->expectExceptionMessage($message);
            }

            if ($code !== null) {
                $this->expectExceptionCode($code);
            }
        } else {
            parent::expectException($class);
            parent::expectExceptionMessage($message);
            parent::expectExceptionCode($code);
        }
    }

    public function randomString($length, $characters = '0123456789abcdef'): string
    {
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }

    protected function getAuth(): Authentication
    {
        return new Authentication('test_username', 'test_password');
    }

}
