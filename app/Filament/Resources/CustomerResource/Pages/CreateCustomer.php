<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use Exception;
use Filament\Actions;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CustomerResource;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        DB::beginTransaction();

        try {
            $customer = new Customer();
            $customer->user_id = $data['employee_id'];
            $customer->lead_source_id = $data['lead_source_id'];
            $customer->tag_id = $data['tag_id'];
            $customer->first_name = $data['first_name'];
            $customer->last_name = $data['last_name'];
            $customer->email = $data['email'];
            $customer->phone_number = $data['phone_number'];
            $customer->description = $data['description'];
            $customer->additional_details = json_encode($data['additional_details']);
            $customer->save();

            $customer->pipelineStages()->attach($data['pipeline_stage_id']);
            
            DB::commit();

            return $customer;
        } catch (Exception $e) {
            DB::rollBack();

            Log::debug($e->getMessage());

            return null;
        }
    } 
}
