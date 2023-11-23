<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use App\Models\User;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\TaskResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TaskResource\RelationManagers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->label('Customer')
                    ->searchable()
                    ->options(Customer::pluck('first_name', 'id')->toArray())
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                    ->required(),
                Select::make('user_id')
                    ->label('Employee')
                    ->searchable()
                    ->options(
                        User::whereHas('roles', function($query) {
                            return $query->where('name', 'employee');
                        })->pluck('name', 'id')->toArray()
                    ),
                RichEditor::make('description')
                    ->required()
                    ->columnSpan(2)
                    ->toolbarButtons([
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'heading',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'table',
                        'undo',
                    ]),
                DatePicker::make('due_date')
                    ->date(),
                Toggle::make('is_completed')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Employee')
                    ->sortable(),
                TextColumn::make('description')
                    ->wrap()
                    ->html(),
                TextColumn::make('due_date')
                    ->date(),
                IconColumn::make('is_completed')
                    ->options([
                        'heroicon-o-x-circle',
                        'heroicon-o-check-circle' => fn ($state, $record): bool => $record->is_completed,
                    ])
                    ->colors([
                        'danger',
                        'success' => fn ($state, $record): bool => $record->is_completed,
                    ]),
                TextColumn::make('created_at')
                    ->date()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->date()
                    ->toggleable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('complete')
                    ->icon('heroicon-m-check-badge')
                    ->visible(fn (Model $record): bool => ! $record->is_completed )
                    ->modalHeading('Mark task as completed?')
                    ->modalDescription('Are you sure you want to mark this task as completed?')
                    ->action(function (Task $record): void {
                        $record->is_completed = true;
                        $record->save();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
