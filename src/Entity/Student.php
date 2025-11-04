<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends User
{
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Grade::class, orphanRemoval: true)]
    private Collection $grades;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: true)]
    private ?ClassLevel $classLevel = null;

    public function __construct()
    {
        parent::__construct();
        $this->grades = new ArrayCollection();
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setStudent($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getStudent() === $this) {
                $grade->setStudent(null);
            }
        }

        return $this;
    }

    public function getClassLevel(): ?ClassLevel
    {
        return $this->classLevel;
    }

    public function setClassLevel(?ClassLevel $classLevel): static
    {
        $this->classLevel = $classLevel;

        return $this;
    }

    public function getGradeByEval (Evaluation $evaluation): ?Grade
    {
        foreach ($this->getGrades() as $grade){
            if ($grade->getEvaluation() === $evaluation){
                return $grade;
            }
        }
        return null;
    }

    public function getGradesAverage(): ?float
    {
        $countGrades = $this->getGrades()->count();
        if ($countGrades != 0){
            $sumGrades = 0;
            $sumBaremes = 0;
            foreach ($this->getGrades() as $grade){
                //option 1: notes ramenées sur 20
                //on ramène toutes les notes sur 20
                $sumGrades += ($grade->getGrade()*20/$grade->getEvaluation()->getBareme());
                //option 2: moyenne pondérée
                //ou alors on fait une moyenne pondérée...les éval sur 10 comptent moins que celles sur 20
                //$sumGrades += $grade->getGrade();
                //$sumBaremes += $grade->getEvaluation()->getBareme();
            }
            //return option 1
            return $sumGrades/$countGrades;
            //return option 2
            //return $sumGrades/$sumBaremes*20;
        } else {
            return null;
        }
    }

}
