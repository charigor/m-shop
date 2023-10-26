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
        Schema::create('attribute_products', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->default(0);
            $table->decimal('price',20, 6)->default(0,0);
            $table->decimal('width',5, 1)->default(0,0);
            $table->decimal('height',5, 1)->default(0,0);
            $table->decimal('depth',5, 1)->default(0,0);
            $table->decimal('weight',7, 3)->default(0,000);
            $table->bigInteger('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_products');
    }
};
