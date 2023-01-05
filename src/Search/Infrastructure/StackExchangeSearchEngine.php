<?php

namespace App\Search\Infrastructure;

use App\Search\Domain\Models\Result;
use App\Search\Domain\SearchEngine;
use App\Shared\Domain\HTTPClient;
use App\Shared\Domain\ServerError;
use App\Shared\Infrastructure\Env;

class StackExchangeSearchEngine implements SearchEngine
{
    public function __construct(
        private readonly HTTPClient $client,
    ) {
    }

    public function search(string $query, int $page, int $perPage): array
    {
        if (empty($query)) {
            return [];
        }

        $results = [];

        try {
            $results = $this->client->request(
                method: 'GET',
                url: Env::getSearchProviderUrl() . '/search',
                options: [
                    'query' => [
                        'intitle' => $query,
                        'site' => 'stackoverflow',
                        'page' => $page,
                        'pagesize' => $perPage,
                    ],
                ],
            );
        } catch (ServerError $e) {
            throw new ServerError(
                message: $e->getMessage(),
                statusCode: 502,
                errorData: [
                    'provider_error' => $e->errorData,
                ],
            );
        }

        return array_map(
            fn(array $response) => $this->resultFromResponse($response),
            $results['items'],
        );
    }

    private function resultFromResponse(array $response): Result
    {
        return new Result(
            title: $response['title'],
            answerCount: $response['answer_count'],
            username: $this->getProfileUsername($response),
            profilePictureUrl: $this->getProfilePictureUrl($response),
        );
    }

    private function getProfileUsername(array $response): ?string
    {
        if (isset($response['owner']['display_name'])) {
            return $response['owner']['display_name'];
        }

        return null;
    }

    private function getProfilePictureUrl(array $response): string|null|array
    {
        if (isset($response['owner']['profile_image'])) {
            return $response['owner']['profile_image'];
        }

        return null;
    }
}
