<?php

namespace Tests\Stats\Infrastructure;

use App\Shared\Infrastructure\Database;
use App\Stats\Infrastructure\MySqlStatsRepository;
use Prophecy\Argument;
use Tests\Stats\Factories\StatFactory;
use Tests\TestCase;

final class MySqlStatsRepositoryTest extends TestCase
{
    public function testGetMostSearched()
    {
        $dbClientProphecy = $this->prophet->prophesize(Database::class);

        $expectStat = StatFactory::create();
        $dbClientProphecy->select(
            statement: Argument::type('string'),
            params: [
                'ssi',
                '2021-01-01 00:00:00',
                '2021-01-31 00:00:00',
                10
            ]
        )->willReturn([$expectStat->toArray()]);

        $repo = new MySqlStatsRepository($dbClientProphecy->reveal());
        $stats = $repo->getMostSearched(
            top: 10,
            from: new \DateTime('2021-01-01'),
            until: new \DateTime('2021-01-31')
        );

        $this->assertIsArray($stats);
        $this->assertCount(1, $stats);
        $this->assertEquals($expectStat->query, $stats[0]->query);
        $this->assertEquals($expectStat->searches, $stats[0]->searches);
    }

    public function testCanGetStatsOfQuery()
    {
        $dbClientProphecy = $this->prophet->prophesize(Database::class);

        $expectStat = StatFactory::create();
        $dbClientProphecy->select(
            statement: Argument::type('string'),
            params: [
                'sssi',
                '%php%',
                '2021-01-01 00:00:00',
                '2021-01-31 00:00:00',
                15
            ]
        )->willReturn([$expectStat->toArray()]);

        $repo = new MySqlStatsRepository($dbClientProphecy->reveal());
        $stats = $repo->getStatsOf(
            query: 'php',
            exact: false,
            from: new \DateTime('2021-01-01'),
            until: new \DateTime('2021-01-31'),
            count: 15,
        );

        $this->assertIsArray($stats);
        $this->assertCount(1, $stats);
        $this->assertEquals($expectStat->query, $stats[0]->query);
        $this->assertEquals($expectStat->searches, $stats[0]->searches);
    }

    public function testCanGetStatsOfExactQuery()
    {
        $dbClientProphecy = $this->prophet->prophesize(Database::class);

        $expectStat = StatFactory::create();
        $dbClientProphecy->select(
            statement: Argument::type('string'),
            params: [
                'sssi',
                'php',
                '2021-01-01 00:00:00',
                '2021-01-31 00:00:00',
                15
            ]
        )->willReturn([$expectStat->toArray()]);

        $repo = new MySqlStatsRepository($dbClientProphecy->reveal());
        $stats = $repo->getStatsOf(
            query: 'php',
            exact: true,
            from: new \DateTime('2021-01-01'),
            until: new \DateTime('2021-01-31'),
            count: 15,
        );

        $this->assertIsArray($stats);
        $this->assertCount(1, $stats);
        $this->assertEquals($expectStat->query, $stats[0]->query);
        $this->assertEquals($expectStat->searches, $stats[0]->searches);
    }

    public function testCanRegisterASearch()
    {
        $dbClientProphecy = $this->prophet->prophesize(Database::class);

        $dbClientProphecy->insert(
            statement: Argument::type('string'),
            params: [
                's',
                'php',
            ]
        )->shouldBeCalledTimes(1);

        $repo = new MySqlStatsRepository($dbClientProphecy->reveal());
        $repo->registerSearch(
            query: 'php',
        );

        $this->assertTrue(true);
    }

    public function testCannotRegisterAnEmptySearchQuery()
    {
        $dbClientProphecy = $this->prophet->prophesize(Database::class);

        $dbClientProphecy->insert(
            statement: Argument::type('string'),
            params: Argument::type('array'),
        )->shouldNotBeCalled();

        $repo = new MySqlStatsRepository($dbClientProphecy->reveal());
        $repo->registerSearch(
            query: '',
        );

        $this->assertTrue(true);
    }
}
