<?php
namespace pillr\library\http;


use \Psr\Http\Message\ResponseInterface as ResponseInterface;
use \pillr\library\http\Message         as  Message;

/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class Response extends Message implements ResponseInterface
{

    const VALIDCODES = array(100, 101, 102, 103, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 226, 227, 300, 301, 302, 303, 304, 305, 306, 307, 308, 309, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 451, 452, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512);

    private $statusCode;
    private $reason;

    function __construct($version, $code, $reason, array $inputheaders, $body){
        parent::__construct($inputheaders, $body, $version);

        if($code == ""){
            $code = null;
        }
        else{
            $code = intval($code);
        }

        if(!in_array($code, self::VALIDCODES)){
            throw new \InvalidArgumentException("invalid code: ".$code);
        }

        $this->statusCode = $code;
        $this->reason = $reason;
    }
    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return self
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $withResponse = new Response($this->protocolVersion, $code, $reasonPhrase, $this->headers, $this->messageBody);

        return $withResponse;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be empty. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        return $this->reason
;    }
}