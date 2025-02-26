<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SimpleSender
{
    public static function sendEmail($to, $subject, $message)
    {
        try {
            Mail::raw($message, function ($email) use ($to, $subject) {
                $email->to($to);
                $email->subject($subject);
            });
            
            Log::info("Correo enviado correctamente a: {$to}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error al enviar correo: " . $e->getMessage());
            return false;
        }
    }
}