<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\GoogleClientService;
use Google\Service\SearchConsole;

class SearchConsoleController extends Controller
{
    protected $googleClientService;

    public function __construct(GoogleClientService $googleClientService)
    {
        $this->googleClientService = $googleClientService;
    }

    public function getUnindexedPages()
    {
        $siteUrl = 'https://puntos.yastas.com'; // Reemplaza con tu URL
        $searchConsole = new SearchConsole($this->googleClientService->getClient());

        try {
            $response = $searchConsole->urlInspectionIndex->batchGet([
                'inspectionUrl' => $siteUrl,
                'siteUrl' => $siteUrl,
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
}
