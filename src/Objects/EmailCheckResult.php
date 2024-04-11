<?php

namespace Oxemis\OxiBounce\Objects;

use stdClass;

/**
 * This class is used to help developpers.
 * It is mapped with the JSON returned by the API.
 */
class EmailCheckResult extends EmailCheck
{

    public const RESULT_OK = "OK";
    public const RESULT_KO = "KO";
    public const RESULT_NOTSURE = "NOT_SURE";

    private string $result;
    private string $domain;
    private bool $isDisposable;
    private bool $isFormatValid;
    private bool $isFreemail;
    private bool $isRisky;
    private bool $isRobot;
    private bool $isRole;
    private string $mailSystem;
    private string $reason;
    private string $suggestion;

    /**
     * Get the result of the test. Can be RESULT_OK, RESULT_KO or RESULT_NOTSURE
     * The main test result (OK, KO, NOTSURE). For more details, see
     * “ResultDetail”.
     * OK : The various tests performed by OxiBounce seem to indicate
     * that the address is valid and functional.
     * KO : The tests indicate that the address does not work (or no longer
     * works) and that it is not able to receive messages. This code is also
     * returned when the address is classified as “dangerous” (see the
     * associated detailed code for more information).
     * NOTSURE : The tests carried out do not make it possible to know if
     * the address works or not without carrying out a real sending. The
     * detailed code gives more indication of the reason for this
     * uncertainty.
     *
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    protected function setResult(string $result): void
    {
        $this->result = $result;
    }

    /**
     * Get the domain associated to the email address.
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    protected function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * Indicates that the address is a disposable address. Disposable
     * addresses can be dangerous because they are not necessarily
     * associated with a single recipient (it is sometimes enough to know
     * the address to consult its contents). Never send messages
     * containing confidential information to such addresses.
     *
     * @return bool
     */
    public function isDisposable(): bool
    {
        return $this->isDisposable;
    }

    protected function setIsDisposable(bool $isDisposable): void
    {
        $this->isDisposable = $isDisposable;
    }

    /**
     * Indicates whether the address format is valid or not.
     *
     * @return bool
     */
    public function isFormatValid(): bool
    {
        return $this->isFormatValid;
    }

    protected function setIsFormatValid(bool $isFormatValid): void
    {
        $this->isFormatValid = $isFormatValid;
    }

    /**
     * Indicates that the address is a “free” address, therefore not
     * associated with a particular company.
     *
     * @return bool
     */
    public function isFreemail(): bool
    {
        return $this->isFreemail;
    }

    protected function setIsFreemail(bool $isFreemail): void
    {
        $this->isFreemail = $isFreemail;
    }

    /**
     * Indicates that the address or its domain is identified as potentially
     * dangerous. It is strongly recommended not to send to these
     * addresses to preserve your reputation.
     *
     * @return bool
     */
    public function isRisky(): bool
    {
        return $this->isRisky;
    }

    protected function setIsRisky(bool $isRisky): void
    {
        $this->isRisky = $isRisky;
    }

    /**
     * Indicates that the e-mail address appears to be associated with a
     * robot and not with a real person or a department of a company.
     * Behind some of these addresses are hidden spamtraps. So be very
     * careful.
     *
     * @return bool
     */
    public function isRobot(): bool
    {
        return $this->isRobot;
    }

    protected function setIsRobot(bool $isRobot): void
    {
        $this->isRobot = $isRobot;
    }

    /**
     * Indicates that the address is that of a department of a company or
     * that it is a generic address (type contact@…).
     *
     * @return bool
     */
    public function isRole(): bool
    {
        return $this->isRole;
    }

    protected function setIsRole(bool $isRole): void
    {
        $this->isRole = $isRole;
    }

    /**
     * Contains, if identified, the email system used by the owner of the
     * email address.
     *
     * @return string
     */
    public function getMailSystem(): string
    {
        return $this->mailSystem;
    }

    protected function setMailSystem(string $mailSystem): void
    {
        $this->mailSystem = $mailSystem;
    }

    /**
     * The detailed reason for the result. Many codes are possibles.
     * See : https://www.oxemis.com/docs/oxibounce_status_desc.pdf
     *
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    protected function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    /**
     * In case of an invalid address, OxiBounce will be able to suggest a
     * correction (gmial.com at the link of gmail.com for example). To be
     * used with caution and manual validation of course.
     *
     * @return string
     */
    public function getSuggestion(): string
    {
        return $this->suggestion;
    }

    protected function setSuggestion(string $suggestion): void
    {
        $this->suggestion = $suggestion;
    }

    /** Mapping can't be done automatically cause the JSON structure has a sub part "ResultDetails" */
    protected function myMapFromStdClass(stdClass $test)
    {

        $this->setId($test->ID);
        $this->setEmail($test->Email);
        $this->setStatus($test->Status);
        if ($test->Status == "DONE") {
            $this->setResult($test->Result);
            $this->setDomain($test->ResultDetails->Domain);
            $this->setIsDisposable($test->ResultDetails->IsDisposable);
            $this->setIsRisky($test->ResultDetails->IsRisky);
            $this->setIsFormatValid($test->ResultDetails->IsFormatValid);
            $this->setIsRobot($test->ResultDetails->IsRobot);
            $this->setIsFreemail($test->ResultDetails->IsFreemail);
            $this->setMailSystem($test->ResultDetails->MailSystem);
            $this->setReason($test->ResultDetails->Reason);
            $this->setSuggestion($test->ResultDetails->Suggestion);
        }

    }

}
