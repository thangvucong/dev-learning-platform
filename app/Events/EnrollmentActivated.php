<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrollmentActivated
{
    use Dispatchable, SerializesModels;

    /**
     * @var int
     */
    public int $enrollmentId;

    /**
     * Create a new event instance.
     *
     * @param  int  $enrollmentId
     */
    public function __construct(int $enrollmentId)
    {
        $this->enrollmentId = $enrollmentId;
    }
}

