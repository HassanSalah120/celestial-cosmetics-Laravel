<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;

class EncryptionHelper
{
    /**
     * Encrypt sensitive data
     *
     * @param mixed $value
     * @return string|null
     */
    public static function encrypt($value)
    {
        if (empty($value)) {
            return null;
        }
        
        try {
            return Crypt::encryptString((string) $value);
        } catch (\Exception $e) {
            Log::error('Encryption failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Decrypt sensitive data
     *
     * @param string|null $value
     * @return string|null
     */
    public static function decrypt($value)
    {
        if (empty($value)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            Log::error('Decryption failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Determine if a value is encrypted
     *
     * @param string|null $value
     * @return bool
     */
    public static function isEncrypted($value)
    {
        if (empty($value)) {
            return false;
        }
        
        try {
            $decrypted = Crypt::decryptString($value);
            return true;
        } catch (DecryptException $e) {
            return false;
        }
    }
    
    /**
     * Securely handle credit card information by masking all but last 4 digits
     * 
     * @param string $cardNumber
     * @return string
     */
    public static function maskCreditCard($cardNumber)
    {
        if (empty($cardNumber)) {
            return '';
        }
        
        // Strip any non-numeric characters
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        
        // Keep only first digit and last 4 digits
        $length = strlen($cardNumber);
        if ($length <= 4) {
            return $cardNumber; // Too short to mask
        }
        
        $firstDigit = substr($cardNumber, 0, 1);
        $lastFour = substr($cardNumber, -4);
        
        // Create masked section
        $maskedSection = str_repeat('*', $length - 5);
        
        return $firstDigit . $maskedSection . $lastFour;
    }
} 