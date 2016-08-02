<?php

require __DIR__ . '/../vendor/autoload.php';

use \pillr\library\http\Request  as HttpRequest;
use \pillr\library\http\Response as HttpResponse;
use \pillr\library\http\Uri      as Uri;

class TestHttpServer extends \PHPUnit_Framework_TestCase {

    public function testRequest()
    {
        // *
 // * - Protocol version
 // * - HTTP method
 // * - URI
 // * - Headers
 // * - Message body

        $uri_string = 'https://pillrcompany.com/interns/test?psr=true';

        $httpRequest =  new HttpRequest(
            '1.1',
            'GET',
            new Uri($uri_string),
            array('Accept' => 'application/json'),
            ''
        );

        $this->assertEquals(
            $httpRequest->getRequestTarget(),
            '/interns/test?psr=true'
        );

        $this->assertEquals(
            $httpRequest->getMethod(),
            'GET'
        );

        $this->assertEquals(
            $httpRequest->getUri(),
            new Uri($uri_string)
        );

        $withRequest = $httpRequest->withRequestTarget('https://pillrcompany.com/intern/alt');

        $this->assertEquals(
            $withRequest->getRequestTarget(),
            'https://pillrcompany.com/intern/alt'
        );

    }

    public function testResponse()
    {

 // - Protocol version
 // * - Status code and reason phrase
 // * - Headers
 // * - Message body

        $httpResponse =  new HttpResponse(
            '1.1',
            '200',
            'OK',
            array('Content-Type' => 'application/json'),
            'hello'
        );

        $httpResponseAlt =  new HttpResponse(
            '1.1',
            '404',
            'Not Found',
            array('Content-Type' => 'application/json'),
            'hello'
        );

        $this->assertEquals($httpResponse->getStatusCode(), '200');

        $withRequest = $httpResponse->withStatus('404', 'Not Found');
        $this->assertEquals(
            $withRequest->getStatusCode(),
            "404"
        );
        $this->assertEquals(
            $withRequest->getReasonPhrase(),
            "Not Found"
        );
    }
}
