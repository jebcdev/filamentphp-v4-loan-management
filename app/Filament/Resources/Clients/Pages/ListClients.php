<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Enums\ClientStatus;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Icons\Heroicon;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $all = __('All');
        $active = __('Active');
        $inactive = __('Inactive');
        $blocked = __('Blocked');
        return [
            // Pestaña "Todas" - sin filtro, muestra todos los registros
            'all' => Tab::make($all)
                ->icon(Heroicon::OutlinedListBullet)
                ->badge(fn() => ClientResource::getModel()::count()),

            // Pestaña "Activas" - solo categorías activas
            'active' => Tab::make($active)
                ->icon(Heroicon::OutlinedCheckCircle)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', ClientStatus::active->value))
                ->badge(fn() => ClientResource::getModel()::where('status', ClientStatus::active->value)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make($inactive)
                ->icon(Heroicon::OutlinedPauseCircle)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', ClientStatus::inactive->value))
                ->badge(fn() => ClientResource::getModel()::where('status', ClientStatus::inactive->value)->count())
                ->badgeColor('warning'),

            'blocked' => Tab::make($blocked)
                ->icon(Heroicon::OutlinedXCircle)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', ClientStatus::blocked->value))
                ->badge(fn() => ClientResource::getModel()::where('status', ClientStatus::blocked->value)->count())
                ->badgeColor('danger'),


        ];
    }
}
