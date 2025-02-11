<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedTodo extends Model
{
    protected $fillable = [
        'id_todo',
        'asignee',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'asignee', 'id');
    }

    public function todo()
    {
        return $this->belongsTo(Todo::class, 'id', 'id_todo');
    }
}
