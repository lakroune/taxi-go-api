<?php

namespace App\Events;

use App\Models\Location;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $location;
    public $driver_id;

    /**
     * Create a new event instance.
     */
    public function __construct(Location $location, $driver_id)
    {
        $this->location = $location;
        $this->driver_id = $driver_id;
    }


    /**
     *  Get the channels the event should broadcast on.
     */

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tracking.' . $this->driver_id),
        ];
    }
    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'driver_id' => $this->driver_id,
            'lat' => (float) $this->location->lat,
            'lng' => (float) $this->location->lng,
            'heading' => (int) $this->location->heading,
            'speed' => (int) $this->location->speed,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
