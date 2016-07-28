<?php

require __DIR__ . '/../vendor/autoload.php';

use \pillr\library\http\Stream;
use \pillr\library\http\Uri;
use \pillr\library\http\Request;

class TestRequest extends \PHPUnit_Framework_TestCase {

    public function testConstructAndGet(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $this->assertEquals(
            $testRequest->getRequestTarget(),
            "/search/all?q=one%20piece"
        );

        $this->assertEquals(
            $testRequest->getMethod(),
            "GET"
        );

        $this->assertEquals(
            (string)$testRequest->getUri(),
            "http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment"
        );
    }

    public function testAbsoluteWithRequstTarget(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withRequestTarget("absolute");

        $this->assertEquals(
            $withRequest->getRequestTarget(),
            "http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment"
        );
    }

    public function testOriginWithRequstTarget(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withRequestTarget("absolute");
        $withRequest = $withRequest->withRequestTarget("origin");

        $this->assertEquals(
            $withRequest->getRequestTarget(),
            "/search/all?q=one%20piece"
        );
    }

    public function testAuthorityWithRequstTarget(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withRequestTarget("authority");

        $this->assertEquals(
            $withRequest->getRequestTarget(),
            "userinfo:password@myanimelist.net:8080"
        );
    }

    public function testAsteriskWithRequstTarget(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withRequestTarget("asterisk");

        $this->assertEquals(
            $withRequest->getRequestTarget(),
            "*"
        );
    }

    public function testFailInvalidWithRequstTarget(){
        $this->expectException(InvalidArgumentException::class);
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withRequestTarget("invalid");
    }

    public function testWithMethod(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withMethod("POST");

        $this->assertEquals(
            $withRequest->getMethod(),
            "POST"
        );
    }

    public function testFailInvalidWithMethod(){
        $this->expectException(InvalidArgumentException::class);
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withMethod("invalid");
    }

    public function testWithHeaderWithUri(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = new Uri("http:search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withUri($withUri);

        $this->assertFalse(
            $withRequest->hasHeader("host")
        );

        $this->assertEquals(
            (string)$withRequest->getUri(),
            "http:search/all?q=one%20piece#extrafragment"
        );
    }

    public function testWithoutHeaderPreserveWithUri(){
        $testUri = new Uri("http:search/all?q=one%20piece#extrafragment");
        $withUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "GET",
            $testUri
        );

        $withRequest = $testRequest->withUri($withUri);

        $this->assertEquals(
            $withRequest->getHeader("host"),
            "myanimelist.net"
        );

        $this->assertEquals(
            (string)$withRequest->getUri(),
            "http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment"
        );
    }
} ?>