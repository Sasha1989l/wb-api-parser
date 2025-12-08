<?php

namespace App\Services\WbApi;

use Illuminate\Support\Facades\DB;

class IncomesService
{
    public function __construct(
        private readonly WbApiClient $client
    ) {}

    public function load(): void
    {
        $this->client->getIncomes(function ($data) {
            DB::table('incomes')->insert($data);
        });
    }
}
