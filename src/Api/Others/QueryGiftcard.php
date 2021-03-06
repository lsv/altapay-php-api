<?php
/**
 * Copyright (c) 2016 Martin Aarhof
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Valitor\Api\Others;

use Valitor\AbstractApi;
use Valitor\Request\Giftcard;
use Valitor\Response\GiftcardResponse;
use Valitor\Serializer\ResponseSerializer;
use Valitor\Traits\TerminalTrait;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This method is used to get information about a gift card.
 */
class QueryGiftcard extends AbstractApi
{
    use TerminalTrait;

    /**
     * Gift card to check
     *
     * @param Giftcard $giftcard
     * @return $this
     */
    public function setGiftcard(Giftcard $giftcard)
    {
        $this->unresolvedOptions['giftcard'] = $giftcard;
        return $this;
    }

    /**
     * Configure options
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['terminal', 'giftcard']);
        $resolver->setAllowedTypes('giftcard', Giftcard::class);
    }

    /**
     * Handle response
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    protected function handleResponse(Request $request, Response $response)
    {
        $body = (string) $response->getBody();
        $xml = simplexml_load_string($body);
        return ResponseSerializer::serialize(GiftcardResponse::class, $xml->Body, false, $xml->Header);
    }

    /**
     * Url to api call
     *
     * @param array $options Resolved options
     * @return string
     */
    protected function getUrl(array $options)
    {
        /** @var Giftcard $card */
        $card = $options['giftcard'];
        unset($options['giftcard']);
        $options['giftcard']['account_identifier'] = $card->getAccount();
        $options['giftcard']['provider'] = $card->getProvider();
        $options['giftcard']['token'] = $card->getToken();
        $query = $this->buildUrl($options);
        return sprintf('queryGiftCard/?%s', $query);
    }
}
