<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthMobileService
{
    private $ciphering = "AES-128-CTR";
    private $initialization_vector = '1234567891011121';
    private $key = 'OPCLeads2024';
    private $options = 0;

    /**
     * decrypt password
     *
     * @param string $password
     * @return string
     */
    public function decryptMobilePassword(string $password): string
    {
        $decrypted = '';

        try {
            $decrypted = openssl_decrypt(
                $password,
                $this->ciphering,
                $this->key,
                $this->options,
                $this->initialization_vector
            );
        } catch (Exception $e) {
            return $decrypted;
        }

        return $decrypted;
    }

    /**
     * encrypt password
     *
     * @param string $password
     * @return string
     */
    public function encryptMobilePassword(string $password): string
    {
        $encrypted = '';

        try {
            $encrypted = openssl_encrypt(
                $password,
                $this->ciphering,
                $this->key,
                $this->options,
                $this->initialization_vector
            );
        } catch (Exception $e) {
            return $encrypted;
        }

        return $encrypted;
    }
}
