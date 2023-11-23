<?php

namespace App\Filament\Resources\TaskResource\Pages;

use Filament\Actions;
use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All Tasks')->badge($this->getModel()::count()),
            'completed' => Tab::make('Completed Tasks')
                ->badge($this->getModel()::query()->completed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->completed();
                }),
            'incomplete' => Tab::make('Incomplete Tasks')
                ->badge($this->getModel()::query()->completed(false)->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->completed(false);
                })
        ];

        return $tabs;
    }
}
