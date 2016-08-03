<?php
require __DIR__ . '/../vendor/autoload.php';

# TIP: Use the $_SERVER Sugerglobal to get all the data your need from the Client's HTTP Request.

# TIP: HTTP headers are printed natively in PHP by invoking header().
#      Ex. header('Content-Type', 'text/html');

// HTTP/1.1 200 OK
// Date: <NOW-DATE-TIME-STRING>
// Server: <SERVER-PROVIDING-THE-RESPONSE>
// Last-Modified: <LAST-TIME-CONTENT-MODIFIED-DATE-TIME-STRING>
// Content-Length: <MESSAGE-BODY-STRING-LENGTH>
// Content-Type: application/json
// {
//     "@id": "<URI-THAT-WAS-REQUESTED>",
//     "to": "Pillr",
//     "subject": "Hello Pillr",
//     "message": "Here is my submission.",
//     "from": "<YOUR-NAME>",
//     "timeSent": "<TIMESTAMP>"
// }


use pillr\library\http\ServerRequest   as ServerRequest;
use pillr\library\http\Response         as Response;
use pillr\library\http\Uri              as Uri;

date_default_timezone_set("UTC");

$incomingRequest = new ServerRequest();

$returnResponse = new Response("1.1", "200", "OK", [], "");
$bodyArray = array("@id" => $incomingRequest->getUri(),
                   "to" => "Pillr",
                   "subject" => "Hello Pillr",
                   "message" => "Here is my submission.",
                   "from" => "Michael Woo",
                   "timeSent" => time()
                  );

$body = json_encode($bodyArray);
$returnResponse = $returnResponse->withBody($body);

$returnResponse = $returnResponse->withAddedHeader("Date", Date("D, d M Y H:i:s O"));
$returnResponse = $returnResponse->withAddedHeader("Server", $_SERVER["SERVER_NAME"]);
$returnResponse = $returnResponse->withAddedHeader("Last-Modified", Date("D, d M Y H:i:s O", getlastmod()));
$returnResponse = $returnResponse->withAddedHeader("Content-Length", (string)$returnResponse->getBody()->getSize());
$returnResponse = $returnResponse->withAddedHeader("Content-Type", "application/json");

$headers = $returnResponse->getHeaders();
header("HTTP/".$returnResponse->getProtocolVersion()." ".$returnResponse->getStatusCode()." ".$returnResponse->getReasonPhrase());
foreach ($headers as $key => $value) {
    header($key.": ".$value);
}
echo $returnResponse->getBody();