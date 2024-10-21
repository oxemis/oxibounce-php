<?php

namespace Oxemis\OxiBounce\Components;

use Oxemis\OxiBounce\OxiBounceClient;
use GuzzleHttp\Client as GuzzleClient;
use Oxemis\OxiBounce\OxiBounceException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

/**
 * Base class
 */
abstract class Component
{

    private GuzzleClient $guzzleClient;
    private string $baseUrl;

    /**
     * @param OxiBounceClient $apiClient
     */
    public function __construct(OxiBounceClient $apiClient)
    {
        $this->guzzleClient = new GuzzleClient([
                'headers' => [
                    'user-agent' => $apiClient->getUserAgent(),
                    'authorization' => $apiClient->getAuth()
                ],
            ]);
        $this->baseUrl = $apiClient->getBaseURL();
    }

    /**
     * @param string $verb              HTTP Verb (GET POST DELETE...).
     * @param string $route             Subroute of the component (/user for example).
     * @param string|null $body         HTTP body.
     * @param string|null $file         File to upload (full path).
     * @param string|null $fileName     Name of the file.
     * @param string|null $fileVarName  Name of the variable in the form data.
     * @param array|null $parameters    HTTP query parameters.
     * @return mixed                    Object, Array, Null (for 204) - Please check the API documentation.
     * @throws OxiBounceException
     */
    protected function request(string $verb, string $route, array $parameters = null, string $body = null, string $file = null, string $fileName = null, string $fileVarName = null): mixed
    {

        // Build the query
        $params = [];
        if (!is_null($parameters)) {
            $params["query"] = $parameters;
        }
        if (!is_null($body)) {
            $params["body"] = $body;
        }
        if (!is_null($file)) {

            if (!file_exists($file)) {
                throw new OxiBounceException("File not found : " . $file, 666);
            }

            if (!$fileVarName) {
                throw new OxiBounceException("You must provide a fileVarName when uploading a file", 666);
            }

            if (!$fileName) {
                $fileName = basename($file);
            }

            $params["multipart"] = [
                [
                    'name'     => $fileVarName,
                    'contents' => fopen($file, 'r'),
                    'filename' => $fileName
                ]
            ];
        }

        try {

            // Make request
            $res = $this->guzzleClient->request($verb, $this->baseUrl . $route, $params);

            if (($res->getStatusCode() < 200) or ($res->getStatusCode() > 299)) {
                throw new OxiBounceException($res->getBody(), $res->getStatusCode());
            }

        } catch (RequestException $e) {

            // Exception catched, we get the response
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            // Try to convert it in JSON
            $responseBodyAsJSON = json_decode($responseBodyAsString);

            if ((!$responseBodyAsJSON) || (!property_exists($responseBodyAsJSON,
                    "Code")) || (!property_exists($responseBodyAsJSON, "Message"))) {

                // Unkown error - Invalid JSON or JSON without API Exception Properties (code + message)
                throw new OxiBounceException($e->getMessage(), $e->getCode());

            } else {

                // Error sent by the api (JSON)
                throw new OxiBounceException($responseBodyAsJSON->Message, $responseBodyAsJSON->Code);

            }

        }

        // We got the result
        if ($res->getStatusCode() == 204) {

            // No data, result is null
            $return = null;

        } else {

            // Déconding the JSON
            $return = json_decode((string)$res->getBody());

            if (!$return) {
                // Invalid JSON
                throw new OxiBounceException("Invalid JSON answer from API : " . $res->getBody(), 666);
            }

        }

        return $return;

    }

}
