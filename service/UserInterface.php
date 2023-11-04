<?php

namespace service;

interface UserInterface
{

    public function getUserByUsername($username);

    public function getUsersUsername();

    public function isUser($login, $password);

    public function register($username, $password, $date);
    public function updateLastConnexion($user_ID);
    public function getUserById($id);
    public function getUsersID();
    public function getPostsIdByUserId($userId);
}