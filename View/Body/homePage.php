<?php
///////////////////////////////////////////////////////////////////////////////
////////////////////////////// HOME PAGE //////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/// The home page of the application.
/// It is the first view called when arriving at the website.
if (!isset($cards))
{
    return;
}
?>

    <h1>Accueil</h1>
    <?php
    foreach ($cards as $card)
    {
        echo $card;
    }
    ?>

