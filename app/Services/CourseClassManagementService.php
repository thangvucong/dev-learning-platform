<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Repositories\CourseClassRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CourseClassManagementService
{
    protected CourseClassRepository $classRepository;
    protected ClassSessionGeneratorService $sessionGeneratorService;

    public function __construct(
        CourseClassRepository $classRepository,
        ClassSessionGeneratorService $sessionGeneratorService
    )
    {
        $this->classRepository = $classRepository;
        $this->sessionGeneratorService = $sessionGeneratorService;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createClass(array $payload): CourseClass
    {
        // Normalize nullable fields
        $payload = array_filter(
            $payload,
            static function ($value) {
                return $value !== '' && $value !== null;
            }
        );

        // Defensive: if DB schema doesn't contain some columns (e.g. mode/capacity),
        // don't pass them into model creation to avoid SQL errors.
        if (!Schema::hasColumn('classes', 'mode')) {
            unset($payload['mode']);
        }
        if (!Schema::hasColumn('classes', 'capacity')) {
            unset($payload['capacity']);
        }
        if (!Schema::hasColumn('classes', 'location')) {
            unset($payload['location']);
        }

        // Normalize mode (FE dùng online/offline)
        $mode = Arr::get($payload, 'mode');
        if (is_string($mode)) {
            $payload['mode'] = in_array($mode, ['online', 'offline'], true) ? $mode : 'offline';
        }

        // Normalize status
        $status = Arr::get($payload, 'status');
        if (is_string($status)) {
           $payload['status'] = 'upcoming';
        }

        $scheduleConfig = (array) Arr::pull($payload, 'schedule_config', []);

        return DB::transaction(function () use ($payload, $scheduleConfig) {
            $courseClass = $this->classRepository->createClass($payload);
            $this->sessionGeneratorService->generate($courseClass, $scheduleConfig);

            return $courseClass->loadMissing('sessions');
        });
    }
}

