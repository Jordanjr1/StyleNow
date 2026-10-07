<?php

namespace App\Helpers;

class PasswordHelper
{
    /**
     * Genera hash EXACTO para contraseña
     */
    public static function makeExact($password)
    {
        // 1. Convertir a UTF-8 explícitamente
        $password_utf8 = mb_convert_encoding($password, 'UTF-8');
        
        // 2. SHA256 del string binario
        $hash_binary = hash('sha256', $password_utf8, true);
        
        // 3. Base64 para almacenamiento seguro
        return base64_encode($hash_binary);
    }
    
    /**
     * Verifica contraseña EXACTA
     */
    public static function verifyExact($password, $hash)
    {
        $new_hash = self::makeExact($password);
        return hash_equals($hash, $new_hash); // Comparación segura contra timing attacks
    }
}