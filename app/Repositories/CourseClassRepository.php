<?php
namespace App\Repositories;

use App\Models\CourseClass;

class CourseClassRepository
{
    public function getAllClassesPaginated($perPage = 10)
    {
      
        return CourseClass::with(['course', 'instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}