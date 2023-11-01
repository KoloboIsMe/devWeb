<?php
///////////////////////////////////////////////////////////////////////////////
/////////////////////////////  SINGLETONS  ////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/// Instantiates all Singletons.

$categoryDatabase = new Framework\Database\Table\CategoryTable($dbLector);
$commentDatabase = new Framework\Database\Table\CommentTable($dbLector);
$ticketDatabase = new Framework\Database\Table\TicketTable($dbLector);
$userDatabase = new Framework\Database\Table\UserTable($dbLector);
$accessorsLectors = array(
    'categoryAccessLector' => $categoryDatabase,
    'ticketAccessLector' => $ticketDatabase,
    'commentAccessLector' => $commentDatabase,
    'userAccessLector' =>$userDatabase
);

// initialisation de l'output dans une structure pour le transfert des données
$outputData = new Deprecated\OutputData();

// initialisation du controller avec accès a la structure pour le transfert des données
$controller = new Deprecated\Controller($outputData);

// initialisation du presenter avec accès a la structure pour le transfert des données
$presenter = new Deprecated\Presenter($outputData);

//initialisation des services avec la structure pour le transfert des données
$categoriesGetting = new Deprecated\CategoriesGetting($outputData);
$ticketsGetting = new Deprecated\TicketsGetting($outputData);
$commentsGetting = new Deprecated\CommentsGetting($outputData);
$usersGetting = new Deprecated\UsersGetting($outputData);
$dataGetting = array('categoriesGetting' => $categoriesGetting, 'ticketsGetting' => $ticketsGetting, 'commentsGetting' =>$commentsGetting, 'usersGetting' =>$usersGetting);
return;