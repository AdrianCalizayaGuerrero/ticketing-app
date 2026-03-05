<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\TickedStatus;

class Ticked extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'reference_code',
        'subject',
        'description',
        'status',
        'reporter_id',
        'assigned_agent_id',
        'category_id',
        'priority_id'
    ];

    protected $casts = [
        'status' => TickedStatus::class,
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(StatusHistory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
}
