<?php

namespace App\Services;

use Exception;
use Google_Client;
use Google_Service_Webmasters;
use Google_Service_Indexing;
use Google_Service_Indexing_UrlNotification;
use Illuminate\Support\Facades\Storage;

// use Google\Client;
// use Google\Service\SearchConsole;
// use Exception;

class GoogleSearchConsoleService
{
    protected $client;
    protected $webmasters;
    protected $indexing;

    public function __construct()
    {
        $this->client = new Google_Client();

        $this->client->setApplicationName(config('google.application_name'));
        $this->client->setAuthConfig(config('google.credentials_path'));
        $this->client->setScopes(config('google.scopes'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        $this->webmasters = new Google_Service_Webmasters($this->client);

    }


    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function fetchAccessTokenWithAuthCode($authCode, $scope)
    {
        // dd($authCode);
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
        // dd($accessToken);
        Storage::put('google/token.json', json_encode($accessToken));
        // Storage::put(config('google.token_path'), json_encode($accessToken));
    }

    public function getNonIndexedPages($siteUrl)
    {
        // Aquí debes implementar la lógica para obtener las URLs no indexadas
        // utilizando la API de Google Search Console. Como no hay una API directa para esto,
        // podrías necesitar hacer una lista de tus URLs y verificar su estado una por una.

        // Aquí solo se muestra un ejemplo simplificado.
        $urlsToCheck = ['/baja-california-norte/tijuana/terrazas-del-valle-2a-seccion/abarrotes-y-tortilleria-la-esperanza'];

        $nonIndexedPages = [];

        $response = $this->webmasters->urlInspectionIndex->inspect($siteUrl.'/baja-california-norte/tijuana/terrazas-del-valle-2a-seccion/abarrotes-y-tortilleria-la-esperanza');
        dd($response);

        foreach ($urlsToCheck as $url) {
            try {
                // Aquí no podemos usar directamente `urlInspectionIndex`, así que debemos usar otra forma de verificar
                // Esto es un ejemplo y podría necesitar ajuste según tus necesidades



                $response = $this->webmasters->SearchAnalyticsQueryRequest->query($siteUrl, [
                    'startDate' => '2023-01-01',
                    'endDate' => '2023-12-31',
                    'dimensions' => ['page'],
                    'filters' => [
                        [
                            'dimension' => 'page',
                            'operator' => 'equals',
                            'expression' => $url
                        ]
                    ]
                ]);

                if (empty($response->getRows())) {
                    $nonIndexedPages[] = $url;
                }
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }

        // foreach ($urlsToCheck as $url) {
        //     try {
        //         $response = $this->webmasters->urlInspectionIndex->inspect($url);
        //         if ($response->inspectionResult->indexStatusResult->coverageState !== 'INDEXED') {
        //             dd('URL no indexada: ' . $url);
        //             $nonIndexedPages[] = $url;
        //         }
        //     } catch (\Exception $e) {
        //         // Manejar errores si es necesario
        //         dd($e->getMessage());
        //     }
        // }

        return $nonIndexedPages;
    }

    // Send single indexing status
    public function getStatusIndexing()
    {
        try {
            // $tokenPath = config('google.token_path');
            // Storage::put('google/token.json', json_encode($accessToken));
            // dd(Storage::get('google/token.json'));
            $tokenPath = 'google/token.json';
            // dd(Storage::get($tokenPath));
            if (Storage::exists($tokenPath)) {
                $accessToken = json_decode(Storage::get($tokenPath), true);
                $this->client->setAccessToken($accessToken);
            }

            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    Storage::put($tokenPath, json_encode($newAccessToken));
                } else {
                    throw new Exception('Authorization required. Please visit the authorization URL and provide the verification code.');
                }
            }

            $url = ["url" => "https://puntos.yastas.com/jalisco/tlajomulco-de-zuniga/tlajomulco-centro/abarrotes-chayito"];
            $result = $this->indexing->urlNotifications->getMetadata( $url );
            dd($result);

        } catch (Exception $e) {
            dd($e->getMessage());
        }



    }

    public function requestIndexing($url)
    {
        try {
            $tokenPath = 'google/token.json';
            // dd(Storage::get($tokenPath));
            if (Storage::exists($tokenPath)) {
                $accessToken = json_decode(Storage::get($tokenPath), true);
                $this->client->setAccessToken($accessToken);
                $this->indexing = new Google_Service_Indexing($this->client);
            }

            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    Storage::put($tokenPath, json_encode($newAccessToken));
                } else {
                    throw new Exception('Authorization required. Please visit the authorization URL and provide the verification code.');
                }
            }

            // $url = ["url" => "https://puntos.yastas.com/jalisco/tlajomulco-de-zuniga/tlajomulco-centro/abarrotes-chayito"];
            // $result = $this->indexing->urlNotifications->getMetadata( $url );

            $urlNotification = new Google_Service_Indexing_UrlNotification([
                'url' => "https://puntos.yastas.com/jalisco/tlajomulco-de-zuniga/tlajomulco-centro/abarrotes-chayito",
                'type' => 'URL_UPDATED'
            ]);

            $result = $this->indexing->urlNotifications->publish( $urlNotification );

            dd($result);

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }


    public function indexing_example()
    {
        // dd(config('google.credentials_path'));
        $client = new Google_Client();
        $client->setAuthConfig(config('google.credentials_path'));
        $client->addScope('https://www.googleapis.com/auth/indexing');
        $tokenPath = 'google/token.json';
        $accessToken = json_decode(Storage::get($tokenPath), true);
        $client->setAccessToken($accessToken);
        $httpClient = $client->authorize();
        $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

        $content = '{
            "url": "https://puntos.yastas.com/jalisco/tlajomulco-de-zuniga/tlajomulco-centro/abarrotes-chayito",
            "type": "URL_UPDATED"
        }';

        $response = $httpClient->post($endpoint, [ 'body' => $content ]);
        $status_code = $response->getStatusCode();

        dd($response->getBody()->getContents());
    }
}
