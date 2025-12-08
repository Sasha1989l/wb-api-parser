<?php

namespace App\Services\WbApi;

use Symfony\Component\Finder\Finder;
use Illuminate\Support\Str;

class WbServiceRegistry
{
    public static function discover(): array
    {
        $services = [];

        $path = app_path('Services/WbApi/EndpointsServices');

        $files = Finder::create()
            ->files()
            ->in($path)
            ->name('*Service.php');

        foreach ($files as $file) {
            $className = $file->getFilenameWithoutExtension();
            $endpoint = Str::of($className)
                ->replace('Service', '')
                ->lower()
                ->toString();

            $class = "App\\Services\\WbApi\\EndpointsServices\\{$className}";

            $services[$endpoint] = $class;
        }

        return $services;
    }
}
