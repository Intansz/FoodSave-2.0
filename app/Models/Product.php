<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    public const ACTIVE = 'active';

    public const INACTIVE = 'inactive';

    protected $fillable = [
        'category_id', 'name', 'description', 'image',
        'normal_price', 'foodsave_price', 'stock',
        'pickup_start', 'pickup_end', 'status',
    ];

    protected function casts(): array
    {
        return [
            'normal_price' => 'integer',
            'foodsave_price' => 'integer',
            'discount_percentage' => 'integer',
            'stock' => 'integer',
            'pickup_start' => 'datetime',
            'pickup_end' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Persentase diskon selalu dihitung sistem (PRD FR-03), bukan input mitra.
        static::saving(function (Product $product) {
            $product->discount_percentage = $product->normal_price > 0
                ? max(0, (int) round((1 - $product->foodsave_price / $product->normal_price) * 100))
                : 0;
        });
    }

    /* ---------------------------------------------------------------- Relasi */

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /* ---------------------------------------------------------------- Scopes */

    /**
     * Aturan ketersediaan tunggal (PRD FR-03). Dipakai home, explore, dan validasi order.
     * Tidak tersedia bila: stok 0, dinonaktifkan, pickup berakhir, atau mitra tidak aktif/belum approved.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', self::ACTIVE)
            ->where('stock', '>', 0)
            ->where('pickup_end', '>', now())
            ->whereHas('merchant', fn (Builder $m) => $m
                ->where('is_active', true)
                ->where('verification_status', VerificationStatus::Approved->value));
    }

    public function scopeFlashDeal(Builder $query): Builder
    {
        return $query->where('pickup_end', '<=', now()->addHours(config('foodsave.flash_deal_window_hours')));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.addcslashes($term, '%_\\').'%';

        return $query->where(fn (Builder $q) => $q
            ->where('name', 'like', $like)
            ->orWhereHas('merchant', fn (Builder $m) => $m->where('business_name', 'like', $like)));
    }

    public function scopePriceRange(Builder $query, ?int $min, ?int $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('foodsave_price', '>=', $min))
            ->when($max !== null, fn (Builder $q) => $q->where('foodsave_price', '<=', $max));
    }

    public function scopeOrderedBy(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'termurah' => $query->orderBy('foodsave_price')->orderByDesc('id'),
            'diskon' => $query->orderByDesc('discount_percentage')->orderByDesc('id'),
            'pickup' => $query->orderBy('pickup_end')->orderByDesc('id'),
            default => $query->orderByDesc('id'), // terbaru
        };
    }

    /* ------------------------------------------------------------ Turunan data */

    /** Butuh relasi merchant sudah di-load. */
    public function isAvailable(): bool
    {
        return $this->status === self::ACTIVE
            && $this->stock > 0
            && $this->pickup_end->isFuture()
            && $this->merchant->canSell();
    }

    public function isFlashDeal(): bool
    {
        return $this->isAvailable()
            && $this->pickup_end->lte(now()->addHours(config('foodsave.flash_deal_window_hours')));
    }

    /** Alasan produk tidak bisa dipesan, untuk pesan di UI. Null bila tersedia. */
    public function unavailableReason(): ?string
    {
        return match (true) {
            $this->status !== self::ACTIVE => 'Produk dinonaktifkan oleh mitra.',
            $this->pickup_end->isPast() => 'Waktu pengambilan sudah berakhir.',
            $this->stock <= 0 => 'Stok habis.',
            ! $this->merchant->canSell() => 'Mitra sedang tidak aktif.',
            default => null,
        };
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function getPickupLabelAttribute(): string
    {
        $day = match (true) {
            $this->pickup_start->isToday() => 'Hari ini',
            $this->pickup_start->isTomorrow() => 'Besok',
            default => $this->pickup_start->translatedFormat('d M'),
        };

        return "{$day} {$this->pickup_start->format('H.i')}–{$this->pickup_end->format('H.i')}";
    }
}
