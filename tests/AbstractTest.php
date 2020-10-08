<?php

namespace Altapay\ApiTest;

use Altapay\Authentication;
use Faker\Factory;

abstract class AbstractTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @return \Faker\Generator
     */
    protected function getFaker()
    {
        return Factory::create('da_DK');
    }

    /**
     * @param int    $length
     * @param string $characters
     *
     * @return string
     */
    public function randomString($length, $characters = '0123456789abcdef')
    {
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }

    /**
     * @return Authentication
     */
    protected function getAuth()
    {
        return new Authentication('test_username', 'test_password');
    }

}
