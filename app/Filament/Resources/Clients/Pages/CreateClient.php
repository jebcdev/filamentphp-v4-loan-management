<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

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
