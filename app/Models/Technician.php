namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Technician extends Model
{
    protected $fillable = [
        'cpf', 'rg', 'cnpj', 'company_name', 'crea', 'crt', 'cft', 
        'phone', 'whatsapp', 'avatar_url', 'bio', 'cep', 'state', 'city', 
        'neighborhood', 'location', 'rating_cache', 'jobs_completed',
        'banner_url', 'resume_url', 'experience_years', 'is_remote' // <-- Novos campos da Auditoria
    ];

    /**
     * Conexão inversa polimórfica com a tabela de usuários
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'profileable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}