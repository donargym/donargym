<?php

declare(strict_types=1);

namespace App\Temporary\Infrastructure\SymfonyConsole;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixNewsPostDataCommand extends Command
{
    private Connection $connection;

    protected static $defaultName = 'app:fix-news-post-data';

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $newsPosts = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('nieuwsbericht')
            ->execute()
            ->fetchAll();

        foreach ($newsPosts as $newsPost) {
            $this->connection->createQueryBuilder()
                ->update('nieuwsbericht')
                ->set('created_at', ':createdAt')
                ->where('id = :id')
                ->setParameters(
                    [
                        'createdAt' => \DateTimeImmutable::createFromFormat('d-m-Y: H:i', $newsPost['datumtijd']),
                        'id'        => $newsPost['id'],
                    ],
                    ['createdAt' => Types::DATETIME_IMMUTABLE]
                )
                ->execute();
        }

        return 0;
    }
}
