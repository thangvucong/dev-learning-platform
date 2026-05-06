<?php
namespace App\Repositories;

use App\Models\CourseClass;

class CourseClassRepository
{
    public function getAllClassesPaginated($perPage = 10)
    {
      
        return CourseClass::with(['course'])
            ->withCount([
                'users as current_students' => function ($query) {
                    $query->where('class_user.status', 'active');
                },
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new class (lớp học) record.
     *
     * @param  array<string, mixed>  $payload
     */
    public function createClass(array $payload): CourseClass
    {
        return CourseClass::query()->create($payload)->loadMissing(['course']);
    }
}