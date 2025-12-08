<?php

namespace App\Console\Commands;

use App\Services\WbApi\WbServiceRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearWbTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-wb-tables {endpoints?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear databases for endpoints data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $available = WbServiceRegistry::discover();
        $requestedEndpoints = $this->argument('endpoints');

        if (empty($requestedEndpoints)) {
            $this->info("Эндпоинты не указаны — будут очищены все таблицы.");
            $requestedEndpoints = array_keys($available);
        }

        $tablesToClear = [];

        foreach ($requestedEndpoints as $endpoint) {
            if (!isset($available[$endpoint])) {
                $this->error("Неизвестный endpoint '{$endpoint}'.");
                continue;
            }

            $class = $available[$endpoint];

            if (!property_exists($class, 'table')) {
                $this->error("Сервис {$class} не содержит статическое свойство \$table.");
                continue;
            }

            $tablesToClear[$endpoint] = $class::$table;
        }

        if (empty($tablesToClear)) {
            $this->warn("Нет таблиц для очистки.");
            return Command::SUCCESS;
        }

        $this->warn("Будут ОЧИЩЕНЫ следующие таблицы:");
        foreach ($tablesToClear as $endpoint => $table) {
            $this->line(" - {$endpoint}: {$table}");
        }

        $this->newLine();

        $confirm = $this->ask("Вы уверены? Введите 'yes' или 'y' для подтверждения");

        if (!in_array(strtolower($confirm), ['yes', 'y'])) {
            $this->info("Операция отменена.");
            return Command::SUCCESS;
        }

        foreach ($tablesToClear as $endpoint => $table) {
            try {
                DB::table($table)->truncate();
                $this->info("Таблица '{$table}' очищена ({$endpoint})");
            } catch (\Throwable $e) {
                $this->error("Ошибка очистки {$table}: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
