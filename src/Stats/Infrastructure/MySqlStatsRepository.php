<?php

namespace App\Stats\Infrastructure;

use App\Shared\Infrastructure\Database;
use App\Stats\Domain\Models\Stat;
use App\Stats\Domain\StatsRepository;
use DateTime;

class MySqlStatsRepository implements StatsRepository
{
    public function __construct(private readonly Database $client)
    {
    }

    public function getMostSearched(int $top, \DateTime $from, \DateTime $until): array
    {
        $statement = <<<SQL
          SELECT query, COUNT(*) AS searches
          FROM search_logs
          WHERE searched_at BETWEEN ? AND ?
          GROUP BY query ORDER BY searches DESC LIMIT ?;
        SQL;
        $params = ['ssi', $this->dateFormat($from), $this->dateFormat($until), $top];

        $results = $this->client->select($statement, $params);

        return array_map(
            fn(array $result) => $this->statFromRow($result),
            $results
        );
    }

    public function getStatsOf(string $query, bool $exact, int $count, DateTime $from, DateTime $until): array
    {
        $statement = $this->getStatementForStatsOfQuery($exact);
        $searchTerm = $exact ? $query : "%$query%";
        $params = ['sssi', $searchTerm, $this->dateFormat($from), $this->dateFormat($until), $count];

        $results = $this->client->select($statement, $params);

        return array_map(
            fn(array $result) => $this->statFromRow($result),
            $results
        );
    }

    private function getStatementForStatsOfQuery(bool $exact): string
    {
        if ($exact) {
            return <<<SQL
              SELECT query, COUNT(*) AS searches
              FROM search_logs
              WHERE query = ? AND searched_at BETWEEN ? AND ?
              GROUP BY query ORDER BY searches DESC LIMIT ?;
            SQL;
        }

        return <<<SQL
          SELECT query, COUNT(*) AS searches
          FROM search_logs
          WHERE query LIKE ? AND searched_at BETWEEN ? AND ?
          GROUP BY query ORDER BY searches DESC LIMIT ?;
        SQL;
    }

    public function registerSearch(string $query): void
    {
        if (empty($query)) {
            return;
        }

        $this->client->insert(
            'INSERT INTO search_logs (query) VALUES (?);',
            ['s', $query]
        );
    }

    private function dateFormat(DateTime $date): string
    {
        return $date->format(MYSQL_DATE_FORMAT);
    }

    private function statFromRow(array $row): Stat
    {
        return new Stat(
            query: $row['query'],
            searches: $row['searches']
        );
    }
}
