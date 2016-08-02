<?php

require __DIR__ . '/../vendor/autoload.php';

use \pillr\library\http\Stream;
use \pillr\library\http\Uri;
use \pillr\library\http\Request;

class TestRequest extends \PHPUnit_Framework_TestCase {

    public function testConstructAndGet(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            "1.0",
            "GET",
            $testUri,
            array(),
            "Valid Body"
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

    public function testWithRequstTarget(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            "1.0",
            "GET",
            $testUri,
            array(),
            "Valid Body"
        );

        $withRequest = $testRequest->withRequestTarget("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $this->assertEquals(
            $withRequest->getRequestTarget(),
            "http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment"
        );
    }

    public function testWithMethod(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            "1.0",
            "GET",
            $testUri,
            array(),
            "Valid Body"
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
            "1.0",
            "GET",
            $testUri,
            array(),
            "Valid Body"
        );

        $withRequest = $testRequest->withMethod("invalid");
    }

    public function testWithHeaderWithUri(){
        $testUri = new Uri("http://userinfo:password@myanimelist.net:8080/search/all?q=one%20piece#extrafragment");
        $withUri = new Uri("http:search/all?q=one%20piece#extrafragment");

        $testRequest = new Request(
            "1.0",
            "GET",
            $testUri,
            array(),
            "Valid Body"
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
            "1.0",
            "GET",
            $testUri,
            array(),
            "Valid Body"
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