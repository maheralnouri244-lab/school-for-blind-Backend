<?php
namespace App\Http\Controllers;
use App\Http\Requests\BookRequest;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function index($recordingId)
    {
        $bookmarks = Bookmark::where('student_id', Auth::id())
            ->where('record_id', $recordingId)
            ->orderBy('timestamp_in_seconds', 'asc') // ترتيب تصاعدي حسب زمن المقطع
            ->get();

        return response()->json([
            'success'   => true,
            'bookmarks' => $bookmarks
        ], 200);
    }

    
    public function store(BookRequest $request)
    {$name = $request->input('name'); 
        $validated = $request->validated();
        $bookmark = Bookmark::create([
            'record_id' => $request->recording_id, 
            'student_id'           => Auth::id(), 
            'recording_id'         => $validated['recording_id'],
            'name'                 => $name,
            'timestamp_in_seconds' => $validated['timestamp_in_seconds'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ العلامة المرجعية بنجاح',
            'bookmark' => $bookmark 
        ], 201);
    }

    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
        ]);

        $bookmark = Bookmark::where('student_id', Auth::id())->findOrFail($id);

        $bookmark->update([
            'name' => $validated['name']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث اسم العلامة بنجاح',
            'bookmark' => $bookmark
        ], 200);
    }

    
    public function destroy($id)
    {
        $bookmark = Bookmark::where('student_id', Auth::id())->findOrFail($id);
        $bookmark->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف العلامة المرجعية بنجاح'
        ], 200);
    }

    
    
}