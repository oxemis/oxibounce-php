<?php

namespace Oxemis\OxiBounce\Components;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Oxemis\OxiBounce\ApiClient;
use Oxemis\OxiBounce\ApiException;

/**
 * Base class
 */
abstract class Component
{

    private GuzzleClient $guzzleClient;
    private string $baseUrl;

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient)
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
     * @param array|null $parameters    HTTP query parameters.
     * @return mixed                    Object, Array, Null (for 204) - Please check the API documentation.
     * @throws ApiException
     */
    protected function request(string $verb, string $route, array $parameters = null, string $body = null)
    {

        // Build the query
        $params = [];
        if (!is_null($parameters)) {
            $params["query"] = $parameters;
        }
        if (!is_null($body)) {
            $params["body"] = $body;
        }

        try {

            // Make request
            $res = $this->guzzleClient->request($verb, $this->baseUrl . $route, $params);

            if (($res->getStatusCode() < 200) or ($res->getStatusCode() > 299)) {
                throw new ApiException($res->getBody(), $res->getStatusCode());
            }

        } catch (GuzzleException $e) {

            // Exception catched, we get the response
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            // Try to convert it in JSON
            $responseBodyAsJSON = json_decode($responseBodyAsString);

            if ((!$responseBodyAsJSON) || (!property_exists($responseBodyAsJSON,
                    "Code")) || (!property_exists($responseBodyAsJSON, "Message"))) {

                // Unkown error - Invalid JSON or JSON without API Exception Properties (code + message)
                throw new ApiException($e->getMessage(), $e->getCode());

            } else {

                // Error sent by the api (JSON)
                throw new ApiException($responseBodyAsJSON->Message, $responseBodyAsJSON->Code);

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
                throw new ApiException("Invalid JSON answer from API : " . $res->getBody(), 666);
            }

        }

        return $return;

    }

}
