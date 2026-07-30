<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\MessageDeleted;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Punishment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ConversationWebController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = Conversation::with(['teacher', 'subject'])->latest();

        if ($admin->role === 'Academic Manager' || $admin->role === 'Moderator') {
            $query->where(function ($q) use ($admin) {
                $q->whereIn('type', ['channel', 'discussion'])
                    ->orWhere(function ($subQ) use ($admin) {
                        $subQ->where('type', 'teacher_admin')
                            ->where('admin_id', $admin->id);
                    });
            });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $conversations = $query->paginate(15)->withQueryString();
        $teachers = Teacher::all();
        $subjects = Subject::all();

        return view('pages.conversations.index', compact('conversations', 'teachers', 'subjects'));
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

        // if ($conversation->type !== 'teacher_admin' || $conversation->admin_id !== $admin->id) {
        //     return response()->json(['success' => false, 'message' => 'لا تملك صلاحية الإرسال في هذه المحادثة.'], 403);
        // }

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
            $attachmentType = 'voice';
            $extension = $file->getClientOriginalExtension() ?: 'webm';
            $filename = Str::uuid() . '.' . $extension;
            $attachmentPath = $file->storeAs('chat_attachments/voices', $filename, 'public');
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

        $viewName = '';

        if ($type === 'Student') {
            $user = Student::find($id);
            $viewName = 'pages.conversations.partials.student_profile_modal';
        } elseif ($type === 'Teacher') {
            $user = Teacher::find($id);
            $viewName = 'pages.conversations.partials.teacher_profile_modal';
        } else {
            return response()->json(['success' => false, 'message' => 'نوع المستخدم غير معروف']);
        }

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'المستخدم غير موجود']);
        }

        $punishments = Punishment::orderBy('level', 'asc')->get();

        $html = view($viewName, compact('user', 'type', 'punishments'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
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