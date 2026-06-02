namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles; // <-- A Nova Trait de Controle

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // <-- Incluída aqui

    protected $fillable = [
        'name', 'email', 'password', 'role', 
        'google_id', 'microsoft_id', 'is_active', 'fcm_token',
        'profileable_id', 'profileable_type' // <-- Novos campos permitidos
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento Polimórfico Central
     * Pode retornar uma instância de Technician, Client, Integrator, Company, etc.
     */
    public function profile(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }
}