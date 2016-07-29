<?php

require __DIR__ . '/../vendor/autoload.php';

use \pillr\library\http\Stream;
use \pillr\library\http\Response;

class TestRequest extends \PHPUnit_Framework_TestCase {

    public function testConstructAndGet(){
        $testRequest = new Response(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "200",
            "OK"
        );

        $this->assertEquals(
            $testRequest->getStatusCode(),
            "200"
        );

        $this->assertEquals(
            $testRequest->getReasonPhrase(),
            "OK"
        );
    }

    public function testWithStatus(){
        $testRequest = new Response(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "200",
            "OK"
        );

        $withRequest = $testRequest->withStatus("404", "Not Found");

        $this->assertEquals(
            $withRequest->getStatusCode(),
            "404"
        );

        $this->assertEquals(
            $withRequest->getReasonPhrase(),
            "Not Found"
        );
    }

    public function testFailInvalidCodeWithStatus(){
        $this->expectException(InvalidArgumentException::class);
        $testRequest = new Response(
            array(),
            new Stream("Valid Body"),
            "1.0",
            "200",
            "OK"
        );

        $withRequest = $testRequest->withStatus("asdf", "asdfasdf");
    }
} ?>