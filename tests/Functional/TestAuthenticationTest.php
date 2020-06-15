<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Authentication;
use Altapay\Api\Test\TestAuthentication;

class TestAuthenticationTest extends AbstractFunctionalTest
{

    public function test_auth()
    {
        $response = (new TestAuthentication($this->getAuth()))->call();
        $this->assertTrue($response);
    }

    public function test_auth_fails()
    {
        $response = (new TestAuthentication(new Authentication('username', 'password')))->call();
        $this->assertFalse($response);
    }

    public function test_auth_fails_connection()
    {
        $response = (new TestAuthentication(new Authentication('username', 'password', 'http://doesnotexists.mecom')))->call();
        $this->assertFalse($response);
    }

}
