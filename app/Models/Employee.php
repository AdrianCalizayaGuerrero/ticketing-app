<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'department',
        'position'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'id');
    }

    public function reportedTickets()
    {
        return $this->hasMany(Ticked::class, 'reporter_id');
    }
}
