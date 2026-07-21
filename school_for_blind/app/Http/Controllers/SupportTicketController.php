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

    $attachments = [];
    
    if ($request->hasFile('audio')) {
        $audio = $this->uploadRecord($request->file('audio'), 'support/attachments');
        $attachments['audio'] = str_starts_with($audio, 'storage/') ? $audio : 'storage/' . $audio;
    }

    if ($request->hasFile('image')) {
        $image = $this->uploadFile($request->file('image'), 'support/attachments');
        $attachments['image'] = str_starts_with($image, 'storage/') ? $image : 'storage/' . $image;
    }

    $attachmentPath = empty($attachments) ? null : $attachments;

    $ticket = SupportTicket::create([
        'sender_id'       => $user->id,         
        'sender_type'     => get_class($user), 
        'message'         => $validated['message'] ?? null, 
        'attachment_path' => $attachmentPath, 
    ]);

    return response()->json([
        'success' => true,
        'message' => 'تم إرسال تذكرة الدعم بنجاح.',
        'data'    => $ticket
    ], 201);
}}