<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Opekunov\Centrifugo\Centrifugo;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RoomController extends Controller
{
    private Centrifugo $centrifugo;

    public function __construct(Centrifugo $centrifugo)
    {
        $this->centrifugo = $centrifugo;
    }

    public function index()
    {
        $rooms = Room::with(['users', 'messages' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])->orderBy('created_at', 'desc')->get();

        return view('rooms.index', [
            'rooms' => $rooms,
        ]);
    }

    public function show(int $id, Request $request)
    {
        $room = Room::with(['users', 'messages.user' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])->find($id);

        $room->users()->sync(Auth::user()->id);
        $roomResource = new RoomResource($room);
        return Inertia::render('Room/Detail', [
            'room' => json_decode($roomResource->toJson(), true),
            'isJoin' => $room->users->contains('id', Auth::user()->id)
        ]);
    }

    public function join(int $id)
    {
        $room = Room::find($id);
        $room->users()->sync([Auth::user()->id]);

        return redirect()->route('rooms.show', $id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:32', 'unique:rooms'],
        ]);

        DB::beginTransaction();
        try {
            $room = Room::create(['name' => $request->get('name')]);
            $room->users()->attach(Auth::user()->id);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return redirect()->route('rooms.show', $room->id);
    }

    public function publish(int $id, Request $request)
    {
        $requestData = $request->json()->all();
        $status = Response::HTTP_OK;

        try {
            $message = Message::create([
                'sender_id' => Auth::user()->id,
                'message' => $requestData["message"],
                'room_id' => $id,
            ]);

            $room = Room::with('users')->find($id);

            $channels = [];
            foreach ($room->users as $user) {
                $channels[] = sprintf('presencezone:chatroom.%s', $room->id);
            }

            $messageResource = new MessageResource($message);
            // $this->centrifugo->broadcast($channels, json_decode($messageResource->toJson(), true));
            MessageCreated::dispatchIf(!blank(json_decode($messageResource->toJson(), true)), json_decode($messageResource->toJson(), true));
            return response('success', $status);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            return response('', $status);
        }
    }
}
