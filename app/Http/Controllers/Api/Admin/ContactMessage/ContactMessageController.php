<?php

namespace App\Http\Controllers\Api\Admin\ContactMessage;

use App\Models\ContactInfo;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Mail\ContactMessageMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Content;
use App\Http\Requests\StoreContactMessageRequest;

class ContactMessageController extends Controller
{
    public function storeMessage(StoreContactMessageRequest  $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                [],
                ['name', 'email', 'subject', 'message']
            );
            $contactMessage = ContactMessage::create($data);
            $platformEmail = ContactInfo::first()?->email;
            if ($platformEmail) {
                Mail::to($platformEmail)->send(new ContactMessageMail($data));
            }
            return mainResponse(true, 'ContactMessage created successfully.', compact('contactMessage'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }


    public function getMessages()
    {
        $getMessages = ContactMessage::orderBy('created_at', 'desc')
            ->paginate(10);
        if ($getMessages->isEmpty()) {
            return mainResponse(false, 'No active services found.', [], [], 404, null, false);
        }

        return mainResponse(true, 'Message sections fetched.', compact('getMessages'), [], 200);
    }
}
