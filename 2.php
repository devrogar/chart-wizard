<?php
// Copy your client id and secret from Google developer console
// -----------------------------------------------------------------------------
// DO NOT EDIT BELOW THIS LINE
// -----------------------------------------------------------------------------
require 'vendor/autoload.php';
session_start();
$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/project/2.php');
$client->setScopes(array("https://spreadsheets.google.com/feeds"));

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    print_r(json_decode($client->getAccessToken(), true));
    exit;
}
print '<a href="' . $client->createAuthUrl() . '">Authenticate</a>';
?>