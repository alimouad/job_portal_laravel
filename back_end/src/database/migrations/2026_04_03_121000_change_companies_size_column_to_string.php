<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE companies ALTER COLUMN size TYPE VARCHAR(255) USING size::VARCHAR');
            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE companies MODIFY size VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE companies ALTER COLUMN size TYPE INTEGER USING CASE WHEN size ~ '^[0-9]+$' THEN size::INTEGER ELSE NULL END");
            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE companies MODIFY size INT NULL');
        }
    }
};
