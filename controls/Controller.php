<?php

namespace controls;

class Controller
{
    private $outputData;

    public function __construct($outputData)
    {
        $this->outputData = $outputData;
    }

    public function authenticateAction($usersGetting, $userAccess)
    {
        $usersGetting->authenticate($_POST['username'], $_POST['password'], $userAccess);
        if (!$this->outputData->getOutputData()) {
            return 'Mauvais identifiant ou mot de passe !';
        }
        $user = $usersGetting->getUserByUsername($userAccess, $_POST['username']);
        $_SESSION['isLogged'] = true;
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['user_ID'] = $user->getUser_ID();
        $_SESSION['level'] = $user->getLevel();
        $usersGetting->updateLastConnexion($userAccess, $_SESSION['user_ID']);
        return null;
    }

    public function registerAction($usersGetting, $userAccess)
    {
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            return 'Les mots de passe ne correspondent pas !';
        }
        return $usersGetting->register($_POST['username'], $_POST['password'], $userAccess);
    }

    public function updateUserAction($usersGetting, $userAccess)
    {
        if ($usersGetting->existsUsername($userAccess, $_POST['username']) && $_POST['username']!==$_SESSION['username'])
            return "nom d'utilisateur déja pris";
        if(password_verify($_POST['old_password'], $usersGetting->getUserById($userAccess, $_SESSION['user_ID'])->getPassword())) {
            isset($_POST['password']) ? $newPassword = $_POST['password'] : $newPassword = $_POST['old_password'];
            return $error = $usersGetting->updateUser($_POST['username'], $newPassword, $userAccess);
        }else
            return "mot de passe incorrect";
    }

    public function createTicketAction($ticketsGetting, $ticketAccess)
    {
        $ticketID = $ticketsGetting->createTicket($ticketAccess, $_POST["title"], $_POST["message"]);
        if (isset($_POST["categories"])) {
            $ticketsGetting->addCategoriesToTicket($ticketAccess, $_POST["categories"], $ticketID);
        }
    }

    public function deleteTicket($ticketsGetting, $ticketAccess)
    {
        if($ticketsGetting->isTicketOwner($ticketAccess, $_GET['id'], $_SESSION['user_ID']) || $_SESSION['level'] > 0) {
            $ticketsGetting->deleteTicket($ticketAccess,$_GET['id']);
            return null;
        }else
            return 'Vous n\'avez pas les droits pour supprimer ce ticket !';
    }

    public function editTicket($ticketsGetting, $ticketAccess)
    {
        if($ticketsGetting->isTicketOwner($ticketAccess, $_GET['id'], $_SESSION['user_ID']) || $_SESSION['level'] > 0) {
            $ticketsGetting->editTicket($ticketAccess,$_GET['id'], $_POST["title"], $_POST["message"]);
            return null;
        }else
            return 'Vous n\'avez pas les droits pour modifier ce ticket !';
    }


}