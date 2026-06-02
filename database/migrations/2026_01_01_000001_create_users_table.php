// database/migrations/2026_01_01_000001_create_intelbrastech_backbone_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->enum('role', ['master', 'regional', 'technician', 'client'])->default('client');
            $table->string('google_id')->nullable()->index();
            $table->string('microsoft_id')->nullable()->index();
            $table->boolean('is_active')->default(false)->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('cnpj', 18)->unique()->nullable();
            $table->string('company_name')->nullable();
            $table->string('crea')->nullable();
            $table->string('crt')->nullable();
            $table->string('cft')->nullable();
            $table->string('phone', 20);
            $table->string('whatsapp', 20);
            $table->string('avatar_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('cep', 9);
            $table->string('state', 2);
            $table->string('city');
            $table->string('neighborhood');
            $table->point('location', 4326)->nullable(); // Geometria Espacial GPS
            $table->decimal('rating_cache', 3, 2)->default(5.00);
            $table->unsignedInteger('jobs_completed')->default(0);
            $table->timestamps();

            $table->spatialIndex('location');
            $table->index(['state', 'city']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('category_technician', function (Blueprint $table) {
            $table->foreignId('technician_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->primary(['technician_id', 'category_id']);
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['open', 'assigned', 'accepted', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->boolean('contact_released')->default(false);
            $table->string('cep', 9)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('city')->nullable();
            $table->point('location', 4326)->nullable();
            $table->timestamps();

            $table->spatialIndex('location');
            $table->index(['status', 'client_id', 'state', 'city']);
        });

        Schema::create('reports_rat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->constrained('users');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->text('services_performed');
            $table->text('materials_used')->nullable();
            $table->longText('client_signature'); 
            $table->longText('technician_signature');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports_rat');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('category_technician');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('technicians');
        Schema::dropIfExists('users');
    }
};