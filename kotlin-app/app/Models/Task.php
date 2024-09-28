<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class Task extends Model
// {
   // use HasFactory;
// }

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'task_description',
        'start_datetime',
        'end_datetime',
        'repeat_days',
        'attachment',
        'send_notification',
    ];

    protected $casts = [
        'repeat_days' => 'array', // Cast repeat_days as an array
    ];
}
