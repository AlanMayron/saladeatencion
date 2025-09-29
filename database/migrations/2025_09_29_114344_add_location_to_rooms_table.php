<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_location_to_rooms_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('location', 120)->nullable()->index();
        });
    }
    public function down(): void {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
