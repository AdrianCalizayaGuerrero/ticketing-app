<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Message extends Model
{
    use HasUuids;

    protected $fillable = [
        'content',
        'is_internal',
        'ticket_id',
        'author_id'
    ];

    protected $casts = [
        'is_internal' => 'boolean'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticked::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
