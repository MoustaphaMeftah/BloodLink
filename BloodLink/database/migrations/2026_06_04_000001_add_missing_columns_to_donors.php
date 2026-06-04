<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            if (!Schema::hasColumn('donors', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('donors', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            
            if (!Schema::hasColumn('donors', 'contact_verified')) {
                $table->boolean('contact_verified')->default(false)->after('availability');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            if (Schema::hasColumn('donors', 'latitude')) {
                $table->dropColumn('latitude');
            }
            
            if (Schema::hasColumn('donors', 'longitude')) {
                $table->dropColumn('longitude');
            }
            
            if (Schema::hasColumn('donors', 'contact_verified')) {
                $table->dropColumn('contact_verified');
            }
        });
    }
};
