<?php

namespace Oxemis\OxiBounce\Components;

use DateTime;
use Oxemis\OxiBounce\ApiClient;
use Oxemis\OxiBounce\ApiException;
use Oxemis\OxiBounce\Objects\EmailCheck;
use Oxemis\OxiBounce\Objects\EmailCheckResult;

/**
 * Class for https://api.oxibounce.com/doc/#/check
 */
class CheckAPI extends Component
{

    public function __construct(ApiClient $apiClient)
    {
        parent::__construct($apiClient);
    }

    /**
     * Allows you to run a check asynchronously.
     *
     * @param string $email The email you want to check (you can set multiple emails by separating them with a ";")
     * @return array<EmailCheck>         Check informations (please keep the ID in order to get results) - https://api.oxibounce.com/doc/#/check/post_check
     * @throws ApiException
     */
    public function runCheckAsync(string $email): array
    {
        $checks = $this->request("POST", "/check", ["email" => $email]);
        $result = [];
        foreach ($checks as $check) {
            $result[] = EmailCheck::mapFromStdClass($check);
        }
        return $result;
    }

    /**
     * Allows you to check the status and get the result of a test.
     *
     * @param array<EmailCheck> $emailChecks The checks you want the result
     * @return array<EmailCheckResult>              The status of the tests. See : https://api.oxibounce.com/doc/#/check/get_check
     * @throws ApiException
     */
    public function getCheckResultAsync(array $emailChecks): array
    {
        $ids = [];
        foreach ($emailChecks as $emailCheck) {
            $ids[] = $emailCheck->getId();
        }
        return $this->getCheckResultAsyncFromId(implode(";", $ids));
    }

    /**
     * Allows you to check the status and get the result of a test.
     *
     * @param string $id The ID of the test (you can check multiple IDs by separating them with a ;)
     * @return array<EmailCheckResult>              The status of the test. See : https://api.oxibounce.com/doc/#/check/get_check
     * @throws ApiException
     */
    public function getCheckResultAsyncFromId(string $id): array
    {
        $tests = $this->request("GET", "/check", ["id" => $id]);
        $result = [];

        // Mapping can't be done automatically cause the JSON structure has a sub part "ResultDetails"
        foreach ($tests as $test) {
            $ecr = EmailCheckResult::mapFromStdClass($test);
            $result[$test->Email] = $ecr;
        }

        return $result;
    }

    /**
     * This method is a wrapper for the async methods. It allows you to check synchronously an email
     * and handle a timeout.
     * Please note that if the timeout is reached you MAY have some tests with a "PENDING" status.
     * Some checks take time to complete because of the complexity of the tests.
     *
     * @param string $emails The emails you want to test (you can set multiple emails by separating them with a ";")
     * @param int $timeout The time allowed to check (in seconds). 0 means no timeout
     * @return array<EmailCheckResult>          The result of the tests. See : https://api.oxibounce.com/doc/#/check/get_check
     * @throws ApiException
     */
    public function checkEmails(string $emails, int $timeout = 0): array
    {

        $tests = $this->runCheckAsync($emails);
        $start = new DateTime();

        $done = false;
        $results = [];
        while (!$done) {

            // We need at least 1 second to make the test !
            sleep(1);

            // Get the results
            $results = $this->getCheckResultAsync($tests);

            // Check if all is done
            $done = true;
            foreach ($results as $result) {
                if ($result->getStatus() == "PENDING") {
                    // At least one pending job
                    $done = false;
                }
            }

            // If the jobs are not completed and a timeout is specified
            if (!$done && $timeout != 0) {
                $dif = (new DateTime())->getTimestamp() - $start->getTimestamp();
                if ($dif > $timeout) {
                    // Timeout is reached
                    // We consider that the job is complete
                    $done = true;
                }
            }

        }

        return $results;

    }

}
