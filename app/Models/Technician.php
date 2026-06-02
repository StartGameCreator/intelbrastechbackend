namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Technician extends Model
{
    protected $fillable = ['user_id', 'cpf', 'rg', 'cnpj', 'company_name', 'crea', 'crt', 'cft', 'phone', 'whatsapp', 'avatar_url', 'bio', 'cep', 'state', 'city', 'neighborhood', 'location', 'rating_cache', 'jobs_completed'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}