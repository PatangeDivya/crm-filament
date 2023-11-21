<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $role = Role::find($data['role'][0]);
        $record->assignRole($role->name);

        return $record;
    }
}
