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
        Schema::create('delete_account_requests', function (Blueprint $table) {
            $table->id();
            $table->string('user_type')->nullable();
            $table->string('entity_name')->nullable();
            $table->string('email')->nullable();
            $table->string('is_email_verify')->default(1);
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->tinyInteger('is_phone_verify')->default(1);
            $table->longText('comments')->nullable();
            $table->integer('status')->default(0);
            $table->string('approve_date')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Auto-updates on change
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delete_account_requests');
    }
};
