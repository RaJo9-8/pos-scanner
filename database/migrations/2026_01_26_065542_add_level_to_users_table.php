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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('level', [1, 2, 3, 4, 5])->default(4)->after('email')->comment('1=superadmin, 2=admin, 3=leader, 4=kasir, 5=manager');
            $table->string('phone')->nullable()->after('level');
            $table->string('address')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['level', 'phone', 'address']);
        });
    }
};
