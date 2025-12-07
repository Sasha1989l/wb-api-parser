<?php

namespace App\Services\WbApi;

use Illuminate\Support\Facades\DB;

class SalesService
{
    public $timestamps = false;

    public function __construct(
        private readonly WbApiClient $client
    ) {}

    public function load(): void
    {
        $this->client->getSales(function ($data) {
            DB::table('sales')->insert($data);
        });
    }
}
