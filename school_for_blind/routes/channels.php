<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = Conversation::find($id);

    if (!$conversation) {
        return false;
    }

    if (class_basename($user) === 'Teacher') {
        return (int) $conversation->teacher_id === (int) $user->id;
    }

    if (class_basename($user) === 'Student') {
        return $user->class->teachers()->where('teachers.id', $conversation->teacher_id)->exists();
    }

    if (class_basename($user) === 'Admin') {
        return true;
    }

    return false;
}, ['guards' => ['sanctum', 'web', 'admin']]);