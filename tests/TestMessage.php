<?php

require __DIR__ . '/../vendor/autoload.php';

use \pillr\library\http\Message;
use \pillr\library\http\Stream;

class TestMessage extends \PHPUnit_Framework_TestCase {

    public function testConstructAndGet(){
        $testMessage = new Message(
            array("host" => array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $this->assertEquals(
            $testMessage->getProtocolVersion(),
            "1.0"
        );

        $this->assertEquals(
            $testMessage->getHeaders(),
            array("host" => array("example.com", "additionalInfo"))
        );

        $this->assertTrue(
            $testMessage->hasHeader("host")
        );

        $this->assertEquals(
            $testMessage->getHeader("host"),
            array("example.com", "additionalInfo")
        );

        $this->assertEquals(
            $testMessage->getHeaderLine("host"),
            "example.com,additionalInfo"
        );

        $this->assertEquals(
            (string)$testMessage->getBody(),
            "Valid Body"
        );
    }

    public function testFailNonStringKeyConstruct(){
        $this->setExpectedException(InvalidArgumentException::class);

        $testMessage = new Message(
            array(1 => array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );
    }

    public function testFailNonKeyConstruct(){
        $this->setExpectedException(InvalidArgumentException::class);

        $testMessage = new Message(
            array(array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );
    }

    public function testFailNonStringValueConstruct(){
        $this->setExpectedException(InvalidArgumentException::class);

        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo", 123)),
            "Valid Body",
            "1.0"
        );
    }

    public function testWithProtocolVersion(){
        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withProtocolVersion("1.1");

        $this->assertEquals(
            $withMessage->getProtocolVersion(),
            "1.1"
        );
    }

    public function testFailBadCodeWithProtocolVersion(){
        $this->setExpectedException(InvalidArgumentException::class);

        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withProtocolVersion("foo");

        $this->assertEquals(
            $withMessage->getProtocolVersion(),
            "1.1"
        );
    }

    public function testWithHeader(){
        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withHeader("host", array("example.com", "additionalInfo"));

        $this->assertEquals(
            $withMessage->getHeader("host"),
            array("example.com", "additionalInfo")
        );
    }

    public function testFailNonStringKeyWithHeader(){
        $this->setExpectedException(InvalidArgumentException::class);

        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withHeader(123, array("example.com", "additionalInfo"));
    }

    public function testFailNonStringValueKeyWithHeader(){
        $this->setExpectedException(InvalidArgumentException::class);

        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withHeader("host", array("example.com", "additionalInfo", 123));
    }

    public function testWithAddedHeader(){
        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withAddedHeader("addedHost", array("example.com", "additionalInfo"));

        $this->assertEquals(
            $withMessage->getHeader("addedHost"),
            array("example.com", "additionalInfo")
        );
    }

    public function testFailNonStringKeyWithAddedHeader(){
        $this->setExpectedException(InvalidArgumentException::class);

        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withAddedHeader(123, array("example.com", "additionalInfo"));
    }

    public function testFailNonStringValueKeyWithAddedHeader(){
        $this->setExpectedException(InvalidArgumentException::class);

        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withAddedHeader("host2", array("example.com", "additionalInfo", 123));
    }

    public function testWithOutHeader(){
        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withOutHeader("host");

        $this->assertEquals(
            $withMessage->getHeader("host"),
            array()
        );
    }

    public function testWithBody(){
        $testMessage = new Message(
            array("host" =>array("example.com", "additionalInfo")),
            "Valid Body",
            "1.0"
        );

        $withMessage = $testMessage->withBody("New Body");

        $this->assertEquals(
            $withMessage->getBody(),
            "New Body"
        );
    }
} ?>