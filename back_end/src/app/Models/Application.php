<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'status',
        'cover_letter',
        'cv_path',
    ];

    protected $appends = [
        'cv_url',
    ];

    public function getCvUrlAttribute(): ?string
    {
        if (!$this->cv_path) {
            return null;
        }

        return asset('storage/'.$this->cv_path);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
