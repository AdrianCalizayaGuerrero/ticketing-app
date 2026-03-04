<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    protected $fillable = [
        'ticket_id',
        'previous_status',
        'new_status',
        'comment',
        'changed_by',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticked::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'changed_by');
    }
}
