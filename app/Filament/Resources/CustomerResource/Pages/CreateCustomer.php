<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $customer = new Customer();
        $customer->user_id = $data['employee_id'];
        $customer->lead_source_id = $data['lead_source_id'];
        $customer->tag_id = $data['tag_id'];
        $customer->pipeline_stage_id = $data['pipeline_stage_id'];
        $customer->first_name = $data['first_name'];
        $customer->last_name = $data['last_name'];
        $customer->email = $data['email'];
        $customer->phone_number = $data['phone_number'];
        $customer->description = $data['description'];
        $customer->additional_details = json_encode($data['additional_details']);
        $customer->save();
        
        return $customer;
    } 
}
