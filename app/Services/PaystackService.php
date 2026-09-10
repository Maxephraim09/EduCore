<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        try {
            $this->secretKey = SystemSetting::getValue('paystack_secret_key');
            $this->publicKey = SystemSetting::getValue('paystack_public_key');
        } catch (\Throwable $e) {
            // If system_settings table doesn't exist yet (fresh install/migration not run),
            // do not crash the whole app.
            $this->secretKey = '';
            $this->publicKey = '';

            Log::warning('PaystackService: unable to load system settings', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function isConfigured()
    {
        return !empty($this->secretKey) && !empty($this->publicKey);
    }

    public function initializeTransaction($email, $amount, $reference, $metadata = [], $callbackUrl = null)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Paystack is not configured properly');
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->retry(3, 1000)->post($this->baseUrl . '/transaction/initialize', [
                'email' => $email,
                'amount' => $amount * 100, // Convert to kobo
                'reference' => $reference,
                'metadata' => $metadata,
                'callback_url' => $callbackUrl ?? route('fee-payments.callback'),
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Paystack connection error', ['error' => $e->getMessage(), 'reference' => $reference]);
            throw new \Exception('Unable to reach payment gateway. Please try again later.');
        }

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Paystack initialization failed', [
            'response' => $response->json(),
            'reference' => $reference
        ]);

        throw new \Exception('Payment initialization failed: ' . ($response->json()['message'] ?? 'Unknown error'));
    }

    public function verifyTransaction($reference)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Paystack is not configured properly');
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->timeout(30)->retry(2, 800)->get($this->baseUrl . '/transaction/verify/' . $reference);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Paystack verification connection error', ['error' => $e->getMessage(), 'reference' => $reference]);
            throw new \Exception('Unable to verify transaction at this time.');
        }

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Paystack verification failed', ['response' => $response->json(), 'reference' => $reference]);
        throw new \Exception('Transaction verification failed');
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }
}
