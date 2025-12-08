<?php

namespace App\Services\WbApi\EndpointsServices;

use App\Services\WbApi\WbApiClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StocksService
{
    public static string $table = 'stocks';

    public function __construct(
        private readonly WbApiClient $client
    ) {}

    public function load(): void
    {
        $this->client->load(
            'stocks',
            function ($data) {
                DB::table(self::$table)->insert($data);
            },
            [
                'dateFrom' => Carbon::now()->toDateString(),
                'dateTo' => '',
            ]
        );
    }
}
