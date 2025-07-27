<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'website',
        'logo_url',
    ];

    /**
     * Get the applications for this company.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get the logo URL with fallback to a default logo.
     */
    public function getLogoUrlAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        // Fallback to Clearbit logo service if we have a website
        if ($this->website) {
            return 'https://logo.clearbit.com/' . $this->website;
        }

        // Default placeholder logo
        return 'https://via.placeholder.com/40x40/6B7280/FFFFFF?text=' . substr($this->name, 0, 1);
    }
}
