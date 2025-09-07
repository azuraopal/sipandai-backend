<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $sessionId;

    public function __construct()
    {
        $this->baseUrl   = env('WHATSAPP_API_URL', '');
        $this->apiKey    = env('WHATSAPP_API_KEY', '');
        $this->sessionId = env('WHATSAPP_SESSION_ID', '');
    }

    public function sendMessage(string $phoneNumber, string $message): bool
    {
        if (!$this->baseUrl || !$this->apiKey || !$this->sessionId) {
            Log::error('WHATSAPP_API_URL, WHATSAPP_API_KEY, atau WHATSAPP_SESSION_ID belum diatur di file .env');
            return false;
        }

        if (!str_ends_with($phoneNumber, '@c.us')) {
            $phoneNumber = preg_replace('/^0/', '62', $phoneNumber) . '@c.us';
        }

        try {
            $url = rtrim($this->baseUrl, '/') . "/client/sendMessage/{$this->sessionId}";

            $response = Http::timeout(15)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                ])
                ->post($url, [
                    'chatId' => $phoneNumber,
                    'contentType' => 'string',
                    'content' => $message,
                ]);

            if ($response->successful()) {
                Log::info("Pesan WhatsApp berhasil dikirim ke {$phoneNumber}");
                return true;
            } else {
                Log::error("Gagal mengirim pesan WhatsApp ke {$phoneNumber}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception saat mengirim pesan WhatsApp: " . $e->getMessage());
            return false;
        }
    }
}
