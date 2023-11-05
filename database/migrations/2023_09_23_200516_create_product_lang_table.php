<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_lang', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 128);
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('link_rewrite', 128);
            $table->string('meta_title', 128)->nullable();
            $table->string('meta_description', 512)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('available_now', 255)->nullable();
            $table->string('available_later', 255)->nullable();
            $table->string('delivery_in_stock', 255)->nullable();
            $table->string('delivery_out_stock', 255)->nullable();
            $table->unique(['product_id', 'locale', 'link_rewrite']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_lang');
    }
};
