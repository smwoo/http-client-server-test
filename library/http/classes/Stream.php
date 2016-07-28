<?php
namespace pillr\library\http;

use \Psr\Http\Message\StreamInterface as StreamInterface;

/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 */
class Stream implements StreamInterface
{
    private $streampointer;
    private $readable;
    private $writeable;
    private $seekable;
    private $detached;

    function __construct($body, $isreadable=true, $iswriteable=true, $isseekable=true){
        $this->streampointer = fopen('php://memory', 'r+');
        fwrite($this->streampointer, $body);
        rewind($this->streampointer);
        $this->readable = $isreadable;
        $this->writeable = $iswriteable;
        $this->seekable = $isseekable;
        $this->detached = false;
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        if(!$this->readable){
            return "";
        }

        rewind($this->streampointer);
        $stringBody = stream_get_contents($this->streampointer);
        return $stringBody;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        $this->checkClosed();

        fclose($this->detach());
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $this->checkClosed();

        $detachedpointer = $this->streampointer;
        $this->streampointer = null;
        $this->readable = false;
        $this->writeable = false;
        $this->seekable = false;
        $this->detached = true;
        return $detachedpointer;
    }

    /**
     * checks if stream is closed and throws error if it is
     */
    private function checkClosed() {
      if ($this->detached) {
        throw new \RuntimeException("Stream is closed.");
      }
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        $this->checkClosed();

        $stat = fstat($this->streampointer);
        $size = $stat['size'];
        return $size;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \\RuntimeException on error.
     */
    public function tell()
    {
        $this->checkClosed();

        $position = ftell($this->streampointer);
        if($position == false){
            throw new \RuntimeException();
        }
        else{
            return $position;
        }
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        $this->checkClosed();

        return feof($this->streampointer);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @see http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \\RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->checkClosed();

        if(!$this->seekable){
            throw new \RuntimeException("steam is not seekable");
        }

        if(fseek($this->streampointer, $offset, $whence) == -1){
            throw new \RuntimeException();
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @see http://www.php.net/manual/en/function.fseek.php
     * @throws \\RuntimeException on failure.
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->writeable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \\RuntimeException on failure.
     */
    public function write($string)
    {
        $this->checkClosed();

        if(!$this->writeable){
            throw new \RuntimeException('not a writeable stream');
        }

        $result = fwrite($this->streampointer, $string);
        if($result == false){
            throw new \RuntimeException();
        }
        else{
            return $result;
        }
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \\RuntimeException if an error occurs.
     */
    public function read($length)
    {
        $this->checkClosed();

        if(!$this->readable){
            throw new \RuntimeException("not a readable stream");
        }

        $readstring = fread($this->streampointer, $length);
        if($readstring == false){
            throw new \RuntimeException();
        }
        else{
            return $readstring;
        }
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \\RuntimeException if unable to read.
     * @throws \\RuntimeException if error occurs while reading.
     */
    public function getContents()
    {
        $this->checkClosed();

        if(!$this->readable){
            throw new \RuntimeException("not a readable stream");
        }

        $readstring = stream_get_contents($this->streampointer);
        if($readstring == false){
            throw new \RuntimeException();
        }
        else{
            return $readstring;
        }
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @see http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $this->checkClosed();

        $meta = stream_get_meta_data($this->streampointer);
        if($key == null){
            return $meta;
        }
        else{
            if(array_key_exists($key, $meta)){
                return $meta[$key];
            }
            else{
                return null;
            }
        }
    }
}