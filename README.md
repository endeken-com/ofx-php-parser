# OFX PHP Parser
This project consists of a PHP parser for OFX (Open Financial Exchange) files, implemented using PHP 8.2. Our aim is to make the process of importing OFX files as straightforward and hassle-free as possible.

[![Build Status](https://scrutinizer-ci.com/g/endeken-com/ofx-php-parser/badges/build.png?b=main)](https://scrutinizer-ci.com/g/endeken-com/ofx-php-parser/build-status/main)
[![Latest Stable Version](https://img.shields.io/github/release/endeken/ofx-php-parser.svg)](https://packagist.org/packages/endeken/ofx-php-parser)
[![Code Coverage](https://scrutinizer-ci.com/g/endeken-com/ofx-php-parser/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/endeken/ofx-php-parser/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/endeken-com/ofx-php-parser/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/endeken/ofx-php-parser/?branch=master)
[![Downloads](https://img.shields.io/packagist/dt/endeken/ofx-php-parser.svg)](https://packagist.org/packages/endeken/ofx-php-parser)
[![Downloads](https://img.shields.io/badge/license-MIT-brightgreen.svg)](./LICENSE)


## Installation
Simply require the package using [Composer](https://getcomposer.org/):

```bash
$ composer require endeken/ofx-php-parser
```

## Usage
This project primarily revolves around the `OFX` class in the `Endeken\OFX` namespace. This class provides a static function `parse()` which is used to parse OFX data and return the parsed information. Here is a basic usage example:
```php
<?php

require 'vendor/autoload.php';

use Endeken\OFX;

try {
    // Load the OFX data
    $ofxData = file_get_contents('path_to_your_ofx_file.ofx');

    // Parse the OFX data
    $parsedData = OFX::parse($ofxData);

    // $parsedData is an instance of OFXData which gives you access to all parsed data

    // Access the sign-on status code
    $statusCode = $parsedData->signOn->status->code;

    // Accessing bank accounts data
    $bankAccounts = $parsedData->bankAccounts;
    foreach($bankAccounts as $account) {
        echo 'Account ID: ' .$account->accountNumber . PHP_EOL;
        echo 'Bank ID: ' .$account->routingNumber . PHP_EOL;

        // Loop through each transaction
        foreach ($account->statement->transactions as $transaction) {
            echo 'Transaction Type: ' . $transaction->type . PHP_EOL;
            echo 'Date: ' . $transaction->date . PHP_EOL;
            echo 'Amount: ' . $transaction->amount . PHP_EOL;
        }
    }

} catch (Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}
```

## Acknowledgements

This library is a standalone project, however it is heavily influenced by the work done in the [asgrim/ofxparser](https://github.com/asgrim/ofxparser) which itself is a fork of [grimfor/ofxparser](https://github.com/grimfor/ofxparser). We would like to acknowledge the contributions made by the developers of these projects. Our intent was not to simply fork the project, but to build upon their work while taking the library in a slightly different direction to better serve our purposes.
