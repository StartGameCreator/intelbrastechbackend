use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona os campos para o relacionamento polimórfico do Eloquent
            $table->nullableMorphs('profileable'); // Isso cria automagicamente: profileable_id e profileable_type
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropMorphs('profileable');
        });
    }
};