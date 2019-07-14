<?php

namespace Squadron\Tests;

use Illuminate\Contracts\Auth\Authenticatable;

class TestUser implements Authenticatable
{
    public $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function isRoot(): bool
    {
        return $this->role === 'root';
    }

    public function isBaseUser(): bool
    {
        return $this->role === 'user';
    }

    public function getAuthIdentifierName(): string
    {
        return 'role';
    }

    public function getAuthIdentifier(): string
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    public function getAuthPassword(): string
    {
        return 'password';
    }

    public function getRememberToken(): string
    {
        return 'remember_token';
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName(): string
    {
        return 'role';
    }
}
