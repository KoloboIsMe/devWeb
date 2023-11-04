<?php

include_once 'controls/Controller.php';
include_once 'controls/Presenter.php';

include_once 'Framework/database/CategoryAccess.php';
include_once 'Framework/database/CommentAccess.php';
include_once 'Framework/database/SPDO.php';
include_once 'Framework/database/TicketAccess.php';
include_once 'Framework/database/UserAccess.php';

include_once 'Framework/entities/Category.php';
include_once 'Framework/entities/Comment.php';
include_once 'Framework/entities/Post.php';
include_once 'Framework/entities/Ticket.php';
include_once 'Framework/entities/User.php';

include_once 'gui/Layout.php';
include_once 'gui/View.php';
include_once 'gui/ViewCategories.php';
include_once 'gui/ViewCreatePosts.php';
include_once 'gui/ViewEditTicket.php';
include_once 'gui/ViewError.php';
include_once 'gui/ViewHomepage.php';
include_once 'gui/ViewLogin.php';
include_once 'gui/ViewRegister.php';
include_once 'gui/ViewPosts.php';
include_once 'gui/ViewUsers.php';

include_once 'service/CategoriesGetting.php';
include_once "service/CommentsGetting.php";
include_once "service/OutputData.php";
include_once "service/TicketsGetting.php";
include_once "service/UsersGetting.php";

$dbAdmin = null;
$dbLector = null;
try {

    define("CHEMIN_VERS_FICHIER_INI", 'config.ini');
    define("BASE_DE_DONNEES", 'metahub_login');
    // construction du modèle
    $dbAdmin = database\SPDO::getInstance("serveur_admin");
    $dbLector = database\SPDO::getInstance("serveur_lecture");

} catch (PDOException $e) {
    print "Erreur de connexion !: " . $e->getMessage() . "<br/>";
    die();
}

$categoryAccessLector = new database\CategoryAccess($dbLector);
$commentAccessLector = new database\CommentAccess($dbLector);
$ticketAccessLector = new database\TicketAccess($dbLector);
$userAccessLector = new database\UserAccess($dbLector);

$ticketAccess = new database\TicketAccess($dbAdmin);
$userAccess = new database\UserAccess($dbAdmin);

// initialisation de l'output dans une structure pour le transfert des données
$outputData = new service\OutputData();

// initialisation du controller avec accès a la structure pour le transfert des données
$controller = new controls\Controller($outputData);

// initialisation du presenter avec accès a la structure pour le transfert des données
$presenter = new controls\Presenter($outputData);

//initialisation des services avec la structure pour le transfert des données
$categoriesGetting = new service\CategoriesGetting($outputData);
$ticketsGetting = new service\TicketsGetting($outputData);
$commentsGetting = new service\CommentsGetting($outputData);
$usersGetting = new service\UsersGetting($outputData);

// chemin de l'URL demandée au navigateur
$url = $_GET['url'] ?? '';

// définition d'une session d'une heure
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();

if (isset($_SESSION['isLogged']) && $_SESSION['isLogged']) {
    $layoutTemplate = 'gui/layoutLogged.html';
} else {
    $layoutTemplate = 'gui/layout.html';
}


if ('registerAction' == $url && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password_confirmation'])) {

    $page = $_GET['id'] ?? $page = null;
    $error = $controller->registerAction($usersGetting, $userAccess);
    if ($error) {
        $url = 'register';
    } else
        $url = 'login_verification';
}
if ('login_verification' == $url && isset($_POST['username']) && isset($_POST['password'])) {

    $page = $_GET['id'] ?? $page = null;
    $error = $controller->authenticateAction($usersGetting, $userAccess);
    if ($error) {
        $page ? $redirect = 'login&id=' . $page : $redirect = 'login';
        $url = 'error';
    } else {
        $page ? header("refresh:0;url=/$page") : header("refresh:0;url=/");
        $url = '/';
    }
}
if ('createPostsAction' == $url && isset($_POST["title"]) && isset($_POST["message"])) {

    $controller->createTicketAction($ticketsGetting, $ticketAccess);
    $url = '/';
    header("refresh:0;url=/");
}
if ('deleteTicketAction' == $url && isset($_GET['id'])) {

    $error = $controller->deleteTicket($ticketsGetting,$ticketAccess);
    if ($error) {
        $redirect = '/';
        $url = 'error';
    }else{
        $url = '/';
        header("refresh:0;url=/");
    }
}
if ('editTicketAction' == $url && isset($_GET['id']) && isset($_POST["title"]) && isset($_POST["message"])) {

    $error = $controller->editTicket($ticketsGetting,$ticketAccess);
    if ($error) {
        $redirect = '/';
        $url = 'error';
    }else{
        $url = '/';
        header("refresh:0;url=/");
    }
}


