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
        Schema::create('user_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->foreignId('type_id')->nullable()->constrained('leave_types')->onDelete('set null');
            $table->string('other_type')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('number_of_days_off');
            $table->text('reason')->nullable();
            $table->string('mark')->nullable();
            $table->enum('leave_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('checked_by')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_leaves');
    }
};
