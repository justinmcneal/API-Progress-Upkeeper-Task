<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'task_description',
        'start_datetime',
        'end_datetime',
        'repeat_days',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'repeat_days' => 'array',
    ];
    
}