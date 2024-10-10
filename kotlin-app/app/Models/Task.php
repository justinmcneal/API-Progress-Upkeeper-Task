<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'task_description',
        'start_datetime',
        'end_datetime',
        'repeat_days',
        'user_id'  // Include user_id in the fillable fields
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'repeat_days' => 'array',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
