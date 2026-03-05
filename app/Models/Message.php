<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasUuids, HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

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
        return $this->belongsTo(Ticked::class, 'ticket_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
