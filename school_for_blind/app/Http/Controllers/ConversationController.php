<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\MessageSent;

class ConversationController extends Controller
{

    private function determineAttachmentType($mimeType)
    {
        if (Str::startsWith($mimeType, 'image/')) {
            return 'image';
        }

        if (Str::startsWith($mimeType, 'audio/')) {
            return 'voice';
        }

        if (Str::startsWith($mimeType, 'video/')) {
            return 'video';
        }

        return 'file';
    }

    // FOR TEACHER
    public function getTeacherChannels(Request $request)
    {
        $teacher = $request->user();

        $channels = Conversation::where('teacher_id', $teacher->id)
            ->where('type', 'channel')
            ->with('discussion')
            ->get();

        return response()->json(['success' => true, 'data' => $channels]);
    }

    // FOR STUDENTS

    public function getStudentChannels(Request $request)
    {
        $student = $request->user();

        $teacherIds = $student->class->teachers()->pluck('teachers.id');

        $channels = Conversation::whereIn('teacher_id', $teacherIds)
            ->where('type', 'channel')
            ->with('discussion', 'teacher', 'subject')
            ->get();

        return response()->json(['success' => true, 'data' => $channels]);
    }

    // FOR BOTH

    public function getMessages($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $messages]);
    }



    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'body' => 'nullable|string',
            'attachment' => 'nullable|file|max:51200',
        ]);

        if (!$request->filled('body') && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'message' => 'لا يمكن إرسال رسالة فارغة'], 400);
        }

        $user = $request->user();
        $conversation = Conversation::findOrFail($conversationId);

        if (class_basename($user) === 'Student' && $conversation->type === 'channel') {
            return response()->json(['success' => false, 'message' => 'لا يمكنك الإرسال في هذه القناة'], 403);
        }

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $attachmentType = $this->determineAttachmentType($file->getMimeType());

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('chat_attachments/' . $attachmentType . 's', $filename, 'public');
        }

        $message = $conversation->messages()->create([
            'sender_type' => get_class($user),
            'sender_id' => $user->id,
            'body' => $request->body,
            'attachment_path' => $attachmentPath ? 'storage/' . $attachmentPath : null,
            'attachment_type' => $attachmentType,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'data' => $message]);
    }

    public function deleteMessage(Request $request, $messageId)
    {
        $user = $request->user();
        $message = Message::with('conversation')->findOrFail($messageId);

        $isStudent = class_basename($user) === 'Student';
        $isTeacher = class_basename($user) === 'Teacher';
        if ($isStudent) {
            if ($message->sender_id !== $user->id || $message->sender_type !== get_class($user)) {
                return response()->json(['success' => false, 'message' => 'لا تملك الصلاحية لحذف رسائل الآخرين.'], 403);
            }
        }
        if ($isTeacher) {
            $isMyMessage = ($message->sender_id === $user->id && $message->sender_type === get_class($user));
            $isMyChannel = ($message->conversation->teacher_id === $user->id);

            if (!$isMyMessage && !$isMyChannel) {
                return response()->json(['success' => false, 'message' => 'لا تملك الصلاحية لحذف هذه الرسالة.'], 403);
            }
        }
        $message->delete();
        broadcast(new MessageDeleted($message->id, $message->conversation_id))->toOthers();

        return response()->json(['success' => true, 'message' => 'تم حذف الرسالة بنجاح.']);
    }
}