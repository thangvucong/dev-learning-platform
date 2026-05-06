<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Repositories\CourseClassRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class CourseClassManagementService
{
    protected CourseClassRepository $classRepository;

    public function __construct(CourseClassRepository $classRepository)
    {
        $this->classRepository = $classRepository;
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
            $payload['status'] = in_array($status, ['upcoming', 'ongoing', 'completed'], true)
                ? $status
                : 'upcoming';
        }

        return $this->classRepository->createClass($payload);
    }
}

