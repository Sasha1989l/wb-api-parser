<?php

namespace App\Services\WbApi;

use App\Exceptions\ProblemWithUrl;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WbApiClient
{
    private string $currentDate;

    private string $baseUrl;
    private string $key;
    private string $dateFrom;
    private string $dateTo;

    public function __construct()
    {
        $this->currentDate = Carbon::now()->toDateString();
        $this->baseUrl = config('wbparser.base_url');
        $this->key = config('wbparser.key');
        $this->dateFrom = '2010-01-01';
        $this->dateTo = $this->currentDate;
    }

    private function fetchPage(string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $defaultParams = [
            'key' => $this->key,
            'page' => 1,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ];
        $params = array_merge($defaultParams, $params);

        $response = Http::retry(
            times: 3,
            sleepMilliseconds: 30000,
            when: function ($exception, $request) use ($url) {
                Log::warning('A request to this address {url} returned a response with code 429', ['url' => $url]);
                return $exception->getCode() === 429;
            }
        )->get($url, $params);
        $responseData = $response->json();

        if (empty($responseData['data'])  || !$response->ok()) {
            Log::emergency('There was a problem with the url: {url}', ['url' => $url]);
            throw new ProblemWithUrl('There was a problem with the url: ' . $url);
        }

        return ['data' => $responseData['data'], 'meta' => $responseData['meta']];
    }

    public function load(string $endpoint, callable $callback, array $params = [], ): void
    {
        Log::info('The request to the endpoint has been started: {endpoint}', ['endpoint' => $endpoint]);

        $page = 1;

        do {
            $params['page'] = $page;
            Log::info('Making a request To page: {page}', ['page' => $page]);

            $response = $this->fetchPage($endpoint, $params);
            if (empty($response['data'])) {
                Log::info('Page don\'t have results: {page}', ['page' => $page]);
                break;
            }
            $callback($response['data']);

            $lastPage = (int)$response['meta']['last_page'];
            $page++;

        } while ($page <= $lastPage);


        Log::info(
            'Requests to the address have been completed: {endpoint}.',
            ['endpoint' => $endpoint]
        );
    }
}
