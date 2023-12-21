<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\TaskResource;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
 
class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Task::class;

    protected function headerActions(): array
    {
        return [
            // Actions\CreateAction::make(),            
        ];
    }
    
    public function fetchEvents(array $fetchInfo): array
    {
        return Task::query()
            ->get()
            ->map(
                fn (Task $event) => [
                    'title' => $event->description,
                    'start' => $event->due_date,
                    'end' => $event->due_date,
                    'url' => TaskResource::getUrl(name: 'edit', parameters: ['record' => $event]),
                ]
            )
            ->all();
    }

    public static function canView(): bool
    {
        return false;
    }
}
