<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Quote;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\QuoteResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QuoteResource\RelationManagers;
use Filament\Tables\Columns\TextColumn;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                    ->required(),
                Section::make('Quote Products')
                    ->schema([
                        Repeater::make('quoteProducts')
                            ->label('')
                            ->relationship()
                            ->columnSpan(2)
                            ->schema([
                                Select::make('product_id')
                                    ->label('Products')
                                    ->live()
                                    ->options(Product::pluck('name', 'id')->toArray())
                                    // Disable options that are already selected in other rows
                                    ->disableOptionWhen(function ($value, $state, Get $get) {
                                        return collect($get('../*.product_id'))
                                            ->reject(fn($id) => $id == $state)
                                            ->filter()
                                            ->contains($value);
                                    })
                                    ->required()
                                    ->afterStateUpdated(
                                        function (callable $get, callable $set) {
                                            $product = Product::find($get('product_id'));
                                            if ($product) {
                                                $set('price', $product->price);
                                            }
                                        }
                                    ),
                                TextInput::make('price')
                                    ->required()
                                    ->numeric(),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                            ])
                            // Repeatable field is live so that it will trigger the state update on each change
                            ->live()
                            // After adding a new row, we need to update the totals
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            // After deleting a row, we need to update the totals
                            ->deleteAction(
                                fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                            )
                            ->addActionLabel('Add Product')
                            ->columns(3)
                    ]),
                Section::make('')
                    ->schema([
                        TextInput::make('subtotal')
                            ->prefix('$')
                            ->numeric()
                            ->readOnly()
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),
                        TextInput::make('taxes')
                            ->numeric()
                            ->suffix('%')
                            ->default(20)
                            ->required()
                            // Live field, as we need to re-calculate the total on each change
                            ->live(true)
                            // This enables us to display the subtotal on the edit page load
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),
                        TextInput::make('total')
                            ->prefix('$')
                            ->numeric()
                            ->readOnly()
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                    ])
            ]);
    }

    // This function updates totals based on the selected products and quantities
    public static function updateTotals(Get $get, Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('quoteProducts'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));
    
        // Retrieve prices for all selected products
        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');
    
        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);
    
        // Update the state with the new values
        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total', number_format($subtotal + ($subtotal * ($get('taxes') / 100)), 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('taxes')
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->sortable(),
                TextColumn::make('total')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
