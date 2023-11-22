<?php

namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\LeadSource;
use Filament\Tables\Table;
use App\Models\CustomField;
use App\Models\PipelineStage;
use Filament\Resources\Resource;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Employee Information')
                    ->schema([
                        Select::make('employee_id')
                            ->label('Employee Name')
                            ->options(
                                User::whereHas('roles', function($query) {
                                    return $query->where('name', 'employee');
                                })->pluck('name', 'id')->toArray()
                            )
                            ->required()
                            ->autofocus()
                            ]),
                Section::make('Customer Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->autofocus(),
                        TextInput::make('last_name')
                            ->required()
                            ->autofocus(),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->autofocus(),
                        TextInput::make('phone_number')
                            ->required()
                            ->tel()
                            ->autofocus(),
                        Textarea::make('description')
                            ->autofocus()
                            ->rows(4)
                            ->columnSpan([
                                'md' => 2
                            ])
                    ]),
                Section::make('Lead Details')
                    ->columns([
                        'md' => 3
                    ])
                    ->schema([
                        Select::make('lead_source_id')
                            ->label('Lead Source')
                            ->options(LeadSource::pluck('name', 'id')->toArray()),
                        Select::make('tag_id')
                            ->label('Tags')
                            ->options(Tag::pluck('name', 'id')->toArray()),
                        Select::make('pipeline_stage_id')
                            ->label('Pipeline Stage')
                            ->options(PipelineStage::pluck('name', 'id')->toArray())
                    ]),
                Section::make('Additional Fields')
                    ->schema([
                        Repeater::make('additional_details')
                            ->label('')
                            ->columns(2)
                            ->schema([
                                Select::make('custom_field_id')
                                    ->options(CustomField::pluck('name', 'id')->toArray())
                                    ->label('Field Type')
                                    ->required(),
                                TextInput::make('value')
                                    ->required()
                                           
                            ])
                            ->defaultItems(1) 
                            ->addActionLabel('Add Another Field')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Employee Name'),
               
                ViewColumn::make('customer_name')->view('tables.columns.customer-name'),
                TextColumn::make('email'),
                TextColumn::make('phone_number'),
                TextColumn::make('leadSource.name'),
                TextColumn::make('pipelineStage.name')
            ])
            ->filters([
                
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ]),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
