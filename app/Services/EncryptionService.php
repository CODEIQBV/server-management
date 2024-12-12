<?php

namespace App\Services;

class EncryptionService
{
    public function encrypt(string $value): string
    {
        return encrypt($value);
    }

    public function decrypt(string $encryptedValue): string
    {
        return decrypt($encryptedValue);
    }
} 