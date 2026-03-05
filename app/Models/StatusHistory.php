<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\TickedStatus;

class StatusHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ticket_id',
        'previous_status',
        'new_status',
        'comment',
        'changed_by',
        'changed_at'
    ];

    protected $casts = [
    'previous_status' => TickedStatus::class,
    'new_status' => TickedStatus::class,
    'changed_at' => 'datetime',
];

    public function ticket()
    {
        return $this->belongsTo(Ticked::class, 'ticked_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'changed_by');
    }
}
