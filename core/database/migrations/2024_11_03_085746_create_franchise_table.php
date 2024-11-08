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
        Schema::create('franchises', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->text('store');
            $table->bigInteger('state_id');
            $table->bigInteger('district_id');
            $table->integer('commission');
            $table->text('name');
            $table->text('mobile');
            $table->char('pan_number', length: 30);
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise');
    }
};
