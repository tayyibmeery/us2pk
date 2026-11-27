<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProhibitedItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_name',
        'category',
        'description',
        'reason',
        'severity',
        'icon',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get severity badge color
     */
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get severity label
     */
    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            'high' => 'High Risk',
            'medium' => 'Medium Risk',
            'low' => 'Low Risk',
            default => 'Unknown',
        };
    }

    /**
     * Scope for active items only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for specific severity
     */
    public function scopeSeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Get all unique categories
     */
    public static function getCategories(): array
    {
        return self::distinct()->pluck('category')->filter()->values()->toArray();
    }

    /**
     * Get all severity options
     */
    public static function getSeverityOptions(): array
    {
        return [
            'high' => 'High Risk',
            'medium' => 'Medium Risk',
            'low' => 'Low Risk',
        ];
    }
}
