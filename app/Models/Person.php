<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Person extends Model
{
    use HasUuids;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
