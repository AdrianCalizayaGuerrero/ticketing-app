<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'employee_code',
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'id');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticked::class, 'assigned_agent_id');
    }
}
