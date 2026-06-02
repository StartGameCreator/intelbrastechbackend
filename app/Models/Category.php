namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function technicians(): BelongsToMany
    {
        return $this->belongsToMany(Technician::class);
    }
}