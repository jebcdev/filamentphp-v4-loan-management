<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\ViewClient;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Schemas\ClientInfolist;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $recordTitleAttribute = 'full_name';

    /*  Inicio de Personalización  */

    /**
     * Obtiene la etiqueta de navegación para el recurso.
     *
     * Esta etiqueta se muestra en el menú lateral del panel de Filament.
     * Se utiliza para identificar el recurso en la navegación.
     *
     * @return string La etiqueta de navegación traducida.
     */
    public static function getNavigationLabel(): string
    {
        // Define el nombre en singular para la navegación lateral
        $text=__('Clients');
        return $text;
    }

    // Opcional: Cambiar los nombres usados en los títulos y Breadcrumbs
    /**
     * Obtiene la etiqueta del modelo en singular.
     *
     * Se utiliza en títulos como "Crear [Modelo]" y en breadcrumbs.
     *
     * @return string La etiqueta del modelo traducida.
     */
    public static function getModelLabel(): string
    {
        $text=__('Client');
        return $text; // Usado en 'Crear'
    }

    /**
     * Obtiene la etiqueta del modelo en plural.
     *
     * Se utiliza en títulos como "Lista de [Modelos]" y en navegación.
     *
     * @return string La etiqueta del modelo plural traducida.
     */
    public static function getPluralModelLabel(): string
    {
        $text=__('Clients');
        return $text; // Usado en el título principal 'Lista de ...'
    }

    /**
     * Obtiene el grupo de navegación para el recurso.
     *
     * Los recursos se agrupan en el menú lateral bajo este grupo.
     *
     * @return string|null El nombre del grupo de navegación traducido.
     */
    public static function getNavigationGroup(): ?string
    {
        $text=__('Clients Management');
        return $text;
    }

    /**
     * Obtiene el orden de clasificación del grupo de navegación.
     *
     * Determina el orden en que aparecen los grupos en el menú lateral.
     * Números más bajos aparecen primero.
     *
     * @return int|null El orden de clasificación del grupo.
     */
    public static function getNavigationGroupSort(): ?int
    {

        return 1;
    }

    /**
     * Obtiene el orden de clasificación del recurso dentro de su grupo.
     *
     * Determina el orden en que aparecen los recursos dentro de un grupo.
     * Números más bajos aparecen primero.
     *
     * @return int|null El orden de clasificación del recurso.
     */
    public static function getNavigationSort(): ?int
    {

        return 1;
    }

    /**
     * Obtiene el badge de navegación para el recurso.
     *
     * Muestra un contador o indicador junto al nombre del recurso en el menú.
     *
     * @return string|null El valor del badge (generalmente un conteo).
     */
    public static function getNavigationBadge(): ?string
    {
         return static::getModel()::count();

    }

    /**
     * Obtiene el tooltip del badge de navegación.
     *
     * Proporciona información adicional cuando se pasa el mouse sobre el badge.
     *
     * @return string|null El texto del tooltip traducido.
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        $text=__('Total Clients Registered');
        return $text;
    }

    /*  Fin de Personalización  */

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
