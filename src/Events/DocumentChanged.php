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

class DocumentChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $doc;
    public $morph;
    public $action;

    /**
     * Create a new event instance.
     *
     * ImageChanged constructor.
     * @param File $file
     * @param string $action
     * @param null|Model $morph
     */
    public function __construct(File $file, $action = "", $morph = null)
    {
        $this->doc = $file;
        $this->action = $action;
        if (! empty($morph)) {
            $this->morph = $morph;
        } elseif (! empty($file->fileable_id)) {
            $this->morph = $file->fileable;
        }
        else {
            $this->morph = null;
        }
    }
}
