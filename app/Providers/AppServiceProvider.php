<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Asegura que el timezone de PHP coincida con config('app.timezone')
        // Esto hace que las funciones de fecha de PHP y Carbon usen el timezone configurado
        // incluso si el sistema/php.ini tiene un valor por defecto diferente
        date_default_timezone_set(config('app.timezone'));

        // Macro para TextColumn que combina searchable y sortable
        \Filament\Tables\Columns\TextColumn::macro('searchableAndSortable', function () {
            /** @var \Filament\Tables\Columns\TextColumn $this */
            return $this->searchable()->sortable();
        });

        // Macro para IconColumn que combina searchable y sortable
        \Filament\Tables\Columns\IconColumn::macro('searchableAndSortable', function () {
            /** @var \Filament\Tables\Columns\IconColumn $this */
            return $this->searchable()->sortable();
        });
    }
}
