<?php

namespace App\Tests\Unit\Entity\Service;

use App\Entity\Evaluation;
use App\Entity\Grade;
use App\Entity\Student;
use PHPUnit\Framework\TestCase;

class EvaluationTest extends TestCase
{
    public function testGetGradesAverage():void
    {
        $evaluation = new Evaluation();
        $grade1 = new Grade();
        $grade1->setGrade('15.50');
        $grade2 = new Grade();
        $grade2->setGrade('12.00');
        $grade3 = new Grade();
        $grade3->setGrade(null); // absent student
        $grade3->setPresent(false);

        $evaluation->addGrade($grade1);
        $evaluation->addGrade($grade2);
        $evaluation->addGrade($grade3);

        $this->assertEquals(13.75, $evaluation->getGradesAverage());
    }

    public function testGetGradesByStudent():void
    {
        $evaluation = new Evaluation();
        $student = $this->createMock(Student::class);
        $grade = new Grade();
        $grade->setStudent($student);

        $evaluation->addGrade($grade);

        $this->assertSame($grade, $evaluation->getGradeByStudent($student));
    }
}
