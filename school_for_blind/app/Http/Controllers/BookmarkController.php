<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
 public function store(BookRequest $request)
{
    $validated = $request->validated();
    $bookmark = Bookmark::create([
        'student_id' => Auth::id(), 
        'record_id' => $validated['record_id'],
        'name' => $validated['name'],
        'timestamp_in_seconds' => $validated['timestamp_in_seconds'],
    ]);

    return response()->json([
        'message' => 'تم حفظ العلامة المرجعية بنجاح',
        'bookmark' => $bookmark
    ], 201);
}  







public function index($recordingId)
{
    $bookmarks = Bookmark::where('student_id',Auth::id())
        ->where('recording_id', $recordingId)
        ->orderBy('timestamp_in_seconds', 'asc') 
        ->get();

    return response()->json($bookmarks);
}
public function destroy($id)
{
    $bookmark = Bookmark::where('user_id', Auth::id())->findOrFail($id);
    $bookmark->delete();

    return response()->json(['message' => 'تم حذف العلامة المرجعية']);
}






}
