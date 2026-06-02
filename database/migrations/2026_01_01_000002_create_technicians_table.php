<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('cpf', 14)
                ->unique()
                ->nullable();

            $table->string('rg', 20)
                ->nullable();

            $table->string('cnpj', 18)
                ->unique()
                ->nullable();

            $table->string('company_name')
                ->nullable();

            $table->string('crea')
                ->nullable();

            $table->string('crt')
                ->nullable();

            $table->string('cft')
                ->nullable();

            $table->string('phone', 20);

            $table->string('whatsapp', 20);

            $table->string('avatar_url')
                ->nullable();

            $table->text('bio')
                ->nullable();

            $table->string('cep', 9);

            $table->string('state', 2);

            $table->string('city');

            $table->string('neighborhood');

            $table->decimal('latitude', 10, 8)
                ->nullable();

            $table->decimal('longitude', 11, 8)
                ->nullable();

            $table->decimal('rating_cache', 3, 2)
                ->default(5.00);

            $table->unsignedInteger('jobs_completed')
                ->default(0);

            $table->timestamps();

            $table->index([
                'state',
                'city'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};