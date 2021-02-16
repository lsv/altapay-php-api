<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Exceptions\ResponseHeaderException;
use Altapay\Exceptions\ResponseMessageException;
use Altapay\Api\Payments\CaptureReservation;
use Altapay\Api\Payments\RefundCapturedReservation;
use Altapay\Api\Payments\ReservationOfFixedAmount;
use Altapay\Response\CaptureReservationResponse;
use Altapay\Response\Embeds\Header;
use Altapay\Response\RefundResponse;
use Altapay\Response\ReservationOfFixedAmountResponse;

class FixedAmountProgressTest extends AbstractFunctionalTest
{
    public function test_create_fixed_amount_fails(): void
    {
        $this->expectException(ResponseHeaderException::class);

        $api = new ReservationOfFixedAmount($this->getAuth());
        $api
            ->setTerminal($this->getTerminal())
            ->setShopOrderId((string)time())
            ->setAmount($this->getFaker()->randomFloat(2, 1, 50))
            ->setCurrency('DKK');
        $api->call();
    }

    public function test_create_fixed_amount_fails_exception(): void
    {
        try {
            $api = new ReservationOfFixedAmount($this->getAuth());
            $api
                ->setTerminal($this->getTerminal())
                ->setShopOrderId((string)time())
                ->setAmount($this->getFaker()->randomFloat(2, 1, 50))
                ->setCurrency('DKK');
            $api->call();
        } catch (ResponseHeaderException $e) {
            $this->assertInstanceOf(Header::class, $e->getHeader());
        }
    }

    public function test_can_reserve_capture_refund(): void
    {
        // Reserve
        $api = new ReservationOfFixedAmount($this->getAuth());
        $api
            ->setTerminal($this->getTerminal())
            ->setShopOrderId((string)time())
            ->setAmount($this->getFaker()->randomFloat(2, 1, 50))
            ->setCurrency('DKK')
            ->setCard($this->getValidCard());
        $response = $api->call();
        $this->assertInstanceOf(ReservationOfFixedAmountResponse::class, $response);

        // Capture
        $api = new CaptureReservation($this->getAuth());
        $api
            ->setTransaction($response->Transactions[0]);
        $response = $api->call();
        $this->assertInstanceOf(CaptureReservationResponse::class, $response);

        // Refund
        $api = new RefundCapturedReservation($this->getAuth());
        $api
            ->setTransaction($response->Transactions[0]);
        $response = $api->call();
        $this->assertInstanceOf(RefundResponse::class, $response);
    }

    public function test_preauth_declined_by_bank(): void
    {
        $this->expectException(ResponseMessageException::class);
        $this->expectExceptionMessage('TestAcquirer[pan=0566 or amount=5660]');

        $api = new ReservationOfFixedAmount($this->getAuth());
        $api
            ->setTerminal($this->getTerminal())
            ->setShopOrderId((string)time())
            ->setAmount(5.66)
            ->setCurrency('DKK')
            ->setCard($this->generateCard('4180000000000566'));
        $api->call();
    }

    public function test_preauth_bank_error(): void
    {
        $this->expectException(ResponseMessageException::class);
        $this->expectExceptionMessage('TestAcquirer[pan=0567 or amount=5670][54321]');

        $api = new ReservationOfFixedAmount($this->getAuth());
        $api
            ->setTerminal($this->getTerminal())
            ->setShopOrderId((string)time())
            ->setAmount(5.67)
            ->setCurrency('DKK')
            ->setCard($this->generateCard('4130000000000567'));
        $api->call();
    }

//    public function test_preauth_cvv_check_error()
//    {
//        $this->markTestSkipped('Not working with API');
//
//        $this->setExpectedException(ResponseMessageException::class);
//        $this->expectExceptionMessage('TestAcquirer[pan=0572 or amount=5720]');
//
//        $api = new ReservationOfFixedAmount($this->getAuth());
//        $api
//            ->setTerminal($this->getTerminal())
//            ->setShopOrderId((string)time())
//            ->setAmount(5.72)
//            ->setCurrency('DKK')
//            ->setCard($this->generateCard('4190000000000572'))
//        ;
//        $api->call();
//    }
//
//    public function test_preauth_card_failed()
//    {
//        $this->markTestSkipped('Not working with API');
//
//        $api = new ReservationOfFixedAmount($this->getAuth());
//        $api
//            ->setTerminal($this->getTerminal())
//            ->setShopOrderId((string)time())
//            ->setAmount(16.66)
//            ->setCurrency('DKK')
//            ->setCard($this->generateCard('4120000000001666'))
//        ;
//        $api->call();
//    }
//
//    public function test_preauth_card_error()
//    {
//        $this->markTestSkipped('Not working with API');
//
//        $api = new ReservationOfFixedAmount($this->getAuth());
//        $api
//            ->setTerminal($this->getTerminal())
//            ->setShopOrderId((string)time())
//            ->setAmount(16.67)
//            ->setCurrency('DKK')
//            ->setCard($this->generateCard('4160000000001667'))
//        ;
//        $api->call();
//    }

    public function test_fraud_check_challenge(): void
    {
        $api = new ReservationOfFixedAmount($this->getAuth());
        $api
            ->setTerminal($this->getTerminal())
            ->setShopOrderId((string)time())
            ->setAmount(102)
            ->setCurrency('DKK')
            ->setCard($this->generateCard('4170000000000121'))
            ->setFraudService('maxmind');
        $response = $api->call();
        $this->assertInstanceOf(ReservationOfFixedAmountResponse::class, $response);
        $this->assertSame('Deny', $response->Transactions[0]->FraudRecommendation);
    }

    public function test_fraud_check_deny(): void
    {
        $api = new ReservationOfFixedAmount($this->getAuth());
        $api
            ->setTerminal($this->getTerminal())
            ->setShopOrderId((string)time())
            ->setAmount(110)
            ->setCurrency('DKK')
            ->setCard($this->generateCard('4170000000000105'))
            ->setFraudService('maxmind');
        $response = $api->call();
        $this->assertInstanceOf(ReservationOfFixedAmountResponse::class, $response);
        $this->assertSame('Deny', $response->Transactions[0]->FraudRecommendation);
    }
}
