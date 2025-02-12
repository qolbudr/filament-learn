<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TodoResource\Pages;
use App\Filament\Resources\TodoResource\RelationManagers;
use App\Models\Todo;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TodoResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Todo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'todo:create',
            'todo:update',
            'todo:delete',
            'todo:pagination',
            'todo:detail',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->placeholder('Buy groceries'),
                Forms\Components\Select::make('assigner')
                    ->options(User::where('id', Auth::id())->pluck('name', 'id'))
                    ->default(Auth::id())
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Milk, eggs, bread, and butter'),
                Forms\Components\CheckboxList::make('asignee')
                    ->options(User::all()->pluck('name', 'id'))
                    ->label('Assignee')
                    ->dehydrated(),
                Forms\Components\DatePicker::make('due_date')
                    ->required()
                    ->label('Due Date'),
                Forms\Components\Select::make('status')
                    ->options([
                        'todo' => 'Todo',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    ])
                    ->default('todo')
                    ->required()
                    ->label('Status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignerUser.name')
                    ->label('Assigner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedUsers.name')
                    ->label('Assignee')
                    ->getStateUsing(function ($record) {
                        return $record->assignedUsers->pluck('name')->implode(', ');
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable()
                    ->getStateUsing(fn($record) => match ($record->status) {
                        'todo' => 'Todo',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Todo' => 'gray',
                        'In Progress' => 'warning',
                        'Done' => 'success',
                    })
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()->fillForm(function ($record, $table) {
                    $data = $record->attributesToArray();
                    $data['asignee'] = $record->assignedUsers->pluck('id')->toArray();
                    return $data;
                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTodos::route('/'),
            'create' => Pages\CreateTodo::route('/create'),
            'edit' => Pages\EditTodo::route('/{record}/edit'),
        ];
    }
}
