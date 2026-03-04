<?php

namespace App\Events;

use App\Models\Sppd\SppdRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SppdApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SppdRequest $sppd)
    {
    }
}

