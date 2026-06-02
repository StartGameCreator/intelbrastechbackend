use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabela de Postagens
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Dono do post (Técnico)
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('media_url')->nullable(); // Foto ou vídeo do "Antes e Depois"
            $table->enum('media_type', ['image', 'video', 'none'])->default('none');
            $table->unsignedInteger('likes_count')->default(0); // Cache de performance para escala
            $table->timestamps();
        });

        // Tabela de Comentários
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();
        });

        // Tabela Pivô de Curtidas (Garante apenas 1 curtida por usuário por post)
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['post_id', 'user_id']); // Chave única composta
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
    }
};