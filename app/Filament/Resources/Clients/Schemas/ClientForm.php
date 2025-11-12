<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\ClientStatus;
use App\Enums\DocumentType;
use App\Enums\Gender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Personal Information'))
                    ->description(__('Enter the basic personal details of the client.'))
                    ->icon(Heroicon::User)
                    ->schema([
                        TextInput::make('full_name')
                            ->label(__('Full Name'))
                            ->required(),

                        Select::make('document_type')
                            ->options(
                                array_combine(
                                    DocumentType::values(),
                                    array_map(fn($value) => __($value), DocumentType::values())
                                )
                            )
                            ->preload()
                            ->searchable()
                            ->label(__('Document Type'))
                            ->required()
                            ->default('citizenship_id_card'),

                        TextInput::make('document_number')
                            ->label(__('Document Number'))
                            ->required(),

                        DatePicker::make('birth_date')
                            ->label(__('Birth Date'))
                            ->required(),

                        Select::make('gender')
                            ->options(
                                array_combine(
                                    Gender::values(),
                                    array_map(fn($value) => __($value), Gender::values())
                                )
                            )
                            ->preload()
                            ->searchable()
                            ->label(__('Gender'))
                            ->required()
                            ->default(Gender::other),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Contact Information'))
                    ->description(__('Provide contact details for the client.'))
                    ->icon(Heroicon::Phone)
                    ->schema([
                        TextInput::make('address')
                            ->label(__('Address'))
                            ->required(),

                        TextInput::make('city')
                            ->label(__('City'))
                            ->required(),

                        TextInput::make('phone')
                            ->label(__('Phone'))
                            ->required(),

                        TextInput::make('secondary_phone')
                            ->label(__('Secondary Phone'))
                            ->required(),

                        TextInput::make('email')
                            ->label(__('Email Address'))
                            ->email()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Financial Information'))
                    ->description(__('Enter financial details and credit limits.'))
                    ->icon(Heroicon::Banknotes)
                    ->schema([
                        TextInput::make('occupation')
                            ->label(__('Occupation'))
                            ->required(),

                        TextInput::make('monthly_income')
                            ->label(__('Monthly Income'))
                            ->numeric()
                            ->required(),

                        TextInput::make('max_credit_limit')
                            ->label(__('Max Credit Limit'))
                            ->required()
                            ->numeric()
                            ->default(0),

                        TextInput::make('used_credit_limit')
                            ->label(__('Used Credit Limit'))
                            ->required()
                            ->numeric()
                            ->default(0),

                        TextInput::make('available_credit_limit')
                            ->label(__('Available Credit Limit'))
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Status and Notes'))
                    ->description(__('Set the client status and add any additional notes.'))
                    ->icon(Heroicon::ClipboardDocumentList)
                    ->schema([
                        ToggleButtons::make('status')
                            ->options(
                                array_combine(
                                    ClientStatus::values(),
                                    array_map(fn($value) => __($value), ClientStatus::values())
                                )
                            )
                            ->grouped()
                            ->label(__('Status'))
                            ->required()
                            ->default('active'),

                        Textarea::make('personal_references')
                            ->label(__('Personal References'))
                            ->placeholder(__('Enter personal references here: name, phone, relationship'))
                            ->columnSpanFull()
                            ->required(),

                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->placeholder(__('Enter notes here'))
                            ->columnSpanFull()
                            ,
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
