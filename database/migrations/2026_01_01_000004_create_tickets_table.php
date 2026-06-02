Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('title');
    $table->text('description');
    $table->enum('status', ['open', 'assigned', 'accepted', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('open');
    $table->boolean('contact_released')->default(false);
    $table->timestamps();
    
    $table->index(['status', 'client_id']);
});