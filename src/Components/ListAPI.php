<?php

namespace Oxemis\OxiBounce\Components;

use DateTime;
use Oxemis\OxiBounce\OxiBounceClient;
use Oxemis\OxiBounce\Objects\EmailList;
use Oxemis\OxiBounce\OxiBounceException;
use Oxemis\OxiBounce\Objects\EmailCheckResult;

/**
 * Class for https://api.oxibounce.com/doc/#/list
 */
class ListAPI extends Component
{

    public function __construct(OxiBounceClient $apiClient)
    {
        parent::__construct($apiClient);
    }

    /**
     * Get all the lists.
     *
     * @return array<EmailList>        Check informations (please keep the ID in order to get results) - https://api.oxibounce.com/doc/#/check/post_check
     * @throws OxiBounceException
     */
    public function getLists(): array
    {
        $lists = $this->request("GET", "/list");
        foreach ($lists as $list) {
            $result[] = EmailList::mapFromStdClass($list);
        }
        return $result;
    }

    /**
     * Get a list by its ID.
     *
     * @param string $id                The ID of the list
     * @return EmailList                The list informations
     * @throws OxiBounceException
     */
    public function getList(string $id): EmailList
    {
        $list = $this->request("GET", "/list/$id");
        return EmailList::mapFromStdClass($list);
    }

    /**
     * Add a list.
     *
     * @param string $jsonFile          The path to the JSON file
     * @return EmailList                The list informations
     * @throws OxiBounceException
     */
    public function addList(string $jsonFile): EmailList{
        $result = $this->request("POST", "/list", null, null, $jsonFile, null, "file");
        return EmailList::mapFromStdClass($result);
    }

    /**
     * Delete a list by its ID.
     *
     * @param string $id                The ID of the list
     * @return bool                     True if the list has been deleted
     * @throws OxiBounceException
     */
    public function deleteList(string $id): bool
    {
        $result = $this->request("DELETE", "/list/$id");
        return $result->Code == 200;
    }

    /**
     * Get the results of a list.
     *
     * @param string $id                The ID of the list
     * @param string|null $filterOn     Filter the results on OK, KO or NOT_SURE (use the EmailCheckResult::RESULT_* constants)
     * @return array<EmailCheckResult>  The results of the list
     * @throws OxiBounceException
     */
    public function getListResults(string $id, ?string $filterOn = null): array
    {

        // Check that the list exists and is DONE or CANCELED
        $list = $this->getList($id);
        if (($list->getStatus() != EmailList::STATUS_DONE)  && ($list->getStatus() != EmailList::STATUS_CANCELED)) {
            throw new OxiBounceException("The list must be DONE or CANCELED");
        }

        $parameters = [];
        if ($filterOn) {
            if ($filterOn != EmailCheckResult::RESULT_OK && $filterOn != EmailCheckResult::RESULT_KO && $filterOn != EmailCheckResult::RESULT_NOTSURE) {
                throw new OxiBounceException("Filter must be OK, KO or NOT_SURE");
            }
            $parameters["filter"] = $filterOn;
        }
        
        $results = $this->request("GET", "/list/$id/results", $parameters);
        $listResults = [];
        if ($results) {
            foreach ($results as $result) {
                if ($result->result) {
                    $listResults[] = EmailCheckResult::mapFromStdClass($result);
                }
            }    
        }
        return $listResults;

    }

    /**
     * Start a list.
     *
     * @param string $id                The ID of the list
     * @return bool                     True if the list has been started
     * @throws OxiBounceException
     */
    public function startList(string $id): bool
    {
        $result = $this->request("POST", "/list/$id/start");
        return $result->Code == 200;
    }

    /**
     * Stop a list.
     *
     * @param string $id                The ID of the list
     * @return bool                     True if the list has been stopped
     * @throws OxiBounceException
     */
    public function stopList(string $id): bool
    {
        $result = $this->request("POST", "/list/$id/stop");
        return $result->Code == 200;
    }

}
