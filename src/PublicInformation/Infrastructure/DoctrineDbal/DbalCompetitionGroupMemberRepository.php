<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Competition\CompetitionGroupFunction;
use App\PublicInformation\Domain\Competition\CompetitionGroupMember;
use App\PublicInformation\Domain\Competition\CompetitionGroupMembers;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionGroupMemberRepository
{
    private Connection $connection;
    private SystemClock $clock;
    private string $defaultPictureFileName;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection             = $connection;
        $this->clock                  = $clock;
        $this->defaultPictureFileName = 'plaatje.jpg';
    }

    public function find(int $id): ?CompetitionGroupMember
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'm.voornaam',
                'm.achternaam',
                'm.geboortedatum',
                'p.locatie AS picture_file_name',
                'f.functie'
            )->from('persoon', 'm')
            ->join('m', 'functie', 'f', 'f.persoon_id = m.id')
            ->leftJoin('m', 'selectiefoto', 'p', 'p.id = m.foto_id')
            ->andWhere('m.id = :id')
            ->setParameter('id', $id)
            ->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAllForGroup(int $groupId): CompetitionGroupMembers
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'm.id',
                'm.voornaam',
                'm.achternaam',
                'm.geboortedatum',
                'p.locatie AS picture_file_name',
                'f.functie'
            )->from('persoon', 'm')
            ->join('m', 'functie', 'f', 'f.persoon_id = m.id')
            ->leftJoin('m', 'selectiefoto', 'p', 'p.id = m.foto_id')
            ->orderBy('m.geboortedatum', 'ASC')
            ->andWhere('f.groepen_id = :groupId')
            ->setParameter('groupId', $groupId)
            ->execute();

        $competitionGroupMembers = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $competitionGroupMembers[] = $this->hydrate($row);
        }

        return CompetitionGroupMembers::fromArray($competitionGroupMembers);
    }

    private function hydrate(array $row): CompetitionGroupMember
    {
        return CompetitionGroupMember::createFromDataSource(
            (int) $row['id'],
            $row['voornaam'],
            $row['achternaam'],
            new DateTimeImmutable($row['geboortedatum']),
            $row['picture_file_name'] ?: $this->defaultPictureFileName,
            CompetitionGroupFunction::fromString($row['functie']),
            $this->clock
        );
    }
}
