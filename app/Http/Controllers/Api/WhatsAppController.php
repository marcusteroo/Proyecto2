<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function sendMessage(Request $request)
{
    $response = Http::post('http://127.0.0.1:3001/send-message', [
        'number' => $request->number,
        'message' => $request->message,
    ]);

    return $response->json();
}

}
