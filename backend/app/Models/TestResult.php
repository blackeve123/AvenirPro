<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = [
        'user_id',
        'riasec_scores',
        'top_profile',
        'recommended_jobs',
    ];

    protected $casts = [
        'riasec_scores'    => 'array',
        'recommended_jobs' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
