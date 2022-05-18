<?php

namespace App\Repositories\Contact;

use Illuminate\Http\Request;

interface IContactRepository
{
    public function showContacts(Request $request);

    public function getContactsByUserId($user_id);

    public function createContact(Request $request);

    public function createContactWithData($user_id, $name, $phone, $address, $description);

    public function getContact($id);

    public function currentContactID();

    public function updateContact($id, Request $request);

    public function deleteContact($id);
}
