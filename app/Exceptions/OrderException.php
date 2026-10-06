<?php

namespace App\Exceptions;

use RuntimeException;

class OrderException extends RuntimeException
{
    public static function unavailable(): self
    {
        return new self('Produk ini sudah tidak tersedia untuk dipesan.');
    }

    public static function insufficientStock(int $remaining): self
    {
        return new self("Stok tidak cukup. Sisa stok saat ini: {$remaining}.");
    }
}
