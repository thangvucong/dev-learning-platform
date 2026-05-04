<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Services\EnrollmentClassSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoAssignEnrollmentClassJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    protected int $enrollmentId;

    /**
     * Create a new job instance.
     *
     * @param  int  $enrollmentId
     */
    public function __construct(int $enrollmentId)
    {
        $this->enrollmentId = $enrollmentId;
    }

    /**
     * Execute the job.
     *
     * @param  \App\Services\EnrollmentClassSyncService  $enrollmentClassSyncService
     * @return void
     */
    public function handle(EnrollmentClassSyncService $enrollmentClassSyncService): void
    {
        $enrollment = Enrollment::query()->find($this->enrollmentId);
        if (!$enrollment) {
            return;
        }

        $enrollmentClassSyncService->autoAssignUpcomingClassForEnrollment($enrollment);
    }
}

