<?php

use App\Enums\ReportStatus;
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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('report_code', 8)->unique();
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->foreignId('type_id')->constrained('report_types');
            $table->foreignId('category_id')->constrained('report_categories');
            $table->string('district_id', 2);
            $table->foreign('district_id')->references('code')->on('districts');
            $table->string('village_id', 5);
            $table->foreign('village_id')->references('code')->on('villages');
            $table->string('title');
            $table->text('description');
            $table->text('address_detail');
            $table->geography('coordinates', 'point');
            $table->string('phone_number', 15);
            $table->string('current_status')->default(ReportStatus::PENDING_VERIFICATION->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
