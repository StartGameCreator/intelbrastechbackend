namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'cnpj', 'corporate_name', 'trade_name', 'ie', 
        'phone', 'whatsapp', 'website', 'logo_url', 'bio', 
        'cep', 'state', 'city', 'neighborhood', 'address_number'
    ];

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'profileable');
    }
}