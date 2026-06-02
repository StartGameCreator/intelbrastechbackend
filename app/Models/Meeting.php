namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'comment'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}