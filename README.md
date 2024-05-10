# Official OxiBounce PHP Wrapper

![MIT License](https://img.shields.io/badge/license-MIT-007EC7.svg?style=flat-square)
![Current Version](https://img.shields.io/badge/version-1.0.0-green.svg)

## Overview

This repository contains the official PHP wrapper for the OxiBounce API.
To get started, create an account and request your free credits on [this page](https://account.oxemis.com/)

This library is a wrapper for the [OxiBounce API](https://api.oxibounce.com) but you don't have to know how to use the API to get started with this library.

## What is OxiBounce ?

OxiBounce is an online service dedicated to the verification of emails addresses. It will not only validate that the 
address is valid but it will also check the **existence of the email**.

## Table of contents

- [Compatibility](#compatibility)
- [Installation](#installation)
- [Authentication](#authentication)
- [Getting information about your account](#getting-information-about-your-account)
- [Checking an email the best way](#checking-an-email-the-best-way)
- [Asynchronous check](#asynchronous-check)
- [Synchronous check](#synchronous-check)
- [What are the meaning of the EmailCheckResult properties ?](#what-are-the-meaning-of-the-emailcheckresult-properties-)
- [Contribute / Need help ?](#contribute--need-help-)

## Compatibility

This library requires **PHP v7.4** or higher.

## Installation

Use the below code to install the wrapper:

`composer require oxemis/oxibounce`

## Authentication

This library is a wrapper to the [OxiBounce API](https://api.oxibounce.com).
You can request an API KEY in your [OxiBounce Account](https://account.oxemis.com). Free credits are offered.

You should export your API_LOGIN and API_PASSWORD in order to use them in this library :

```bash
export OXIBOUNCE_API_LOGIN='your API login'
export OXIBOUNCE_API_PWD='your API password'
```

Initialize your **OxiBounce** Client:

```php
require_once 'vendor/autoload.php';
use \Oxemis\OxiBounce\OxiBounceClient;

// getenv will allow us to get the OXIBOUNCE_API_LOGIN/OXIBOUNCE_API_PWD variables we created before:

$apilogin = getenv('OXIBOUNCE_API_LOGIN');
$apipwd = getenv('OXIBOUNCE_API_PWD');

$oxibounce = new OxiBounceClient($apilogin, $apipwd);

// or, without using environment variables:

$apilogin = 'your API login';
$apipwd = 'your API password';

$oxibounce = new OxiBounceClient($apilogin, $apipwd);
```

## Getting information about your account
You will find all the information about your OxiBounce account with the "**UserAPI**" object.
Informations returned are documented in the class.

```php
require_once "vendor/autoload.php";
use \Oxemis\OxiBounce\OxiBounceClient;

$client = new OxiBounceClient(API_LOGIN,API_PWD);
$user = $client->userAPI->getUser();

echo "Account :" . $user->getEmail() . "\n" .
     "Remaining credits : " . $user->getCredits() . "\n";
```

## Checking an email the best way
OxiBounce allows you to check an email address with two methods :

- **Asynchronously** : this is the best method to get better results but it can take time to validate completly an address. Use this method for a background worker for instance. 
- **Synchronously** : this is the method you should prefer if you want to validate emails on a form (for example). You will be able to specify a "timeout" after which the verification method will stop in order to not block users. The more time you let, the better the results will be.


> **The complete validation of an email address can take time !**
>
> More than **20 different tests** are carried out, some of which depend on the performance of the mail servers of the
> addresses tested.
>
> We always do our best to return the result as quickly as possible (within seconds), but sometimes the whole test can
> take several minutes!
>
> **If you are limited by time, use the "Synchronous" method with an appropriate timeout**.

## Asynchronous check

The async method will require you to :

- Start a test that will return you an ID
- Periodically, check this ID to check if the test is "PENDING" or "DONE"

**Step 1 : run the test**

```php
require_once 'vendor/autoload.php';
use Oxemis\OxiBounce\OxiBounceClient;
use Oxemis\OxiBounce\Objects\EmailCheck;
use Oxemis\OxiBounce\Objects\EmailCheckResult;  

// Create the Client
$apilogin = 'your API login';
$apipwd = 'your API password';
$client = new OxiBounceClient($apilogin, $apipwd);

// Run the check
// You can specify multiple addresses separated with a ";" (up to 50)
// You will have to keep the returned array (one element by email tested) to get results.
$tests = $client->checkAPI->runCheckAsync("email1@example.com");
```

**Step 2 : get the status and results**
```php
// We'll have to check that all tests are carried out.
// Remember that tests can take minutes !
// Other option is to use the synchronous method (which handle a timeout), see next chapter !
$pending = true;
while ($pending) {

    // $tests is the array returned by runCheckAsync()
    $results = $client->checkAPI->getCheckResultAsync($tests);
    
    // Will be set to true below if some tests are still "PENDING"
    $pending = false;
    
    // Wait for results
    foreach ($results as $result) {
        if ($result->getStatus() == EmailCheck::STATUS_PENDING) 
            // Some tests are still pending
            $pending = true;
            // Waiting for 1 second
            sleep(1);
        }        
    }
    
} 

// Use the results
foreach ($results as $result) {
    switch ($result->getResult()) {
        case EmailCheckResult::RESULT_OK:
            // The address is valid.
            echo $result->getEmail() . " is valid !";
            break;
        case EmailCheckResult::RESULT_KO:
            // The address is invalid.
            echo $result->getEmail() . " is invalid !";
            break;
        case EmailCheckResult::RESULT_NOTSURE:
            // The tests did not reveal whether the address is valid or not.
            // Other properties ($result->isRisky() for example) can give you more
            // information to let you decide whether or not to authorize the address.
            echo $result->getEmail() . " is not sure !";
            break;
    }
}
```

## Synchronous check
The synchronous method manage a limit of time for the checks. 

Here is a simple sample :

```php
require_once 'vendor/autoload.php';
use Oxemis\OxiBounce\OxiBounceClient;
use Oxemis\OxiBounce\Objects\EmailCheck;
use Oxemis\OxiBounce\Objects\EmailCheckResult;  

// Create the Client
$apilogin = 'your API login';
$apipwd = 'your API password';
$client = new OxiBounceClient($apilogin, $apipwd);

// Run the check
// You can specify multiple addresses separated with a ";" (up to 50)
// The second parameter is the time out (in seconds).
$results = $client->checkAPI->checkEmails("email1@example.com", 10);

// Use the results
foreach ($results as $result) {
    if ($result->getStatus() == EmailCheckResult::STATUS_DONE) {
        // The test is done, use object properties
        switch ($result->getResult()) {
            case EmailCheckResult::RESULT_OK:
                // The address is valid.
                echo $result->getEmail() . " is valid !";
                break;
            case EmailCheckResult::RESULT_KO:
                // The address is invalid.
                echo $result->getEmail() . " is invalid !";
                break;
            case EmailCheckResult::RESULT_NOTSURE:
                // The tests did not reveal whether the address is valid or not.
                // Other properties ($result->isRisky() for example) can give you more
                // information to let you decide whether or not to authorize the address.
                echo $result->getEmail() . " is not sure !";
                break;
        }
    } else {
        // This test is not finished (but timeout is reached) 
        echo $result->getEmail() . " the test is still 'PENDING'.";
    }
}
```

> **What to do with "PENDING" checks (timeout reached) ?**
> 
> Short answer : consider them as "NOT_SURE".
> 
> You can also store there "ID" (`$result->getId()`) in order to get the results later 
> with the `$client->checkAPI->getCheckResultAsyncFromId()` method.

## What are the meaning of the `EmailCheckResult` properties ?

The `EmailCheckResult` structure contains a lot of informations about the test results.
Each one is documented as PHPDoc if you need to get a description in your IDE.

| Property          | Meaning                                                                                                                                                                                                                                                                                                            |
|-------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **Result**        | The result of the test. Can be RESULT_OK, RESULT_KO or RESULT_NOTSURE. For more details, see “Reason”.                                                                                                                                                                                                             |
| **Reason**        | The detailled reason of the result (many codes are availables, see [page 2 of this document](https://www.oxemis.com/docs/oxibounce_status_desc.pdf)).                                                                                                                                                              |
| **Domain**        | The domain of the email.                                                                                                                                                                                                                                                                                           |
| **IsFormatValid** | True if the email address has a valid format.                                                                                                                                                                                                                                                                      |
| **IsDisposable**  | Indicates that the address is a disposable address. Disposable addresses can be dangerous because they are not necessarily associated with a single recipient (it is sometimes enough to know the address to consult its contents). **Never send messages containing confidential information to such addresses**. |
| **IsFreeMail**    | Indicates that the address is a “free” address, therefore not associated with a particular company.                                                                                                                                                                                                                |
| **IsRisky**       | Indicates that the address or its domain is identified as potentially dangerous. **It is strongly recommended not to send to these addresses to preserve your reputation.**                                                                                                                                        |
| **IsRobot**       | Indicates that the e-mail address appears to be associated with a robot and not with a real person or a department of a company. **Behind some of these addresses are hidden spamtraps**. So be very careful.                                                                                                      |
| **IsRole**        | Indicates that the address is that of a department of a company or that it is a generic address (contact@…).                                                                                                                                                                                                       |
| **MailSystem**    | Contains, if identified, the email system used by the owner of the email address.                                                                                                                                                                                                                                  |
| **Suggestion**    | In case of an invalid address, OxiBounce will be able to suggest a correction (gmial.com at the link of gmail.com for example). **To be used with caution and manual validation of course**.                                                                                                                       |                                                                                                                       |


## Contribute / Need help ?

Feel free to ask anything, and contribute to this project.
Need help ? 👉 support@oxemis.com

