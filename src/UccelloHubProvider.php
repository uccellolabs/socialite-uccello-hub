<?php

namespace UccelloLabs\SocialiteUccelloHub;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class UccelloHubProvider extends AbstractProvider
{
    protected string $host;

    public function __construct($request, string $clientId, string $clientSecret, string $redirectUrl, array $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);

        $this->host = rtrim(config('services.uccello-hub.host', ''), '/');
    }

    protected function getAuthUrl(string $state): string
    {
        return $this->buildAuthUrlFromBase("{$this->host}/oauth/authorize", $state);
    }

    protected function getTokenUrl(): string
    {
        return "{$this->host}/oauth/token";
    }

    /**
     * @return array{id: int, name: string, email: string, email_verified_at: string|null, current_team_id: int|null, preferred_locale: string|null}
     */
    protected function getUserByToken(string $token): array
    {
        $response = $this->getHttpClient()->get("{$this->host}/api/user", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Accept'        => 'application/json',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'nickname' => null,
            'avatar'   => null,
        ]);
    }
}
