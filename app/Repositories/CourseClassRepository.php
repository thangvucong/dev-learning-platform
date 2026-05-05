<?php
namespace App\Repositories;

use App\Models\CourseClass;

class CourseClassRepository
{
    public function getAllClassesPaginated($perPage = 10)
    {
      
        return CourseClass::with(['course'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}