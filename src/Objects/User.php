<?php

namespace Oxemis\OxiBounce\Objects;

/**
 * This class is used to help developpers.
 * It is mapped with the JSON returned by the API.
 */
class User extends ApiObject
{

    private string $email;
    private int $credits;

    /**
     * Email associated with the account.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    protected function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Remaining Credits.
     *
     * @return int
     */
    public function getCredits(): int
    {
        return $this->credits;
    }

    protected function setCredits(int $credits): void
    {
        $this->credits = $credits;
    }

}
