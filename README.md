# Data Reporting PHP SDK

- Collect your data via ACTE Technology secured gateway

## Installation

Install with composer:
```bash
composer require acte/data-report-sdk
```

# Configuration

- Contact ACTE support in order to receive username, password and authorized databases
- copy below code and edit configuration variables

## Example
```php
<?php
use ReportSdk\Connector;

// Configuration variables
$base_url = "https://example.com";
$username = "YOUR_USERNAME";
$password = "YOUR_PASSWORD";
$db = "DATA_DB"; # database

// init api class and connect
$api = new Connector($base_url);
$api->connect($username, $password);

// Check if the connection is successful
if ($api->check_connection()) {
    echo "Connected successfully.\n";
}

// get allowed databases
$databases = $api->getDatabases();
print_r($databases);
$db = $databases[0];

// get devices
$devices = $api->getDevices($db);
print_r($devices);
$device = $devices[0];

// get keys from device
$keys = $api->getKeys($db, $device);
print_r($keys);
$key = $keys[0];


// get telemetry data
//  prepare configuration parameters
date_default_timezone_set('UTC'); // Set the timezone to UTC
$to_ts = strtotime(date("Y-m-d", time()));
$from_ts = strtotime(date("Y-m-d", strtotime("-7 days")));

// TO DO:::
// $agg_type = "SUM"; // aggregation method: AVG | MIN | MAX | SUM
// $agg_interval = 3600; // aggregation interval in seconds

$data = $api->getTelemetry($db, $device, $key, $from_ts, $to_ts);
print_r($data);
```
