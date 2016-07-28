<?php

namespace pillr\library\http;


use \Psr\Http\Message\RequestInterface  as  RequestInterface;
use \Psr\Http\Message\UriInterface      as  UriInterface;

use \pillr\library\http\Message         as  Message;
/**
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * During construction, implementations MUST attempt to set the Host header from
 * a provided URI if no Host header is provided.
 *
 * Requests are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */



class Request extends Message implements RequestInterface
{
    const HTTPMETHODS = array("GET", "HEAD", "POST", "PUT", "\DELETE", "CONNECT", "OPTIONS", "TRACE");

    private $httpMethod;
    private $uri;
    private $requestTarget;

    function __construct(array $inputheaders, $body, $version, $method, UriInterface $uri){
        parent::__construct($inputheaders, $body, $version);

        if(!$this->hasHeader("host") && !empty($this->getHeader("host")) && $uri->getHost() != ""){
            $this->headers["host"] = $uri->getHost();
        }

        $this->uri = $uri;
        $this->httpMethod = $method;
        if($this->uri->getQuery() == ""){
            $this->requestTarget = $this->uri->getPath();
        }
        else{
            $this->requestTarget = $this->uri->getPath() . '?' . $this->uri->getQuery();
        }
    }


    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->requestTarget;
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @see http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        $withRequest = new Request($this->headers, $this->getBody(), $this->protocolVersion, $this->httpMethod, $this->uri);

        switch ($requestTarget) {
            case 'origin':
                if($withRequest->uri->getQuery() == ""){
                    $withRequest->requestTarget = $withRequest->uri->getPath();
                }
                else{
                    $withRequest->requestTarget = $withRequest->uri->getPath() . '?' . $withRequest->uri->getQuery();
                }
                break;

            case 'absolute':
                $withRequest->requestTarget = (string)$withRequest->uri;
                break;

            case 'authority':
                $withRequest->requestTarget = $withRequest->uri->getAuthority();
                break;

            case 'asterisk':
                $withRequest->requestTarget = "*";
                break;

            default:
                throw new \InvalidArgumentException("invalid request target");
                break;
        }

        return $withRequest;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return self
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        if(!in_array($method, self::HTTPMETHODS)){
            throw new \InvalidArgumentException("Method not valid");

        }

        $withRequest = new Request($this->headers, $this->messageBody, $this->protocolVersion, $method, $this->uri);
        return $withRequest;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return self
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $newheaders = $this->getHeaders();

        if($preserveHost){
            if(!$this->hasHeader("host") && !empty($this->getHeader("host")) && $uri->getHost() != ""){
                $newheaders["host"] = $uri->getHost();
            }
        }

        else{
            if($uri->getHost() != ""){
                $newheaders["host"] = $uri->getHost();
            }

            else{
                unset($newheaders["host"]);
            }
        }

        $withRequest = new Request($newheaders, $this->messageBody, $this->protocolVersion, $this->httpMethod, $uri);
        return $withRequest;
    }

}