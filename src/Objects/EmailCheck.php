<?php

namespace Oxemis\OxiBounce\Objects;

/**
 * This class is used to help developpers.
 * It is mapped with the JSON returned by the API.
 */
class EmailCheck extends ApiObject
{

    public const STATUS_PENDING = "PENDING";
    public const STATUS_DONE = "DONE";
    private int $id;
    private string $email;
    private string $status = self::STATUS_PENDING;

    /**
     * Get the ID of the check.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    protected function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the email of the check.
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
     * Get the status of the check (STATUS_PENDING or STATUS_DONE).
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    protected function setStatus(string $status): void
    {
        $this->status = $status;
    }

}
