<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleSearchConsoleService;

class SearchConsoleController extends Controller
{
    protected $searchConsoleService;

    public function __construct(GoogleSearchConsoleService $searchConsoleService)
    {
        $this->searchConsoleService = $searchConsoleService;
    }

    public function getAuthUrl()
    {
        $authUrl = $this->searchConsoleService->getAuthUrl();
        return response()->json(['auth_url' => $authUrl]);
    }

    public function authenticate(Request $request)
    {
        try {
            $authCode = $request->input('code');
            $scope = $request->input('scope');
            $this->searchConsoleService->fetchAccessTokenWithAuthCode($authCode, $scope);

            return response()->json(['message' => 'Authenticated successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }



    public function getNonIndexedPages(Request $request)
    {
        $siteUrl = 'https://puntos.yastas.com'; //$request->input('site_url');
        $pages = $this->searchConsoleService->getNonIndexedPages($siteUrl);
        return response()->json($pages);
    }
    public function getStatusIndexing(Request $request)
    {
        $pages = $this->searchConsoleService->getStatusIndexing();
        return response()->json($pages);
    }

    public function requestIndexing(Request $request)
    {
        $url = $request->input('url');
        $this->searchConsoleService->requestIndexing($url);
        return response()->json(['message' => 'Indexing requested']);
    }

    public function getUnindexedPages()
    {
        $siteUrl = 'https://puntos.yastas.com'; //'YOUR_SITE_URL'; // Cambia esto por tu URL.
        $searchConsole = $this->searchConsoleService;

        dd($siteUrl);
        try {
            $response = $searchConsole->urlInspection_index()->query([
                'siteUrl' => $siteUrl,
                'inspectionUrl' => $siteUrl,
                'categories' => ['INDEXING'],
            ]);

            $unindexedPages = [];

            foreach ($response->getInspectionResults() as $result) {
                if ($result->getIndexStatusResult()->getVerdict() === 'NOT_INDEXED') {
                    $unindexedPages[] = $result->getInspectionUrl();
                }
            }

            return response()->json($unindexedPages);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function indexing_example(){
        $response = $this->searchConsoleService->indexing_example();
        return response()->json(['response' => $response]);
    }
}
