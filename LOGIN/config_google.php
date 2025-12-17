<?php
// config_google.php

// Pastikan path ke autoload benar. 
// Karena composer dijalankan di folder LOGIN, maka vendor ada di folder yang sama.
require_once 'vendor/autoload.php';

$clientID = '697550183776-sm3j6tkl1n3kuscq1qv01tb7jod9uuel.apps.googleusercontent.com'; 
$clientSecret = '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';
$redirectUri = 'http://localhost/Project/LOGIN/google_auth.php'; // Harus sama persis dengan Google Console

// Setup Google Client
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
?>