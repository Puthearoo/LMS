<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('checkout_date')->nullable();
            $table->date('due_date')->nullable();
            $table->datetime('return_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'checked_out', 'returned', 'rejected', 'overdue', 'cancelled'])->default('pending');

            $table->boolean('extension_requested')->default(false);
            $table->timestamp('extension_requested_at')->nullable();
            $table->integer('extension_days')->nullable();
            $table->date('extended_due_date')->nullable();
            $table->enum('extension_status', ['pending', 'approved', 'rejected'])->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};