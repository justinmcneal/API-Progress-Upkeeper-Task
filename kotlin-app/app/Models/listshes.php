<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class listshes extends Model
{
    protected $fillable = ['title','description'];
    protected $table = 'listshes';
}