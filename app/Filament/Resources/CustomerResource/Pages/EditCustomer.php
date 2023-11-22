<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['employee_id'] = $data['user_id'];
        $additionalDetails = json_decode($data['additional_details']);
        $data['additional_details'] = $this->setAdditionalDetails($additionalDetails);
 
        return $data;
    }

    protected function setAdditionalDetails($data)
    {
        foreach ($data as $key => $row) {
            $data[$key] = (array) $row;
        }
    
        return $data;
    }
}