if ('' == $url || '/' == $url) {

    $ticketsGetting->get5LastPosts($ticketAccessLector);
    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewHomepage($layout, $presenter))->display();

} elseif ('login' == $url) {

    isset($_GET['id']) ? $page = $_GET['id'] : $page = null;
    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewLogin($layout, $page))->display();

} elseif ('register' == $url) {

    isset($_GET['id']) ? $page = $_GET['id'] : $page = null;
    $layout = new gui\Layout($layoutTemplate);
    if (!isset($error))
        $error = null;
    (new gui\ViewRegister($layout, $page, $error))->display();

} elseif ('logout' == $url) {

    session_unset();
    session_destroy();
    header("Location: /");

} elseif ('posts' == $url) {

    if (!isset($_SESSION['isLogged']))
        header('Location: /login&id=' . $url);

    isset($_GET['id']) ? $ticketsGetting->getPostById($ticketAccessLector, $_GET['id']) : $ticketsGetting->getPosts($ticketAccessLector);
    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewPosts($layout, $presenter))->display();

} elseif ('createPosts' == $url) {

    if (!isset($_SESSION['isLogged']))
        header('Location: /login&id=' . $url);

    $categoriesGetting->getCategories($categoryAccessLector);
    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewCreatePosts($layout, $presenter))->display();

} elseif ('categories' == $url) {

    if (!isset($_SESSION['isLogged']))
        header('Location: /login&id=' . $url);

    $category = null;
    if (isset($_GET['id'])) {
        $category = $categoriesGetting->getCategoryById($categoryAccessLector, $_GET['id']);
        $postsID = $categoriesGetting->getPostsIdByCategoryId($categoryAccessLector, $_GET['id']);
        $ticketsGetting->getCategoryPosts($ticketAccessLector, $postsID);
    } else
        $categoriesGetting->getCategories($categoryAccessLector);

    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewCategories($layout, $presenter, $category))->display();

} elseif ('editTicket' == $url) {

    if (!isset($_SESSION['isLogged']))
        header('Location: /login&id=' . $url);

    isset($_GET['id']) ? $ticketsGetting->getPostById($ticketAccessLector, $_GET['id']) : $ticketsGetting->getPosts($ticketAccessLector);
    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewEditTicket($layout, $presenter))->display();

} elseif ('users' == $url) {

    if (!isset($_SESSION['isLogged']))
        header('Location: /login&id=' . $url);

    $user = null;
    if (isset($_GET['id'])) {
        $user = $usersGetting->getUserById($userAccessLector, $_GET['id']);
        //liste des id tes tickets de l'user
        $postsID = $ticketsGetting->getPostsIdByUserId($userAccessLector, $_GET['id']);
        $ticketsGetting->getUserPosts($ticketAccessLector, $postsID);
    } else
        //si l'id nest pas set, afficher tout les user
        $usersGetting->getUsers($userAccessLector);

    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewUsers($layout, $presenter, $user))->display();
}

//elseif (preg_match("/^posts\/\d+$/",$url)===1) {      //cas avec url posts/:id
//    $id = explode('/',$url)[1];
//    $ticketsGetting->getTicketById($ticketAccessLector, $id);
//    $layout = new gui\Layout($layoutTemplate);
//    (new gui\ViewPosts($layout, $presenter))->display();
//
//}
elseif ('lostmdp/' == $url) {

    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewError($layout, $error, $redirect))->display();

} elseif ('error' == $url) {

    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewError($layout, $error, $redirect))->display();

} else {
    $layout = new gui\Layout($layoutTemplate);
    (new gui\ViewError($layout, 'Page introuvable'))->display();
}