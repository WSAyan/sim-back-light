<?php


namespace App\Repositories\Contact;

use App\Contact;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactRepository extends BaseRepository implements IContactRepository
{
    public function showContacts(Request $request)
    {
    }

    public function getContactsByUserId($user_id)
    {
        return DB::table('contacts')
            ->where("contacts.user_id", $user_id)
            ->get();
    }

    public function createContact(Request $request)
    {
    }

    public function createContactWithData($user_id, $name, $phone, $address, $description)
    {
        $contact = new Contact(
            [
                "user_id" => $user_id,
                "name" => $name,
                "phone" => $phone,
                "address" => $address,
                "description" => $description
            ]

        );

        $contact->save();

        return $contact;
    }

    public function getContact($id)
    {
    }

    public function currentContactID()
    {
    }

    public function updateContact(Request $request, $id)
    {
    }

    public function deleteContact($id)
    {
    }
}
