<?php

// src/Security/User.php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private $id;
    private $username;
    private $password;
    private $roles = [];

    public function __construct($id, $username, $password, array $roles)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt()
    {
        // You don't need a salt unless you're not using a modern PHP version.
        // See https://symfony.com/doc/4.4/security/entity_provider.html
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
