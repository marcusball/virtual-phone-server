<?php

// Check the environment variable to determine whether this is in production or development mode. 
define('IS_PRODUCTION', (isset($_SERVER['APP_IS_PRODUCTION']) && $_SERVER['APP_IS_PRODUCTION'] == '1'));

if (!IS_PRODUCTION) {
    // Error reporting for development
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Timezone
date_default_timezone_set('America/New_York');

// Settings
$settings = [];

// Path settings
$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';

// Leave OVERWRITE_BASE_URL empty to have the app use the request URL when constructing new URLs.
// Otherwise, set to a full base host URL to use that for constructing new URLs. 
// For example, if the server is running on 'http://localhost:8888/', but you want to use
// a proxy for incoming requests, set this value to 'https://your-proxy-domain.com/' to use that domain for constructing URLs. 
$settings['overwrite_base_url'] = isset($_SERVER['OVERWRITE_BASE_URL']) ? $_SERVER['OVERWRITE_BASE_URL'] : '';

// Error Handling Middleware settings
$settings['error'] = [
    // Should be set to false in production
    'display_error_details' => !IS_PRODUCTION,

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,

    // Display error details in error log
    'log_error_details' => true,
];

$settings['logger'] = [
    // The name of the logger
    'name' => 'app',
];

$dbopts = parse_url($_SERVER['DATABASE_URL']);

// Database settings
$settings['db'] = [
    'driver'   => 'pgsql',
    'host'     => $dbopts['host'],
    'username' => $dbopts['user'],
    'database' => ltrim($dbopts["path"],'/'),
    'password' => $dbopts['pass'],
    'charset'  => 'UTF8',
    'flags'    => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => false,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];

$settings['twilio'] = [
    'sid' => $_SERVER['TWILIO_SID'],
    'token' => $_SERVER['TWILIO_TOKEN'],
];

return $settings;