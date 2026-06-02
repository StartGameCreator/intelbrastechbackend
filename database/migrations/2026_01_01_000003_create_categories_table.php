// database/migrations/2026_01_01_000003_create_categories_table.php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->timestamps();
});

// Tabela Pivô: Técnico <-> Categoria
Schema::create('category_technician', function (Blueprint $table) {
    $table->foreignId('technician_id')->constrained()->onDelete('cascade');
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->primary(['technician_id', 'category_id']);
});