<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Envía un mensaje de texto al chat de Telegram configurado (Grupo o Canal).
     *
     * @param string $message
     * @return bool
     */
    public static function sendMessage(string $message): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (empty($token) || empty($chatId)) {
            Log::warning('Telegram: Bot Token o Chat ID no están configurados en el archivo .env');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if ($response->failed()) {
                Log::error('Telegram: Error al enviar mensaje. Respuesta: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram: Excepción al enviar mensaje: ' . $e->getMessage());
            return false;
        }
    }
}
