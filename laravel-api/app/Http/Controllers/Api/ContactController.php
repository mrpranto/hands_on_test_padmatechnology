<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ContactServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(ContactServices $services)
    {
        $this->service = $services;
    }

    public function index(Request $request)
    {
        return $this->service->contacts($request);
    }


    public function store(Request $request)
    {
        $this->service
            ->validate($request)
            ->store($request);

        return response()->json([
            'success' => true,
            'message' => 'Contact Create successful'
        ]);
    }


    public function show(Contact $contact): Contact
    {
        return $contact->load('contactDetails', 'groups');
    }


    public function update(Request $request, Contact $contact)
    {
        $this->service
            ->setModel($contact)
            ->validateUpdate($request)
            ->update($request);

        return response()->json([
            'success' => true,
            'message' => 'Contact Update successful'
        ]);
    }


    public function destroy(Contact $contact): JsonResponse
    {
        $this->service
            ->setModel($contact)
            ->deleteContact();

        return response()->json([
            'success' => true,
            'message' => 'Contact Delete successful'
        ]);
    }
}
