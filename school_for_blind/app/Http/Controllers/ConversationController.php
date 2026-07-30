<?php

namespace App\Http\Controllers;

use App\Events\MessageDeleted;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        if (!$student->class) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الطالب غير مسجل في أي شعبة أو صف حالياً.'
            ], 400);
        }

        $teacherIds = $student->class->teachers()->pluck('teachers.id');

        $channels = Conversation::whereIn('teacher_id', $teacherIds)
            ->where('type', 'channel')
            ->with('discussion', 'teacher', 'subject')
            ->get();

        return response()->json(['success' => true, 'data' => $channels]);
    }

    // FOR BOTH

    public function getMessages(Request $request, $conversationId)
    {
        $user = $request->user();

        $conversation = Conversation::with('parent')->findOrFail($conversationId);

        if (class_basename($user) === 'Student') {
            if ($conversation->type === 'teacher_admin') {
                return response()->json(['success' => false, 'message' => 'غير مصرح لك بالوصول.'], 403);
            }

            $teacherId = $conversation->teacher_id ?? optional($conversation->parent)->teacher_id;

            $hasAccess = $user->class->teachers()->where('teachers.id', $teacherId)->exists();
            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'غير مصرح لك باستعراض رسائل هذه المحادثة.'], 403);
            }
        }

        if (class_basename($user) === 'Teacher') {
            if ((int) $conversation->teacher_id !== (int) $user->id) {
                return response()->json(['success' => false, 'message' => 'غير مصرح لك بالوصول لهذه المحادثة.'], 403);
            }
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $messages->transform(function ($message) {
            if ($message->attachment_path) {
                $message->attachment_path = asset($message->attachment_path);
            }
            return $message;
        });

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

        $conversation = Conversation::with('parent')->findOrFail($conversationId);

        if (class_basename($user) === 'Student') {

            if ($conversation->type === 'channel') {
                return response()->json(['success' => false, 'message' => 'لا يمكنك الإرسال في هذه القناة'], 403);
            }

            if ($conversation->type === 'discussion') {
                $teacherId = $conversation->teacher_id ?? optional($conversation->parent)->teacher_id;

                if (!$teacherId) {
                    return response()->json(['success' => false, 'message' => 'بيانات المحادثة غير مكتملة.'], 400);
                }

                $isEnrolled = $user->class->teachers()->where('teachers.id', $teacherId)->exists();

                if (!$isEnrolled) {
                    return response()->json(['success' => false, 'message' => 'عذراً، لا يمكنك الإرسال في مجموعة نقاش لا تنتمي لأساتذة شعبتك.'], 403);
                }
            }
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

    public function getTeacherAdminConversations(Request $request)
    {
        $teacher = $request->user();

        $conversations = Conversation::where('teacher_id', $teacher->id)
            ->where('type', 'teacher_admin')
            ->with('admin:id,role,email')
            ->get();

        return response()->json(['success' => true, 'data' => $conversations]);
    }


    public function reportMessage(Request $request, $messageId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $reporter = $request->user();

        $message = Message::findOrFail($messageId);

        if ($message->sender_id === $reporter->id && $message->sender_type === get_class($reporter)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك الإبلاغ عن رسالتك الخاصة.'
            ], 400);
        }

        $report = Report::create([
            'reporter_type' => get_class($reporter),
            'reporter_id' => $reporter->id,

            'reported_type' => $message->sender_type,
            'reported_id' => $message->sender_id,

            'reason' => $request->reason,
            'status' => 'pending',

            'reportable_type' => get_class($message),
            'reportable_id' => $message->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال البلاغ بنجاح، ستقوم الإدارة بمراجعته.'
        ]);
    }
}