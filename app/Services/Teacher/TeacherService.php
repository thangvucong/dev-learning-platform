<?php

namespace App\Services\Teacher ;

use App\Repositories\Teacher\TeacherRepository;

class TeacherService
{
    protected $teacherRepository;

    public function __construct(TeacherRepository $teacherRepository)
    {
        $this->teacherRepository = $teacherRepository;
    }

    public function getTeacherClasses(int $teacherId)
    {
        
        return $this->teacherRepository->getClassesByTeacher($teacherId);
    }
    public function getClassesWithStudents(int $teacherId)
{
    return $this->teacherRepository->getClassesWithStudents($teacherId);
}
// File: app/Services/Teacher/TeacherService.php

public function getTeacherMonthlySchedule(int $teacherId)
{
    
    return $this->teacherRepository->getMonthlySchedule($teacherId);
}
}