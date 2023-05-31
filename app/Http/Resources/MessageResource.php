<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "text" => $this->message,
            "roomId" => $this->room_id,
            "senderId" => $this->sender_id,
            "senderName" => $this->user->name,
            "createdAt" => Carbon::parse($this->created_at)->format('H:i:s d/m/Y')
        ];
    }
}
