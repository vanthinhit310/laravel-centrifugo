<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Opekunov\Centrifugo\Centrifugo;

class CentrifugoProxyController extends Controller
{
    public function connect()
    {
        return new JsonResponse([
            'result' => [
                'user' => (string) Auth::user()->id,
                'channels' => ["personal:#" . Auth::user()->id],
            ]
        ]);
    }

    public function genConnectionToken(Request $request, Centrifugo $centrifugo)
    {
        $currentUser = Auth::user();

        if (isset($currentUser) && !blank($currentUser)) {
            return response()->json(['token' => $centrifugo->generateConnectionToken($currentUser->id, now()->addDays(1))]);
        }

        return response()->json(['token' => '']);
    }
}
