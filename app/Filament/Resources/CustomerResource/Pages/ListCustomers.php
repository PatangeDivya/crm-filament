<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CustomerResource;
use App\Models\PipelineStage;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('All Customers')->badge($this->getModel()::count())];
 
        $pipelineStages = PipelineStage::withCount('customers')
            ->get();
 
        foreach ($pipelineStages as $pipelineStage) {
            $name = $pipelineStage->name;
            $slug = str($name)->slug()->toString();
 
            $tabs[$slug] = Tab::make($name)
                ->badge($pipelineStage->customers_count)
                ->modifyQueryUsing(function ($query) use ($pipelineStage) {
                    return $query->where('pipeline_stage_id', $pipelineStage->id);
                });
        }
 
        return $tabs;
    }
}
