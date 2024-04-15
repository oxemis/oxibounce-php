<?php

namespace Oxemis\OxiBounce;

use Oxemis\OxiBounce\Components\CheckAPI;
use Oxemis\OxiBounce\Components\UserAPI;

/**
 * API Client for OxiBounce.
 */
class ApiClient
{

    private string $auth;
    private string $baseURL;
    private string $userAgent;

    public function __construct(string $apiLogin, string $apiPassword)
    {

        $this->auth = base64_encode($apiLogin . ":" . $apiPassword);
        $this->userAgent = Configuration::USER_AGENT . PHP_VERSION . '/' . Configuration::WRAPPER_VERSION;
        $this->baseURL = Configuration::MAIN_URL;
        $this->userAPI = new UserAPI($this);
        $this->checkAPI = new CheckAPI($this);

    }

    public function getAuth(): string
    {
        return $this->auth;
    }

    public function getBaseURL(): string
    {
        return $this->baseURL;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

}
