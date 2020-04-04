<?php

declare(strict_types=1);

namespace App\Domain;

final class AboutGymnast
{
    private string $mostFunApparatus;

    private string $explanationAboutMostFunApparatus;

    private string $mostFunCompetition;

    private string $mostFunOrHardestSkill;

    private string $wouldLikeToLearn;

    private string $exampleGymnast;

    private string $anythingElse;

    public static function createFromDataSource(
        string $mostFunApparatus,
        string $explanationAboutMostFunApparatus,
        string $mostFunCompetition,
        string $mostFunOrHardestSkill,
        string $wouldLikeToLearn,
        string $exampleGymnast,
        string $anythingElse
    ): self
    {
        $self                                   = new self();
        $self->mostFunApparatus                 = $mostFunApparatus;
        $self->explanationAboutMostFunApparatus = $explanationAboutMostFunApparatus;
        $self->mostFunCompetition               = $mostFunCompetition;
        $self->mostFunOrHardestSkill            = $mostFunOrHardestSkill;
        $self->wouldLikeToLearn                 = $wouldLikeToLearn;
        $self->exampleGymnast                   = $exampleGymnast;
        $self->anythingElse                     = $anythingElse;

        return $self;
    }

    public function mostFunApparatus(): string
    {
        return $this->mostFunApparatus;
    }

    public function explanationAboutMostFunApparatus(): string
    {
        return $this->explanationAboutMostFunApparatus;
    }

    public function mostFunCompetition(): string
    {
        return $this->mostFunCompetition;
    }

    public function mostFunOrHardestSkill(): string
    {
        return $this->mostFunOrHardestSkill;
    }

    public function wouldLikeToLearn(): string
    {
        return $this->wouldLikeToLearn;
    }

    public function exampleGymnast(): string
    {
        return $this->exampleGymnast;
    }

    public function anythingElse(): string
    {
        return $this->anythingElse;
    }

    private function __construct()
    {
    }
}
