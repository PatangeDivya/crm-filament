<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use Exception;
use Filament\Actions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CustomerResource;

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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();

        try {
            $record->user_id = $data['employee_id'];
            $record->first_name = $data['first_name'];
            $record->last_name = $data['last_name'];
            $record->email = $data['email'];
            $record->phone_number = $data['phone_number'];
            $record->description = $data['description'];
            $record->lead_source_id = $data['lead_source_id'];
            $record->tag_id = $data['tag_id'];
            $record->additional_details = json_encode($data['additional_details']);
            $record->save();
        
            $record->pipelineStages()->sync($data['pipeline_stage_id']);

            DB::commit();

            return $record;
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            DB::rollBack();

            return null;
        }
    }
}
