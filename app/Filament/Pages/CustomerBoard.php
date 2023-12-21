<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\PipelineStage;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use InvadersXX\FilamentKanbanBoard\Pages\FilamentKanbanBoard;

class CustomerBoard extends FilamentKanbanBoard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public bool $sortable = true;

    public bool $sortableBetweenStatuses = true;

    protected function statuses() : Collection
    {
        return PipelineStage::all()
            ->map(function (PipelineStage $pipelineStage) {
                return [
                    'id' => $pipelineStage->id,
                    'title' => $pipelineStage->name
                ];
            });
    }

    protected function records() : Collection
    {
        return Customer::all()
            ->map(function (Customer $customer) {
                return [
                    'id' => $customer->id,
                    'title' => $customer->first_name . ' ' . $customer->last_name,
                    'status' => $customer->pipelineStages()->first() ? $customer->pipelineStages()->first()->id : '',
                ];
            });
    }

    public function onStatusChanged($recordId, $statusId, $fromOrderedIds, $toOrderedIds): void
    {
        $customer = Customer::find($recordId);

        $customer->pipelineStages()->sync($statusId);
    }
}
