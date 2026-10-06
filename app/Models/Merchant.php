<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'description',
        'phone',
        'address',
        'area',
        'maps_url',
        'operating_hours',
        'logo',
        'verification_status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'verification_status' => VerificationStatus::class,
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function canSell(): bool
    {
        return $this->is_active && $this->verification_status === VerificationStatus::Approved;
    }

    /** Fallback: bila link Maps kosong/rusak, view menampilkan alamat teks (PRD §8). */
    public function hasValidMapsUrl(): bool
    {
        return filled($this->maps_url) && str_starts_with($this->maps_url, 'https://');
    }
    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }
}
