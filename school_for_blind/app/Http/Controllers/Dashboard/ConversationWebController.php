<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\MessageDeleted;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ConversationWebController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $query = Conversation::with('teacher')->latest();

        if ($admin->role === 'Academic Manager' || $admin->role === 'Moderator') {
            $query->where(function ($q) use ($admin) {
                $q->whereIn('type', ['channel', 'discussion'])
                    ->orWhere(function ($subQ) use ($admin) {
                        $subQ->where('type', 'teacher_admin')
                            ->where('admin_id', $admin->id);
                    });
            });
        }
        $conversations = $query->paginate(15);
        return view('pages.conversations.index', compact('conversations'));
    }

    public function show($id)
    {
        $admin = Auth::guard('admin')->user();
        $conversation = Conversation::with('teacher')->findOrFail($id);
        $canReply = false;
        if ($conversation->type === 'teacher_admin' && $conversation->admin_id === $admin->id) {
            $canReply = true;
        }

        \Log::info($conversation);

        return view('pages.conversations.show', compact('conversation', 'canReply'));
    }

    public function fetchMessages(Request $request, $id)
    {
        $messages = Message::with('sender')
            ->where('conversation_id', $id)
            ->latest() 
            ->cursorPaginate(30);

        $messages->through(function ($message) {
            if ($message->attachment_path) {
                $message->attachment_path = asset($message->attachment_path);
            }
            return $message;
        });

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();
        $conversation = Conversation::findOrFail($id);

        if ($conversation->type !== 'teacher_admin' || $conversation->admin_id !== $admin->id) {
            return response()->json(['success' => false, 'message' => 'لا تملك صلاحية الإرسال في هذه المحادثة.'], 403);
        }

        $request->validate([
            'body' => 'nullable|string',
            'attachment' => 'nullable|file|max:51200',
        ]);

        if (!$request->filled('body') && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'message' => 'لا يمكن إرسال رسالة فارغة'], 400);
        }

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('chat_attachments/' . $attachmentType . 's', $filename, 'public');
        }

        $message = $conversation->messages()->create([
            'sender_type' => get_class($admin),
            'sender_id' => $admin->id,
            'body' => $request->body,
            'attachment_path' => $attachmentPath ? 'storage/' . $attachmentPath : null,
            'attachment_type' => $attachmentType,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        $message->load('sender');
        if ($message->attachment_path)
            $message->attachment_path = asset($message->attachment_path);

        return response()->json(['success' => true, 'data' => $message]);
    }

    public function getUserProfile(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');

        if (class_basename($type) === 'Student') {
            $user = Student::with('class')->findOrFail($id);
            $html = view('pages.conversations.partials.student_profile_modal', compact('user'))->render();
        } elseif (class_basename($type) === 'Teacher') {
            $user = Teacher::with('subjects', 'classes')->findOrFail($id);
            $html = view('pages.conversations.partials.teacher_profile_modal', compact('user'))->render();
        } else {
            return response()->json(['success' => false, 'message' => 'نوع المستخدم غير معروف'], 400);
        }

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        $conversationId = $message->conversation_id;
        $message->delete();
        broadcast(new MessageDeleted($id, $conversationId))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الرسالة بنجاح وبث الإجراء لحظياً.'
        ]);
    }
}