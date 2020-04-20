<?php
declare(strict_types=1);

namespace App\PublicInformation\Domain\Competition;

use App\Shared\Domain\CompetitionGroupId;

final class CompetitionGroup
{
    private CompetitionGroupId $id;
    private string $name;

    public static function fromDataSource(
        CompetitionGroupId $id,
        string $name
    ): self {
        $self       = new self();
        $self->id   = $id;
        $self->name = $name;

        return $self;
    }

    public function id(): CompetitionGroupId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    private function __construct()
    {
    }
}
