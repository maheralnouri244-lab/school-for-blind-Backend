<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\MessageDeleted;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConversationWebController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with('teacher')->latest()->paginate(15);
        return view('pages.conversations.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Conversation::with(['messages.sender', 'teacher'])->findOrFail($id);
        return view('pages.conversations.show', compact('conversation'));
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