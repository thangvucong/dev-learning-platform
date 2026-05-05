<?php

namespace App\Listeners;

use App\Events\EnrollmentActivated;
use App\Jobs\AutoAssignEnrollmentClassJob;

class DispatchAutoAssignEnrollmentClassJob
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\EnrollmentActivated  $event
     * @return void
     */
    public function handle(EnrollmentActivated $event): void
    {
        AutoAssignEnrollmentClassJob::dispatch($event->enrollmentId);
    }
}

