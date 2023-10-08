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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_id');
            $table->enum('payment_channel', ['bca', 'mandiri', 'bni', 'bri', 'jenius'])->nullable();
            $table->enum('payment_type', ['dp', 'pelunasan', 'full_payment', 'lainnya'])->nullable();
            $table->text('proof_of_payment')->nullable();
            $table->bigInteger('amount');
            $table->bigInteger('price')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('date');
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
        Schema::dropIfExists('sales');
    }
};
