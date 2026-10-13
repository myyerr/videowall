<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Content extends Model
{
    protected $fillable = [
        'display_id',
        'title',
        'custom_elements',
        'status',
        'display_date',
        'start_time',
        'end_time',
        'created_by',
    ];

    protected $casts = [
        'custom_elements' => 'array',
        'display_date'    => 'date',
    ];

    // ── Relationships ───────────────────────────────────────
    public function display()
    {
        return $this->belongsTo(Display::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(ContentDetail::class)->orderBy('sort_order');
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('status', 'active')
            ->where('display_date', $now->toDateString())
            ->where('start_time', '<=', $now->format('H:i:s'))
            ->where('end_time', '>=', $now->format('H:i:s'));
    }

    // ── Auto-status Helper ───────────────────────────────────
    public static function computeStatus(string $displayDate, string $startTime, string $endTime): string
    {
        $now         = Carbon::now();
        $todayStr    = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        if ($displayDate < $todayStr || ($displayDate === $todayStr && $endTime < $currentTime)) {
            return 'expired';
        }

        if ($displayDate === $todayStr && $startTime <= $currentTime && $endTime >= $currentTime) {
            return 'active';
        }

        return 'draft';
    }
}