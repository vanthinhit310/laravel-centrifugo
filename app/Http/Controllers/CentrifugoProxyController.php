<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Opekunov\Centrifugo\Centrifugo;

class CentrifugoProxyController extends Controller
{
    private $centrifugo;

    public function __construct(Centrifugo $centrifugo)
    {
        $this->centrifugo = $centrifugo;
    }

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

    public function genSubscriptionToken(Request $request, Centrifugo $centrifugo)
    {
        $currentUser = Auth::user();

        if (isset($currentUser) && !blank($currentUser)) {
            return response()->json(['token' => $centrifugo->generateSubscriptionToken((string)$currentUser->id, $request->get('channel_name'), now()->addDays(1))]);
        }

        return response()->json(['token' => '']);
    }

    public function getUserJoined(Request $request)
    {
        $uid = $request->get("uid");

        if (!blank($uid)) {
            $info = $this->centrifugo->presence($request->get('channel'));

            if (isset($info) && is_array($info)) {
                $clients = array_values(Arr::get($info, 'result.presence', []));
                if (isset($clients) && is_array($clients) && !blank($clients)) {
                    $filtered = Arr::where($clients, function ($value) use ($uid) {
                        return intval($value['user']) === intval($uid);
                    });

                    if (blank($filtered) || count($filtered) === 1) {
                        return response()->json(['info' => User::find($uid)]);
                    }
                }
            }
        }

        return response()->json(['info' => ""]);
    }
}
