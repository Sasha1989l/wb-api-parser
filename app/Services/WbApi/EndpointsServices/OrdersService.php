<?php

namespace App\Services\WbApi\EndpointsServices;

use App\Services\WbApi\WbApiClient;
use Illuminate\Support\Facades\DB;

class OrdersService
{
    public static string $table = 'orders';

    public function __construct(
        private readonly WbApiClient $client
    ) {}

    public function load(): void
    {
        $this->client->load(
            'orders',
            function ($data) {
                DB::table(self::$table)->insert($data);
            }
        );
    }
}
