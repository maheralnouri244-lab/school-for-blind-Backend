<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportTicketRequest;
use App\Models\SupportTicket;
use App\Traits\RecordUploadTrait;
use App\Traits\UploadFileTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{use RecordUploadTrait, UploadFileTrait;
public function store(StoreSupportTicketRequest $request): JsonResponse  
{
    $validated = $request->validated();
    $user = Auth::user();

    $attachmentPath = null;
    if ($request->hasFile('audio')) {
        $attachmentPath = $this->uploadRecord($request->file('audio'), 'support/attachments');
    } elseif ($request->hasFile('image')) {
        $attachmentPath = $this->uploadFile($request->file('image'), 'support/attachments');
    }

    $ticket = SupportTicket::create([
        'sender_id'       => $user->id,         
        'sender_type'     => get_class($user), 
        'message'         => $validated['message'], 
        'attachment_path' => $attachmentPath,      
    ]);

    return response()->json([
        'success' => true,
        'message' => 'تم إرسال تذكرة الدعم بنجاح.',
        'data'    => $ticket
    ], 201);
}

}
