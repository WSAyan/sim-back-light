<?php

namespace App\Http\Controllers;

use App\Repositories\Contact\IContactRepository;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private $contactRepo;

    public function __construct(IContactRepository $contactRepo)
    {
        $this->contactRepo = $contactRepo;
    }

    public function showContacts(Request $request)
    {
        return $this->contactRepo->showContacts($request);
    }

    public function store(Request $request)
    {
        return $this->contactRepo->createContact($request);
    }

    public function show($id)
    {
        return $this->contactRepo->getContact($id);
    }

    public function update(Request $request, $id)
    {
        return $this->contactRepo->updateContact($request, $id);
    }

    public function destroy($id)
    {
        return $this->contactRepo->deleteContact($id);
    }
}
