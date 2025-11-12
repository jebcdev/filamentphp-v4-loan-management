<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['creator', 'updater']))
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->searchableAndSortable(),

                TextColumn::make('document_number')
                    ->label(__('Document Number'))
                    ->searchableAndSortable(),

                TextColumn::make('monthly_income')
                    ->label(__('Monthly Income'))
                    ->money('COP', locale: 'es_CO')
                    ->searchableAndSortable(),

                TextColumn::make('max_credit_limit')
                    ->label(__('Max Credit Limit'))
                    ->money('COP', locale: 'es_CO')
                    ->searchableAndSortable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn($state) => __($state))
                    ->searchable(),

                /* Columnas Ocultas */

                TextColumn::make('document_type')
                    ->label(__('Document Type'))
                    ->formatStateUsing(fn($state) => __($state))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('birth_date')
                    ->label(__('Birth Date'))
                    ->dateTooltip()
                    ->date()
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('d/m/Y'))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->formatStateUsing(fn($state) => __($state))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('occupation')
                    ->label(__('Occupation'))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('secondary_phone')
                    ->label(__('Secondary Phone'))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->label(__('Email Address'))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('address')
                    ->label(__('Address'))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('city')
                    ->label(__('City'))
                    ->searchableAndSortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('used_credit_limit')
                    ->label(__('Used Credit Limit'))
                    ->money('COP', locale: 'es_CO')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('available_credit_limit')
                    ->label(__('Available Credit Limit'))
                    ->money('COP', locale: 'es_CO')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label(__('Created By'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label(__('Updated By'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                /* Columnas Ocultas */

            ])
            ->filters([
                // Filtro para ver registros eliminados (soft deletes)
                TrashedFilter::make(),

            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make()->url(fn($record) => ClientResource::getUrl('view', ['record' => $record]))->label(__('View')),

                    EditAction::make()->label(__('Edit')),

                    \Filament\Actions\DeleteAction::make()->label(__('Delete')),

                    \Filament\Actions\RestoreAction::make()->label(__('Restore')),

                    \Filament\Actions\ForceDeleteAction::make()->label(__('Force Delete')),

                ]),

            ], position: \Filament\Tables\Enums\RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                // Acciones masivas agrupadas: eliminar, forzar eliminación,
                // restaurar. Se usan las implementaciones nativas de Filament.
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                ]),

            ]);
    }
}
