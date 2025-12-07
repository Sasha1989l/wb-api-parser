<?php

namespace App\Services\WbApi;

use Illuminate\Support\Facades\DB;

class OrdersService
{
    public $timestamps = false;

    public function __construct(
        private readonly WbApiClient $client
    ) {}

    public function load(): void
    {
        $this->client->getOrders(function ($data) {
            DB::table('orders')->insert($data);
        });
    }
}
