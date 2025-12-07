<?php

namespace App\Services\WbApi;

use Illuminate\Support\Facades\DB;

class StocksService
{
    public $timestamps = false;

    public function __construct(
        private readonly WbApiClient $client
    ) {}

    public function load(): void
    {
        $this->client->getStocks(function ($data) {
            DB::table('stocks')->insert($data);
        });
    }
}
