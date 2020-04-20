<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

final class TrainingPresence
{
    const ABSENT_WITHOUT_NOTICE = 'absent without notice';
    const ABSENT_WITH_NOTICE    = 'absent with notice';
    const PRESENT               = 'present';
}
