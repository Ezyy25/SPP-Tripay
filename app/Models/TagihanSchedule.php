<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'generate_day',
        'due_day',
        'nominal',
        'is_active',
    ];
}