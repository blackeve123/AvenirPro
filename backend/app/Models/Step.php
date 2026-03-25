<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
    protected $fillable = [
        'job_id',
        'title',
        'description',
        'order',
        'duration',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
