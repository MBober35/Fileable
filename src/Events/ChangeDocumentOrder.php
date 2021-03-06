<?php

namespace MBober35\Fileable\Events;

use App\Models\File;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangeDocumentOrder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $morph;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Model $morph)
    {
        $this->morph = $morph;
    }
}
