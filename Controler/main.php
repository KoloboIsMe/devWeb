<?php
///////////////////////////////////////////////////////////////////////////////
////////////////////////////////  MAIN  ///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/// The main controler of the app.
/// It redirects to the correct controller.

if (empty($_GET['url']))
{
    $page = 'homepage';
}
else
{
    $page = $_GET['url'];
}

//if(!isset($_SESSION['isLogged'])) {
//    header('Location: /login&id=' . $url);
//}


require 'display.php';

return;