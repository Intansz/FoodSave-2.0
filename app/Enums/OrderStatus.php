<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case ReadyForPickup = 'ready_for_pickup';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Konfirmasi',
            self::Confirmed => 'Dikonfirmasi',
            self::ReadyForPickup => 'Siap Diambil',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /** Kunci tone dipetakan ke kelas Tailwind di <x-fs.status-badge>. */
    public function tone(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'brand',
            self::ReadyForPickup, self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    /** Ikon sebagai penanda selain warna (design.md §7.3). */
    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Confirmed, self::Completed => 'check',
            self::ReadyForPickup => 'bag',
            self::Cancelled => 'x',
        };
    }

    public function isOngoing(): bool
    {
        return ! in_array($this, [self::Completed, self::Cancelled], true);
    }

    /** Urutan langkah pada timeline (tanpa Cancelled). */
    public static function timeline(): array
    {
        return [self::Pending, self::Confirmed, self::ReadyForPickup, self::Completed];
    }
}
