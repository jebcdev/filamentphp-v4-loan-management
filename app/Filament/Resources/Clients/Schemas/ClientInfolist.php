<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Personal Information'))
                    ->description(__('Enter the basic personal details of the client.'))
                    ->icon(Heroicon::User)
                    ->schema([
                        TextEntry::make('full_name')
                            ->label(__('Full Name')),
                        TextEntry::make('document_type')
                            ->label(__('Document Type'))
                            ->formatStateUsing(fn($state) => __($state)),
                        TextEntry::make('document_number')
                            ->label(__('Document Number')),
                        TextEntry::make('birth_date')
                            ->label(__('Birth Date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('gender')
                            ->label(__('Gender'))
                            ->formatStateUsing(fn($state) => __($state))
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Contact Information'))
                    ->description(__('Provide contact details for the client.'))
                    ->icon(Heroicon::Phone)
                    ->schema([
                        TextEntry::make('address')
                            ->label(__('Address'))
                            ->placeholder('-'),
                        TextEntry::make('city')
                            ->label(__('City'))
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label(__('Phone'))
                            ->placeholder('-'),
                        TextEntry::make('secondary_phone')
                            ->label(__('Secondary Phone'))
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label(__('Email Address'))
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Financial Information'))
                    ->description(__('Enter financial details and credit limits.'))
                    ->icon(Heroicon::Banknotes)
                    ->schema([
                        TextEntry::make('occupation')
                            ->label(__('Occupation'))
                            ->placeholder('-'),
                        TextEntry::make('monthly_income')
                            ->label(__('Monthly Income'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('max_credit_limit')
                            ->label(__('Max Credit Limit'))
                            ->numeric(),
                        TextEntry::make('used_credit_limit')
                            ->label(__('Used Credit Limit'))
                            ->numeric(),
                        TextEntry::make('available_credit_limit')
                            ->label(__('Available Credit Limit'))
                            ->numeric()
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('Status and Notes'))
                    ->description(__('Set the client status and add any additional notes.'))
                    ->icon(Heroicon::ClipboardDocumentList)
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('Status'))
                            ->formatStateUsing(fn($state) => __($state)),
                        TextEntry::make('personal_references')
                            ->label(__('Personal References'))
                            ->placeholder('-')
                            // ->columnSpanFull()
                            ,
                        TextEntry::make('notes')
                            ->label(__('Notes'))
                            ->placeholder('-')
                            // ->columnSpanFull()
                            ,
                        TextEntry::make('creator.name')
                            ->label(__('Created By'))
                            ->placeholder('-'),
                        TextEntry::make('updater.name')
                            ->label(__('Updated By'))
                            ->placeholder('-'),
                    ])
                    ->columnSpan(2)
                    ->columnSpanFull(),

            ]);
    }
}
