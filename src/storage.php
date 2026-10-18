<?php

const USERS_FILE = __DIR__ . "/../data/users.json";

function loadUsers(string $filePath): array
{
    if (!file_exists($filePath)) {
        return [];
    }

    $json = file_get_contents($filePath);

    if ($json === false) {
        return [];
    }

    $users = json_decode($json, true);

    return is_array($users) ? $users : [];
}

function saveUsers(string $filePath, array $users): bool
{
    $encodedUsers = json_encode($users, JSON_PRETTY_PRINT);

    if ($encodedUsers === false) {
        return false;
    }


    return file_put_contents($filePath, $encodedUsers, LOCK_EX) !== false;
}


function registerUser(string $filePath, string $username, string $password): ?string
{
    $normalizedUser = normalizeUsername($username);
    if ($normalizedUser === "") {
        return "The user cannot be empty.";
    }
    if ($password === "" || $password !== trim($password)) {
        return "Invalid password.";
    }
    $users = loadUsers($filePath);

    if (isset($users[$normalizedUser])) {
        return "The username is already taken.";
    }


    $users[$normalizedUser] = [
        'password_hash' => password_hash($password, PASSWORD_DEFAULT)
    ];

    if (!saveUsers($filePath, $users)) {
        return 'Could not save the user.';
    }
    return null;
}


function finduser(string $filePath, string $username): ?array
{
    $users = loadUsers($filePath);

    return $users[normalizeUsername($username)] ?? null;
}


function normalizeUsername(string $username): string
{
    return strtolower(trim($username));
}


var_dump(finduser(USERS_FILE, "CHE"));
