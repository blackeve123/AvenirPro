<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'sector',
        'salary_range',
        'riasec_types',
        'image_path',
    ];

    protected $casts = [
        'riasec_types' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function steps(): HasMany
    {
        /** @var HasMany $relation */
        $relation = $this->hasMany(Step::class)->orderBy('order');
        return $relation;
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        // Local file: served from public/ directory
        return url($this->image_path);
    }
}