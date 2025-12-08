<?php

namespace App\Services\WbApi\EndpointsServices;

use App\Services\WbApi\WbApiClient;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public static string $table = 'sales';

    public function __construct(
        private readonly WbApiClient $client
    ) {}

    public function load(): void
    {
        $this->client->load(
            'sales',
            function ($data) {
                DB::table(self::$table)->insert($data);
            }
        );
    }
}
