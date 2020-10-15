<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Api\Test\TestAuthentication;
use Altapay\Authentication;

class TestAuthenticationTest extends AbstractFunctionalTest
{
    public function test_auth(): void
    {
        $response = (new TestAuthentication($this->getAuth()))->call();
        $this->assertSame('ok', $response);
    }

    public function test_auth_fails(): void
    {
        $response = (new TestAuthentication(new Authentication('username', 'password')))->call();
        $this->assertFalse($response);
    }

    public function test_auth_fails_connection(): void
    {
        $response = (new TestAuthentication(new Authentication(
            'username',
            'password',
            'http://doesnotexists.mecom'
        )))->call();
        $this->assertFalse($response);
    }
}
