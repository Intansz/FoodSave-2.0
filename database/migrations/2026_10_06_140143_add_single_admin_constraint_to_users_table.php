
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('admin_slot')
                ->nullable()
                ->storedAs("CASE WHEN role = 'admin' THEN 1 ELSE NULL END")
                ->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['admin_slot']);
            $table->dropColumn('admin_slot');
        });
    }
};