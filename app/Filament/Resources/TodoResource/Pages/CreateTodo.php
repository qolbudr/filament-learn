<?php

namespace App\Filament\Resources\TodoResource\Pages;

use App\Filament\Resources\TodoResource;
use App\Models\AssignedTodo;
use Filament\Resources\Pages\CreateRecord;

class CreateTodo extends CreateRecord
{
    protected static string $resource = TodoResource::class;

    public function afterCreate()
    {
        $data = $this->data;
        $record = $this->record;

        // dd($data);

        foreach ($data['asignee'] as $assignee) {
            AssignedTodo::create([
                'id_todo' => $record->id,
                'asignee' => $assignee,
            ]);
        }
    }
}
