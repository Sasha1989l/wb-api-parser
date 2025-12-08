<?php

namespace App\Console\Commands;

use App\Services\WbApi\WbServiceRegistry;
use Illuminate\Console\Command;

class LoadWbData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-wb-data {endpoints?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load wb data from endpoints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $available = WbServiceRegistry::discover();
        $requestedEndpoints = $this->argument('endpoints');

        if (empty($requestedEndpoints)) {
            $this->info("Эндпоинты не указаны — загружаю все доступные.");
            $requestedEndpoints = array_keys($available);
        }

        foreach ($requestedEndpoints as $endpoint) {
            if (!isset($available[$endpoint])) {
                $this->error("Неизвестный endpoint '{$endpoint}'. Файл сервиса отсутствует.");
                continue;
            }

            $serviceClass = $available[$endpoint];
            $service = app($serviceClass);

            $this->info("Загружаю данные для '{$endpoint}'...");

            try {
                $service->load();
                $this->info("'{$endpoint}' успешно загружен.");
            } catch (\Throwable $e) {
                $this->error("Ошибка в '{$endpoint}': " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
