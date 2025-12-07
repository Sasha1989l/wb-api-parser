<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\WbApi\{
    SalesService,
    OrdersService,
    StocksService,
    IncomesService
};

class LoadWbData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-wb-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load wb data from sales, orders, stocks, incomes';

    /**
     * Execute the console command.
     */
    public function handle(
        SalesService $sales,
        OrdersService $orders,
        StocksService $stocks,
        IncomesService $incomes
    )
    {
        $this->info('Loading data started...');

        $sales->load();
        $this->info('Sales loaded');

        $orders->load();
        $this->info('Orders loaded');

        $stocks->load();
        $this->info('Stocks loaded');

        $incomes->load();
        $this->info('Incomes loaded');

        $this->info('Completed.');
    }
}
