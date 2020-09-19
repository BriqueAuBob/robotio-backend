<?php

namespace App\Guards;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

class TokenGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The currently authenticated token.
     *
     * @var Model
     */
    protected $token;

    /**
     * The name of the query string item from the request containing the API token.
     *
     * @var string
     */
    protected $inputKey;

    /**
     * The name of the token "column" in persistent storage.
     *
     * @var string
     */
    protected $storageKey;

    /**
     * Create a new token guard instance.
     *
     * @param UserProvider $provider
     * @param Request $request
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->inputKey = 'access_token';
        $this->storageKey = 'access_token';
    }

    /**
     * Get the currently authenticated user.
     * @return Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        if ($this->user === null && ($token = $this->token()) && $token->isNotExpired()) {
            $this->user = (clone $token)->user;
        }

        return $this->user;
    }

    /**
     * Get the currently token model.
     * @return Model|null
     */
    public function token(): ?Model
    {
        if ($this->token === null) {
            $this->token = $this->retrieveTokenForRequest(
                [$this->inputKey => $this->getTokenCredentials()]
            );
        }

        return $this->token;
    }

    /**
     * Get the token credentials for the current request.
     * @return null|string
     */
    protected function getTokenCredentials(): ?string
    {
        $token = $this->request->get($this->inputKey);

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        return $token;
    }

    /**
     * Retrieve the token for the current request.
     * @param array $credentials
     * @return Authenticatable|Model
     */
    protected function retrieveTokenForRequest(array $credentials)
    {
        if (array_key_exists($this->inputKey, $credentials)) {
            return $this->provider->retrieveByCredentials(
                [$this->storageKey => $credentials[$this->inputKey]]
            );
        }
    }

    /**
     * Validate a user's credentials.
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        if ($token = $this->retrieveTokenForRequest($credentials)) {
            return $token->isNotExpired();
        }

        return false;
    }

    /**
     * Set the current request instance.
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }
}
