<?php

namespace Tests\Search\Application;

use App\Shared\Domain\ServerError;
use Prophecy\Argument;
use Tests\ApiClient;
use Tests\Search\Factories\ResultFactory;
use Tests\TestCase;

final class SearchApiTest extends TestCase
{
    private function setUpCommonMockUpCalls(ApiClient $client)
    {
        $client->cacheProphecy->has(Argument::type('string'))->willReturn(false);
        $client->cacheProphecy->get(Argument::type('string'))->willReturn(null);
        $client->cacheProphecy->set(
            key: Argument::type('string'),
            value: Argument::type('string'),
            ttl: Argument::type('int')
        );

        $client->searchEngineProphecy->search(
            query: Argument::type('string'),
            page: Argument::type('int'),
            perPage: Argument::type('int')
        )->willReturn([]);
    }

    public function testCanSearchWithoutQuery()
    {
        $client = $this->createApiClient();
        $this->setUpCommonMockUpCalls($client);

        $response = $client->executeRequest(
            method: 'GET',
            path: '/search'
        );

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);

        $responseData = json_decode($response->getBody(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals(0, count($responseData['data']['results']));
    }

    public function testCanSearchWithoutPagination()
    {
        $client = $this->createApiClient();
        $this->setUpCommonMockUpCalls($client);

        $expectedResult = ResultFactory::create();

        $client->searchEngineProphecy->search(
            query: 'php',
            page: 1,
            perPage: 10
        )->willReturn([$expectedResult]);

        $response = $client->executeRequest(
            method: 'GET',
            path: '/search',
            query: [
                'query' => 'php'
            ]
        );

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);

        $responseData = json_decode($response->getBody(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals(1, count($responseData['data']['results']));

        $resultFound = $responseData['data']['results'][0];
        $this->assertEquals($expectedResult->title, $resultFound['title']);
        $this->assertEquals($expectedResult->answerCount, $resultFound['answer_count']);
        $this->assertEquals($expectedResult->username, $resultFound['username']);
        $this->assertEquals($expectedResult->profilePictureUrl, $resultFound['profile_picture_url']);
    }

    public function testCanSearchWithPagination()
    {
        $client = $this->createApiClient();
        $this->setUpCommonMockUpCalls($client);

        $expectedResults = ResultFactory::createBatch(5);
        $resultsTitlesMap = array_reduce($expectedResults, function ($carry, $item) {
            $carry[$item->title] = $item;
            return $carry;
        }, []);

        $client->searchEngineProphecy->search(
            query: 'php',
            page: 10,
            perPage: 5
        )->willReturn($expectedResults);

        $response = $client->executeRequest(
            method: 'GET',
            path: '/search',
            query: [
                'query' => 'php',
                'page' => 10,
                'pagesize' => 5
            ]
        );

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);

        $responseData = json_decode($response->getBody(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals(5, count($responseData['data']['results']));

        foreach ($responseData['data']['results'] as $resultFound) {
            $this->assertTrue(isset($resultsTitlesMap[$resultFound['title']]));
        }
    }

    public function testGetBadGatewayErrorIfProviderFails()
    {
        $client = $this->createApiClient();
        $this->setUpCommonMockUpCalls($client);

        $client->searchEngineProphecy->search(
            query: 'php',
            page: 26,
            perPage: 10
        )->willThrow(new ServerError(
            message: 'Bad Gateway',
            statusCode: 502,
            errorData: [
                'provider_error' => [
                    'error_id' => 403,
                    'error_message' => 'page above 25 requires access token or app key',
                    'error_name' => 'access_denied',
                ]
            ]
        ));

        $response = $client->executeRequest(
            method: 'GET',
            path: '/search',
            query: [
                'query' => 'php',
                'page' => 26,
            ]
        );

        $statusCode = $response->getStatusCode();

        $this->assertEquals(502, $statusCode);

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('Bad Gateway', $data['message']);

        $providerError = $data['data']['provider_error'];
        $this->assertEquals(403, $providerError['error_id']);
        $this->assertEquals(
            'page above 25 requires access token or app key',
            $providerError['error_message']
        );
        $this->assertEquals('access_denied', $providerError['error_name']);
    }

    public function testDoesItCallSaveStats()
    {
        $client = $this->createApiClient();
        $this->setUpCommonMockUpCalls($client);
        $client->statsRepositoryProphecy->registerSearch('php')->shouldBeCalledTimes(1);

        $response = $client->executeRequest(
            method: 'GET',
            path: '/search',
            query: [
                'query' => 'php',
            ]
        );

        $statusCode = $response->getStatusCode();
        $this->assertEquals(200, $statusCode);
    }

    public function testSaveInCache()
    {
        $client = $this->createApiClient();
        $this->setUpCommonMockUpCalls($client);
        $cacheKey = $this->getCacheKeyFrom('/search', 'php', 1, 10);
        $client->cacheProphecy
        ->set($cacheKey, Argument::type('string'), Argument::type('int'))
        ->shouldBeCalledTimes(1);

        $response = $client->executeRequest(
            method: 'GET',
            path: '/search',
            query: [
                'query' => 'php',
            ]
        );

        $statusCode = $response->getStatusCode();
        $this->assertEquals(200, $statusCode);
    }

    public function testUseCachedResponse()
    {
        $client = $this->createApiClient();
        $this->setUpCommonMockUpCalls($client);
        $cacheKey = $this->getCacheKeyFrom('/search', 'php', 1, 10);

        $client->cacheProphecy
        ->has($cacheKey)
        ->willReturn(true);

        $result = ResultFactory::create();
        $client->cacheProphecy
        ->get($cacheKey)
        ->willReturn(json_encode([
            'status' => 'success',
            'data' => [
                'results' => [$result->toArray()]
            ]
        ]));

        $client->searchEngineProphecy->search(
            query: Argument::any(),
            page: Argument::any(),
            perPage: Argument::any()
        )->shouldNotBeCalled();

        $response = $client->executeRequest(
            method: 'GET',
            path: '/search',
            query: [
                'query' => 'php',
            ]
        );

        $statusCode = $response->getStatusCode();
        $this->assertEquals(200, $statusCode);

        $responseData = json_decode($response->getBody(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals(1, count($responseData['data']['results']));

        $apiResult = $responseData['data']['results'][0];
        $this->assertEquals($result->title, $apiResult['title']);
        $this->assertEquals($result->answerCount, $apiResult['answer_count']);
        $this->assertEquals($result->username, $apiResult['username']);
        $this->assertEquals($result->profilePictureUrl, $apiResult['profile_picture_url']);
    }

    private function getCacheKeyFrom(string ...$parts)
    {
        return urlencode(implode(':', $parts));
    }
}
