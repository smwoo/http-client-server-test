<?php

namespace pillr\library\http;

use Psr\Http\Message\MessageInterface as MessageInterface;
use Psr\Http\Message\StreamInterface as StreamInterface;
/**
 * HTTP messages consist of requests from a client to a server and responses
 * from a server to a client. This interface defines the methods common to
 * each.
 *
 * Messages are considered immutable{} all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 *
 * @see http://www.ietf.org/rfc/rfc7230.txt
 * @see http://www.ietf.org/rfc/rfc7231.txt
 */
class Message implements MessageInterface
{
    private $protocolVersion;
    private $headers;
    private $messageBody;

    private function is_assoc(array $array)
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

    function __construct(array $inputheaders, string $body, string $version){

        // check if message headers are valid
        if(!is_assoc($inputheaders)){
            throw new RuntimeException("header parameter is incorrect, make sure it is an assosciative array with each key being the header name and each value being a string or ann array of strings");
        }
        else{
            $keys = array_keys($inputheaders);
            foreach($keys as $key){
                $keyvalue = $inputheaders[$key];
                if(!is_array($keyvalue)){
                    if(!is_string($keyvalue)){
                        throw new RuntimeException("header parameter is incorrect, make sure it is an assosciative array with each key being the header name and each value being a string or ann array of strings");
                    }
                }
                else{
                    foreach($keyvalue as $arrayvalue){
                        if(!is_string($keyvalue)){
                            throw new RuntimeException("header parameter is incorrect, make sure it is an assosciative array with each key being the header name and each value being a string or ann array of strings");
                        }
                    }
                }
            }
        }

        $streambody = new StreamInterface($body);

        $this->headers = $inputheaders;
        $this->body = $streambody;
        $this->protocolVersion = $version;
    }

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return self
     */
    public function withProtocolVersion($version)
    {
        $response = new Message($this->headers, $this->messageBody, $version);
        return $response;
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values){}
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false){}
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers.
     *     Each key MUST be a header name, and each value MUST be an array of
     *     strings for that header.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        $keys = array_keys($this->headers);
        foreach($keys as $key){
            if(strtolower($name) == strtolower($key)){
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name)
    {
        $keys = array_keys($this->headers);
        foreach($keys as $key){
            if(strtolower($name) == strtolower($key)){
                return $this->headers[$key];
            }
        }

        return array();
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        $keys = array_keys($this->headers);
        foreach($keys as $key){
            if(strtolower($name) == strtolower($key)){
                return implode(",", $this->headers[$key]);
            }
        }

        return "";
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        $newheader = array( $name => $value);
        $response = new Message($newheader, $this->messageBody, $this->version);
        return $response;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names.
     * @throws \InvalidArgumentException for invalid header values.
     */
    public function withAddedHeader($name, $value)
    {
        $newheader = $this->headers;
        $newheader[$name] = $value;
        $response = new Message($newheader, $this->messageBody, $this->version);
        return $response;
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name)
    {
        $newheader = $this->headers;
        unset($newheader[$name]);
        $response = new Message($newheader, $this->messageBody, $this->version);
        return $response;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        $body = (string) $this->messageBody;
        $response = new Message($this->headers, $body, $this->version);
        return $response;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return self
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        $body = (string) $this->messageBody;
        $response = new Message($this->headers, $body, $this->version);
        return $response;
    }
}