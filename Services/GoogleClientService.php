<?php

namespace App\Services;

use Google\Client;
use Google\Service\SearchConsole;

class GoogleClientService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Your Laravel App');
        $this->client->setScopes([SearchConsole::WEBMASTERS_READONLY]);
        $this->client->setAuthConfig(config('google.credentials'));
        $this->client->useApplicationDefaultCredentials();
    }

    public function getClient()
    {
        return $this->client;
    }
}
