<?php

namespace service;

interface TicketInterface
{
    public function existsTicket($ticketID);
    public function getPostById($ticketid);

    public function getTicketsID();

    public function get5LastTicketsID();

    public function createTicket($title, $message, $date, $author);

    public function addCategoryToTicket($category, $ticketID);

    public function getCategoryIdByLabel($label);

    public function deleteTicket($ticketID);
    public function editTicket($id, $title, $message);
    public function isTicketOwner($ticketID, $userID);
}