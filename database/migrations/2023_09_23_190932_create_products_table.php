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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('brand_id')->nullable();
            $table->bigInteger('tax_id')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('reference')->nullable();
            $table->decimal('price',20, 6)->default(0,0);
            $table->string('unity')->nullable();
            $table->decimal('unit_price_ratio',20, 6)->default(0,0)->nullable();
            $table->decimal('width',5, 1)->default(0,0);
            $table->decimal('height',5, 1)->default(0,0);
            $table->decimal('depth',5, 1)->default(0,0);
            $table->decimal('weight',7, 3)->default(0,000);
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
