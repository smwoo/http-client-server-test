<?php

require __DIR__ . '/../vendor/autoload.php';

use \pillr\library\http\Stream;

class TestStream extends \PHPUnit_Framework_TestCase {
    public function testConstructAndGet(){
        $stream = new Stream("Valid Body");

        $this->assertEquals(
            (string)$stream,
            "Valid Body"
        );

        $stream->rewind();

        $this->assertTrue(
            $stream->isWritable()
        );

        $this->assertTrue(
            $stream->isReadable()
        );

        $this->assertTrue(
            $stream->isSeekable()
        );
    }

    public function testDetach(){
        $stream = new Stream("Valid Body");

        $detachedStream = $stream->detach();

        $this->assertFalse(
            $stream->isWritable()
        );

        $this->assertFalse(
            $stream->isReadable()
        );

        $this->assertFalse(
            $stream->isSeekable()
        );

        $this->assertEquals(
            stream_get_contents($detachedStream),
            "Valid Body"
        );
    }

    public function testClose(){
        $stream = new Stream("Valid Body");

        $stream->close();

        $this->assertFalse(
            $stream->isWritable()
        );

        $this->assertFalse(
            $stream->isReadable()
        );

        $this->assertFalse(
            $stream->isSeekable()
        );
    }

    public function testGetSize(){
        $stream = new Stream("Valid Body");

        $this->assertEquals(
            $stream->getSize(),
            strlen("Valid Body")
        );
    }

    public function testRead(){
        $stream = new Stream("Valid Body");

        $this->assertEquals(
            $stream->read(3),
            "Val"
        );
    }

    public function testSeek(){
        $stream = new Stream("Valid Body");

        $stream->seek(-2, SEEK_END);
        $this->assertEquals(
            $stream->read(3),
            "dy"
        );
    }

    public function testRewind(){
        $stream = new Stream("Valid Body");

        $stream->seek(3);
        $stream->rewind();
        $this->assertEquals(
            $stream->read(3),
            "Val"
        );
    }

    public function testTell(){
        $stream = new Stream("Valid Body");

        $stream->seek(3);
        $this->assertEquals(
            $stream->tell(),
            3
        );
    }

    public function testEof(){
        $stream = new Stream("Valid Body");

        $this->assertFalse(
            $stream->eof()
        );

        (string)$stream;

        $this->assertTrue(
            $stream->eof()
        );
    }

    public function testWrite(){
        $stream = new Stream("Valid Body");

        $stream->seek(0, SEEK_END);
        $stream->write(" New Body");

        $this->assertEquals(
            (string)$stream,
            "Valid Body New Body"
        );
    }

    public function testGetContents(){
        $stream = new Stream("Valid Body");

        $stream->seek(6);

        $this->assertEquals(
            $stream->getContents(),
            "Body"
        );
    }

    public function testGetMetadata(){
        $stream = new Stream("Valid Body");

        $this->assertEquals(
            array_keys($stream->getMetadata()),
            array('timed_out', 'blocked', 'eof', 'wrapper_type', 'stream_type', 'mode', 'unread_bytes', 'seekable', 'uri')
        );

        $this->assertEquals(
            $stream->getMetadata('mode'),
            "w+b"
        );

        $this->assertEquals(
            $stream->getMetadata('non-existant key'),
            ""
        );
    }
} ?>