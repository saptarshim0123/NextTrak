<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'company_id',
        'company_name',
        'job_title',
        'salary',
        'status',
        'application_date',
        'follow_up_date',
        'contact_email',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        // No sensitive fields to hide for applications
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'application_date' => 'date',
        'follow_up_date' => 'date',
    ];

    /**
     * Get the user that owns the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company for this application.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the company name (from relationship or fallback to company_name field).
     */
    public function getCompanyNameAttribute(): string
    {
        return $this->company?->name ?? $this->attributes['company_name'] ?? 'Unknown Company';
    }

    /**
     * Scope a query to only include applications for the authenticated user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the status color classes for styling.
     * 
     * Color Rationale:
     * - Accepted: #065F46 text on #D1FAE5 background - Clear, hopeful green for ultimate positive outcome
     * - Interviewing: #9A3412 text on #FEF9C3 background - Warm, muted yellow/gold for active progress
     * - Rejected/Withdrawn: #991B1B text on #FEE2E2 background - Muted terracotta/brick for respectful closure
     * - Applied: #3F3F46 text on #F5F5F4 background - Simple, soft gray for neutral starting point
     */
    public function getStatusColorAttribute(): array
    {
        return match($this->status) {
            'Applied' => [
                'bg' => 'status-applied-bg',
                'text' => 'status-applied-text',
                'border' => 'border-stone-200/50 dark:border-stone-500/30',
                'bg_style' => 'background-color: #F5F5F4;',
                'text_style' => 'color: #3F3F46;',
                'dark_bg_style' => 'background-color: #52525B;',
                'dark_text_style' => 'color: #E4E4E7;'
            ],
            'Interviewing' => [
                'bg' => 'status-interviewing-bg',
                'text' => 'status-interviewing-text',
                'border' => 'border-amber-200/50 dark:border-amber-500/30',
                'bg_style' => 'background-color: #FEF9C3;',
                'text_style' => 'color: #9A3412;',
                'dark_bg_style' => 'background-color: #92400E;',
                'dark_text_style' => 'color: #FEF3C7;'
            ],
            'Accepted' => [
                'bg' => 'status-accepted-bg',
                'text' => 'status-accepted-text',
                'border' => 'border-emerald-200/50 dark:border-emerald-500/30',
                'bg_style' => 'background-color: #D1FAE5;',
                'text_style' => 'color: #065F46;',
                'dark_bg_style' => 'background-color: #065F46;',
                'dark_text_style' => 'color: #D1FAE5;'
            ],
            'Rejected' => [
                'bg' => 'status-rejected-bg',
                'text' => 'status-rejected-text',
                'border' => 'border-red-200/50 dark:border-red-500/30',
                'bg_style' => 'background-color: #FEE2E2;',
                'text_style' => 'color: #991B1B;',
                'dark_bg_style' => 'background-color: #991B1B;',
                'dark_text_style' => 'color: #FECACA;'
            ],
            'Withdrawn' => [
                'bg' => 'status-rejected-bg',
                'text' => 'status-rejected-text',
                'border' => 'border-red-200/50 dark:border-red-500/30',
                'bg_style' => 'background-color: #FEE2E2;',
                'text_style' => 'color: #991B1B;',
                'dark_bg_style' => 'background-color: #991B1B;',
                'dark_text_style' => 'color: #FECACA;'
            ],
            default => [
                'bg' => 'status-applied-bg',
                'text' => 'status-applied-text',
                'border' => 'border-stone-200/50 dark:border-stone-500/30',
                'bg_style' => 'background-color: #F5F5F4;',
                'text_style' => 'color: #3F3F46;',
                'dark_bg_style' => 'background-color: #52525B;',
                'dark_text_style' => 'color: #E4E4E7;'
            ]
        };
    }
}
