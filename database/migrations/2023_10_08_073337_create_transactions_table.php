<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code')->nullable();
            $table->text('description');
            $table->enum('status', ['pending', 'done']);
            $table->enum('source', ['wa', 'instagram', 'facebook', 'shopee', 'tokped', 'etsy', 'email', 'amazon', 'ebay', 'website']);
            $table->timestamp('date');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
