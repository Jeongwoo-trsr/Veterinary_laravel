<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('cancellation_status', ['none', 'pending', 'approved', 'declined'])->default('none')->after('status');
            $table->timestamp('cancellation_requested_at')->nullable()->after('cancellation_status');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['cancellation_status', 'cancellation_requested_at']);
        });
    }
};