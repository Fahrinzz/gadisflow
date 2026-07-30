<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['quotation', 'invoice', 'delivery_order']);
            $table->string('number')->unique();
            $table->date('doc_date');
            $table->string('status')->nullable();

            // Recipient (Attn) — snapshot on the document
            $table->foreignId('customer_id')->nullable()->constrained()->nullOndelete();
            $table->string('attn_name')->nullable();
            $table->text('attn_address')->nullable();

            $table->text('project_details')->nullable();
            $table->text('terms')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('payment', 12, 2)->default(0);

            // Link to the source document when converted (quotation -> invoice -> DO)
            $table->foreignId('parent_id')->nullable()->constrained('documents')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
