<?php

namespace App\Filament\Resources\TodoResource\Pages;

use App\Filament\Resources\TodoResource;
use App\Models\AssignedTodo;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use PhpParser\Node\Expr\Assign;

class EditTodo extends EditRecord
{
    protected static string $resource = TodoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mutateFormDataBeforeFill
    (array $data): array
    {
        $data['asignee'] = $this->record->assignedUsers->pluck('id')->toArray();
        return $data;
    }

    public function afterSave()
    {
        $data = $this->data;
        $record = $this->record;

        AssignedTodo::where('id_todo', $record->id)->delete();

        foreach ($data['asignee'] as $assignee) {
            AssignedTodo::create([
                'id_todo' => $record->id,
                'asignee' => $assignee,
            ]);
        }
    }
}
