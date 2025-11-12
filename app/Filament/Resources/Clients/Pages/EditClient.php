<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
 * Método para redirigir al index después de una acción.
 *
 * Sobrescribe este método en páginas de Create o Edit para cambiar el comportamiento por defecto.
 *
 * @return string URL de redirección
 */
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
