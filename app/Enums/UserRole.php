<?php

namespace App\Enums;

enum UserRole: string
{
    case Consumer = 'consumer';
    case Merchant = 'merchant';
    case Admin = 'admin';
}
