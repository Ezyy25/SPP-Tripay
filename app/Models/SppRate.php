<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran',
        'nominal',
    ];

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }
}
