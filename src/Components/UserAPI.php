<?php

namespace Oxemis\OxiBounce\Components;

use Oxemis\OxiBounce\ApiClient;
use Oxemis\OxiBounce\ApiException;
use Oxemis\OxiBounce\Objects\User;

/**
 * Class for https://api.oxibounce.com/doc/#/user
 */
class UserAPI extends Component
{

    public function __construct(ApiClient $apiClient)
    {
        parent::__construct($apiClient);
    }

    /**
     * Get informations about your account.
     *
     * @return User             Current user information (see https://api.oxibounce.com/doc/#/user).
     * @throws ApiException
     */
    public function getUser(): User
    {
        $o = $this->request("GET", "/user");
        return (User::mapFromStdClass($o));
    }

}
