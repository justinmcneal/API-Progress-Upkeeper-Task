<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // Define all the fields that are mass assignable
    protected $fillable = ['username', 'email', 'message']; // Combine all fields into one array

    protected $table = 'contacts'; 
}
