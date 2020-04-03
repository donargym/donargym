<?php

declare(strict_types=1);

namespace App\Infrastructure\DoctrineDbal;

use App\Domain\CompetitionGroupFunction;
use App\Domain\CompetitionGroupMember;
use App\Domain\CompetitionGroupMembers;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionGroupMemberRepository
{
    private Connection $connection;
    private string $defaultPictureFileName;

    public function __construct(Connection $connection)
    {
        $this->connection             = $connection;
        $this->defaultPictureFileName = 'plaatje.jpg';
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
            new \DateTimeImmutable($row['geboortedatum']),
            $row['picture_file_name'] ?: $this->defaultPictureFileName,
            CompetitionGroupFunction::fromString($row['functie'])
        );
    }
}
