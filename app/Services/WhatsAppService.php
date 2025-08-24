<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('WHATSAPP_API_URL');
    }

    public function sendMessage(string $phoneNumber, string $message): bool
    {
        if (!$this->baseUrl) {
            Log::error('WHATSAPP_API_URL belum diatur di file .env');
            return false;
        }

        if (!str_ends_with($phoneNumber, '@c.us')) {
            $phoneNumber = preg_replace('/^0/', '62', $phoneNumber) . '@c.us';
        }

        $url = $this->baseUrl;

        try {
            $response = Http::timeout(15)->post($url."/client/sendMessage/ABCD", [
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