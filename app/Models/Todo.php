<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'assigner'
    ];

    public function assignerUser()
    {
        return $this->belongsTo(User::class, 'assigner', 'id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'assigned_todos', 'id_todo', 'asignee');
    }
}
