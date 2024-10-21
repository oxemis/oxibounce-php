<?php

namespace Oxemis\OxiBounce\Objects;
use DateTime;

/**
 * This class is used to help developpers.
 * It is mapped with the JSON returned by the API.
 */
class EmailList extends ApiObject
{

    public const STATUS_PENDING = "PENDING";
    public const STATUS_DONE = "DONE";
    public const STATUS_WAITING = "WAITING";
    public const STATUS_CANCELED = "CANCELED";
    public const STATUS_IMPORTING = "IMPORTING";
    public const STATUS_ERROR = "ERROR";

    private int $id;
    private string $name;
    private DateTime $createdAt;
    private ?DateTime $startedAt;
    private ?DateTime $endedAt;
    private ?int $nbEmails;
    private ?int $nbEmails_InvalidFormat;
    private ?int $nbEmails_Empty;
    private ?int $nbEmails_Duplicate;
    private ?int $totalCost;
    private string $status;

    /**
     * Get the ID of the list.
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
     * Get the status of the list (see STATUS_*).
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

    /**
     * Get the name of the list (based on the file name).
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    protected function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the creation date of the list.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    protected function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = new DateTime($createdAt);
    }

    /**
     * Get the date when the list has been started.
     *
     * @return DateTime|null
     */
    public function getStartedAt(): ?DateTime
    {
        return $this->startedAt;
    }

    protected function setStartedAt(?string $startedAt): void
    {
        $this->startedAt = $startedAt ? new DateTime($startedAt) : null;
    }

    /**
     * Get the date when the list has been ended.
     *
     * @return DateTime|null
     */
    public function getEndedAt(): ?DateTime
    {
        return $this->endedAt;
    }
    
    protected function setEndedAt(?string $endedAt): void
    {
        $this->endedAt = $endedAt ? new DateTime($endedAt) : null;
    }

    /**
     * Get the number of emails in the list.
     *
     * @return int
     */
    public function getNbEmails(): ?int
    {
        return $this->nbEmails;
    }

    protected function setNbEmails(?int $nbEmails): void
    {
        $this->nbEmails = $nbEmails;
    }

    /**
     * Get the number of invalid emails (based on the email format) in the list.
     *
     * @return int
     */
    public function getNbEmails_InvalidFormat(): ?int
    {
        return $this->nbEmails_InvalidFormat;
    }

    protected function setNbEmails_InvalidFormat(?int $nbEmailsInvalid): void
    {
        $this->nbEmails_InvalidFormat = $nbEmailsInvalid;
    }

    /**
     * Get the number of empty emails in the list.
     *
     * @return int
     */
    public function getNbEmails_Empty(): ?int
    {
        return $this->nbEmails_Empty;
    }

    protected function setNbEmails_Empty(?int $nbEmailsEmpty): void
    {
        $this->nbEmails_Empty = $nbEmailsEmpty;
    }

    /**
     * Get the number of duplicate emails in the list.
     *
     * @return int
     */
    public function getNbEmails_Duplicate(): ?int
    {
        return $this->nbEmails_Duplicate;
    }

    protected function setNbEmails_Duplicate(?int $nbEmailsDuplicate): void
    {
        $this->nbEmails_Duplicate = $nbEmailsDuplicate;
    }

    /**
     * Get the total cost of the list.
     *
     * @return int
     */
    public function getTotalCost(): ?int
    {
        return $this->totalCost;
    }

    protected function setTotalCost(?int $totalCost): void
    {
        $this->totalCost = $totalCost;
    }

}
