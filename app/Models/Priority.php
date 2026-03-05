<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Priority extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'weight',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticked::class);
    }
}
