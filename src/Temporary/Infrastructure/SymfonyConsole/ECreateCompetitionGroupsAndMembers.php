<?php
declare(strict_types=1);

namespace App\Temporary\Infrastructure\SymfonyConsole;

use App\CompetitionGroup\Domain\CompetitionGroupMemberTrainingParticipationId;
use App\CompetitionGroup\Domain\CompetitionGroupTrainingId;
use App\CompetitionGroup\Domain\CompetitionGroupTrainingTimeId;
use App\CompetitionGroup\Domain\TrainingPresence;
use App\Shared\Domain\AboutCompetitionGroupMemberId;
use App\Shared\Domain\CompetitionGroupCompetitionResultId;
use App\Shared\Domain\CompetitionGroupId;
use App\Shared\Domain\CompetitionGroupMemberId;
use App\Shared\Domain\CompetitionGroupMemberRoleId;
use App\Shared\Domain\SystemClock;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ECreateCompetitionGroupsAndMembers extends Command
{
    private Connection      $connection;
    private SystemClock     $clock;
    protected static        $defaultName = 'app:create-competition-groups-and-members';

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock      = $clock;
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $groepen = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('groepen')
            ->execute()
            ->fetchAll();
        foreach ($groepen as $groep) {
            $idMapping = $this->insertCompetitionGroupRecord($groep);
        }
        $personen = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('persoon')
            ->execute()
            ->fetchAll();
        foreach ($personen as $persoon) {
            $this->insertCompetitionGroupMemberRecord($persoon, $idMapping);
        }

        return 0;
    }

    private function insertCompetitionGroupRecord(array $groep): array
    {
        $idMapping                                   = [];
        $idMapping['competitionGroupTrainingTimeId'] = [];
        $idMapping['competitionGroupTrainingId']     = [];
        $groupId                                     = CompetitionGroupId::generate()->toString();
        $this->connection->createQueryBuilder()
            ->insert('competition_group')
            ->values(
                [
                    'id'         => ':id',
                    'name'       => ':name',
                    'sort_order' => ':sortOrder',
                ]
            )->setParameters(
                [
                    'id'        => $groupId,
                    'name'      => $groep['name'],
                    'sortOrder' => $groep['id'],
                ],
            )->execute();
        $competitionResults = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('wedstrijduitslagen')
            ->where('groepen_id = :groupId')
            ->setParameter('groupId', $groep['id'])
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($competitionResults as $competitionResult) {
            $this->connection->createQueryBuilder()
                ->insert('competition_group_competition_result')
                ->values(
                    [
                        'id'                   => ':id',
                        'competition_group_id' => ':competitionGroupId',
                        'file_name'            => ':fileName',
                        'competition_date'     => ':competitionDate',
                        'name'                 => ':name',
                    ]
                )->setParameters(
                    [
                        'id'                 => CompetitionGroupCompetitionResultId::generate()->toString(),
                        'competitionGroupId' => $groupId,
                        'fileName'           => $competitionResult['locatie'],
                        'competitionDate'    => $competitionResult['datum'],
                        'name'               => $competitionResult['naam'],
                    ],
                )->execute();
        }
        $trainingen = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('trainingen')
            ->where('groepen_id = :groupId')
            ->setParameter('groupId', $groep['id'])
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($trainingen as $training) {
            $competitionGroupTrainingTimeId = CompetitionGroupTrainingTimeId::generate()->toString();
            $this->connection->createQueryBuilder()
                ->insert('competition_group_training_time')
                ->values(
                    [
                        'id'                   => ':id',
                        'competition_group_id' => ':competitionGroupId',
                        'training_day'         => ':trainingDay',
                        'training_start_time'  => ':trainingStartTime',
                        'training_end_time'    => ':trainingEndTime',
                        'sort_order'           => ':sortOrder',
                    ]
                )->setParameters(
                    [
                        'id'                 => $competitionGroupTrainingTimeId,
                        'competitionGroupId' => $groupId,
                        'trainingDay'        => $this->dayToEnglish($training['dag']),
                        'trainingStartTime'  => \DateTimeImmutable::createFromFormat('H.i', $training['tijdvan']),
                        'trainingEndTime'    => \DateTimeImmutable::createFromFormat('H.i', $training['tijdtot']),
                        'sortOrder'          => 0,
                    ],
                    [
                        'trainingStartTime' => Types::DATETIME_IMMUTABLE,
                        'trainingEndTime'   => Types::DATETIME_IMMUTABLE,
                    ]
                )->execute();
            $idMapping['competitionGroupTrainingTimeId'][$training['id']] = $competitionGroupTrainingTimeId;
            $trainingsData                                                = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('trainingsdata')
                ->where('trainingdata_id = :trainingId')
                ->andWhere('lesdatum > :date')
                ->setParameters(
                    [
                        'trainingId' => $training['id'],
                        'date'       => '2019-01-01',
                    ]
                )
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($trainingsData as $trainingsDatum) {
                $competitionGroupTrainingId = CompetitionGroupTrainingId::generate()->toString();
                $this->connection->createQueryBuilder()
                    ->insert('competition_group_training')
                    ->values(
                        [
                            'id'                                 => ':id',
                            'competition_group_training_time_id' => ':competitionGroupTrainingTimeId',
                            'training_date'                      => ':trainingDate',
                        ]
                    )->setParameters(
                        [
                            'id'                             => $competitionGroupTrainingId,
                            'competitionGroupTrainingTimeId' => $competitionGroupTrainingTimeId,
                            'trainingDate'                   => $trainingsDatum['lesdatum'],
                        ],
                        [
                            'trainingStartTime' => Types::DATETIME_IMMUTABLE,
                            'trainingEndTime'   => Types::DATETIME_IMMUTABLE,
                        ]
                    )->execute();
                $idMapping['competitionGroupTrainingId'][$trainingsDatum['id']] = $competitionGroupTrainingId;
            }
        }

        return $idMapping;
    }

    private function insertCompetitionGroupMemberRecord(array $persoon, array $idMapping): void
    {
        $accountId       = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('competition_group_account')
            ->where('user_id = :userId')
            ->setParameter('userId', $persoon['user_id'])
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);
        $pictureFileName = null;
        if ($persoon['foto_id']) {
            $picture         = $this->connection->createQueryBuilder()
                ->select('locatie')
                ->from('selectiefoto')
                ->where('id = :id')
                ->setParameter('id', $persoon['foto_id'])
                ->execute()
                ->fetch(\PDO::FETCH_ASSOC);
            $pictureFileName = $picture['locatie'];
        }
        $floorMusicFileName = null;
        if ($persoon['vloermuziek_id']) {
            $floorMusic         = $this->connection->createQueryBuilder()
                ->select('locatie')
                ->from('vloermuziek')
                ->where('id = :id')
                ->setParameter('id', $persoon['vloermuziek_id'])
                ->execute()
                ->fetch(\PDO::FETCH_ASSOC);
            $floorMusicFileName = $floorMusic['locatie'];
        }
        $competitionGroupMemberId = CompetitionGroupMemberId::generate()->toString();
        $this->connection->createQueryBuilder()
            ->insert('competition_group_member')
            ->values(
                [
                    'id'                           => ':id',
                    'competition_group_account_id' => ':competitionGroupAccountId',
                    'first_name'                   => ':firstName',
                    'last_name'                    => ':lastName',
                    'date_of_birth'                => ':dateOfBirth',
                    'picture_file_name'            => ':pictureFileName',
                    'floor_music_file_name'        => ':floorMusicFileName',
                ]
            )->setParameters(
                [
                    'id'                        => $competitionGroupMemberId,
                    'competitionGroupAccountId' => $accountId['id'],
                    'firstName'                 => $persoon['voornaam'],
                    'lastName'                  => $persoon['achternaam'],
                    'dateOfBirth'               => $persoon['geboortedatum'],
                    'pictureFileName'           => $pictureFileName,
                    'floorMusicFileName'        => $floorMusicFileName,
                ],
            )->execute();
        $aboutGymnast = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('stukje')
            ->where('id = :id')
            ->setParameter('id', $persoon['stukje_id'])
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);
        if ($aboutGymnast['toestelleuk']) {
            $this->connection->createQueryBuilder()
                ->insert('about_competition_group_member')
                ->values(
                    [
                        'id'                                   => ':id',
                        'competition_group_member_id'          => ':competitionGroupMemberId',
                        'most_fun_apparatus'                   => ':mostFunApparatus',
                        'explanation_about_most_fun_apparatus' => ':explanationAboutMostFunApparatus',
                        'most_fun_competition'                 => ':mostFunCompetition',
                        'most_fun_or_hardest_skill'            => ':mostFunOrHardestSkill',
                        'would_like_to_learn'                  => ':wouldLikeToLearn',
                        'example_gymnast'                      => ':exampleGymnast',
                        'anything_else'                        => ':anythingElse',
                    ]
                )->setParameters(
                    [
                        'id'                               => AboutCompetitionGroupMemberId::generate()->toString(),
                        'competitionGroupMemberId'         => $competitionGroupMemberId,
                        'mostFunApparatus'                 => $aboutGymnast['toestelleuk'],
                        'explanationAboutMostFunApparatus' => $aboutGymnast['omdattoestelleuk'],
                        'mostFunCompetition'               => $aboutGymnast['wedstrijd'],
                        'mostFunOrHardestSkill'            => $aboutGymnast['element'],
                        'wouldLikeToLearn'                 => $aboutGymnast['leren'],
                        'exampleGymnast'                   => $aboutGymnast['voorbeeld'],
                        'anythingElse'                     => $aboutGymnast['overig'],
                    ],
                )->execute();
        }
        $roles = $this->connection->createQueryBuilder()
            ->select('f.functie', 'g.name')
            ->from('functie', 'f')
            ->join('f', 'groepen', 'g', 'f.groepen_id = g.id')
            ->where('persoon_id = :persoonId')
            ->setParameter('persoonId', $persoon['id'])
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($roles as $role) {
            $group   = $this->connection->createQueryBuilder()
                ->select('id')
                ->from('competition_group')
                ->where('name = :name')
                ->setParameter('name', $role['name'])
                ->execute()
                ->fetch(\PDO::FETCH_ASSOC);
            $groupId = $group['id'];
            $this->connection->createQueryBuilder()
                ->insert('competition_group_member_role')
                ->values(
                    [
                        'id'                          => ':id',
                        'competition_group_member_id' => ':competitionGroupMemberId',
                        'competition_group_id'        => ':competitionGroupId',
                        'role'                        => ':role',
                    ]
                )->setParameters(
                    [
                        'id'                       => CompetitionGroupMemberRoleId::generate()->toString(),
                        'competitionGroupMemberId' => $competitionGroupMemberId,
                        'competitionGroupId'       => $groupId,
                        'role'                     => $role['functie'],
                    ],
                )->execute();
        }
        $personenTrainingen = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('personen_trainingen')
            ->where('persoon_id = :persoonId')
            ->setParameter('persoonId', $persoon['id'])
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($personenTrainingen as $persoonTraining) {
            if (!isset($idMapping['competitionGroupTrainingTimeId'][$persoonTraining['trainingen_id']])) {
                continue;
            }
            $this->connection->createQueryBuilder()
                ->insert('competition_group_member_training_time_subscription')
                ->values(
                    [
                        'id'                                 => ':id',
                        'competition_group_training_time_id' => ':competitionGroupTrainingTimeId',
                        'competition_group_member_id'        => ':competitionGroupMemberId',
                    ]
                )->setParameters(
                    [
                        'id'                             => CompetitionGroupMemberRoleId::generate()->toString(),
                        'competitionGroupTrainingTimeId' => $idMapping['competitionGroupTrainingTimeId'][$persoonTraining['trainingen_id']],
                        'competitionGroupMemberId'       => $competitionGroupMemberId,
                    ],
                )->execute();
        }
        $aanwezigheid = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('aanwezigheid')
            ->where('persoon_id = :persoonId')
            ->setParameter('persoonId', $persoon['id'])
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($aanwezigheid as $aanwezigheidsItem) {
            if (!isset($idMapping['competitionGroupTrainingId'][$aanwezigheidsItem['trainingdata_id']])) {
                continue;
            }
            $this->connection->createQueryBuilder()
                ->insert('competition_group_member_training_participation')
                ->values(
                    [
                        'id'                            => ':id',
                        'competition_group_member_id'   => ':competitionGroupMemberId',
                        'competition_group_training_id' => ':competitionGroupTrainingId',
                        'presence'                      => ':presence',
                    ]
                )->setParameters(
                    [
                        'id'                         => CompetitionGroupMemberTrainingParticipationId::generate(
                        )->toString(),
                        'competitionGroupMemberId'   => $competitionGroupMemberId,
                        'competitionGroupTrainingId' => $idMapping['competitionGroupTrainingId'][$aanwezigheidsItem['trainingdata_id']],
                        'presence'                   => $this->convertPresence($aanwezigheidsItem['aanwezig']),
                    ],
                )->execute();
        }
    }

    private function dayToEnglish(string $dayInDutch): string
    {
        switch (strtolower($dayInDutch)) {
            case 'maandag':
                return 'Monday';
            case 'dinsdag':
                return 'Tuesday';
            case 'woensdag':
                return 'Wednesday';
            case 'donderdag':
                return 'Thursday';
            case 'vrijdag':
                return 'Friday';
            case 'zaterdag':
                return 'Saturday';
            case 'zondag':
                return 'Sunday';
            default:
                throw new \LogicException('could not translate day ' . $dayInDutch);
        }
    }

    private function convertPresence(string $old): string
    {
        switch (strtolower($old)) {
            case 'x':
                return TrainingPresence::PRESENT;
            case 'a':
                return TrainingPresence::ABSENT_WITH_NOTICE;
            case '-':
                return TrainingPresence::ABSENT_WITHOUT_NOTICE;
            default:
                throw new \LogicException('could not translate presence ' . $old);
        }
    }
}
