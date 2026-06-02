namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FeedController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::with(['user.technician', 'comments.user'])->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:20480',
        ]);

        $mediaUrl = null;
        $mediaType = 'none';

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('feed_media', 'public');
            $mediaUrl = asset('storage/' . $path);
            $mediaType = in_array($request->file('media')->getClientOriginalExtension(), ['mp4']) ? 'video' : 'image';
        }

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $request->input('title'),
            'content' => $request->content,
            'media_url' => $mediaUrl,
            'media_type' => $mediaType
        ]);

        return response()->json($post, 201);
    }

    public function toggleLike(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;
        $post = Post::findOrFail($id);
        $existingLike = Like::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            Like::create(['post_id' => $post->id, 'user_id' => $userId]);
            $post->increment('likes_count');
            $liked = true;
        }

        return response()->json(['liked' => $liked, 'likes_count' => $post->likes_count]);
    }
}