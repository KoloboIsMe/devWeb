<?php

namespace Database;
use Entity\User;
class AllDatabase
{
    private $tableName;
    private $dbLink;
    public function __construct($tableName)
    {
        $this->dbLink = (new dataBaseConnexion)->connect();
        $this->tableName = $tableName;
    }
    function select($attribute, $data)
    {
        $request = "SELECT * FROM $this->tableName WHERE $attribute = '$data'";

        $result = mysqli_query($this->dbLink, $request);
        if (!($result = mysqli_query($this->dbLink, $request))) {
            echo 'Erreur dans requête<br >';
            // Affiche le type d'erreur.
            echo 'Erreur : ' . mysqli_error($this->dbLink) . '<br>';
            // Affiche la requête envoyée.
            echo 'Requête : ' . $request . '<br>';
            exit();
        }
        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $result;
    }

}