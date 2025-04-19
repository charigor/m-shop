<?php

namespace App\Services\Patterns\StaticFactory;

class UserFactory {
    public static function createAdmin(): User {
        return new User('admin', 'admin@example.com', true);
    }

    public static function createGuest(): User {
        return new User('guest', null, false);
    }
    public static function createRole(): UserRole{
        return new UserRole('manager');
    }
}
