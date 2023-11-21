<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PipelineStageResource\Pages;
use App\Filament\Resources\PipelineStageResource\RelationManagers;
use App\Models\PipelineStage;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PipelineStageResource extends Resource
{
    protected static ?string $model = PipelineStage::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_default')
                    ->options([
                        'heroicon-o-x-circle',
                        'heroicon-o-check-circle' => fn ($state, $record): bool => $record->is_default,
                    ])
                    ->colors([
                        'danger',
                        'success' => fn ($state, $record): bool => $record->is_default,
                    ]),
                TextColumn::make('created_at')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->toggleable()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('is_default')
                    ->label('Set Default')
                    ->icon('heroicon-o-star')
                    ->action(function(Model $record): void {
                        $record->is_default = ! $record->is_default;
                        $record->save();
                    })
                    ->visible(fn (Model $record): bool => ! $record->is_default ),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListPipelineStages::route('/'),
            'create' => Pages\CreatePipelineStage::route('/create'),
            'edit' => Pages\EditPipelineStage::route('/{record}/edit'),
        ];
    }
}
