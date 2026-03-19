<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('zonas')) {
            return;
        }

        if (!Schema::hasColumn('zonas', 'esquina_noroeste')) {
            return;
        }

        DB::statement('ALTER TABLE zonas ADD COLUMN IF NOT EXISTS area geometry(Polygon, 4326)');

        DB::statement(
            "
            UPDATE zonas
            SET area = ST_SetSRID(
                ST_MakePolygon(
                    ST_MakeLine(ARRAY[
                        esquina_noroeste,
                        esquina_noreste,
                        esquina_sureste,
                        esquina_suroeste,
                        esquina_noroeste
                    ])
                ),
                4326
            )
            WHERE esquina_noroeste IS NOT NULL
              AND esquina_noreste IS NOT NULL
              AND esquina_suroeste IS NOT NULL
              AND esquina_sureste IS NOT NULL
            "
        );

        DB::statement('ALTER TABLE zonas DROP COLUMN IF EXISTS esquina_noroeste');
        DB::statement('ALTER TABLE zonas DROP COLUMN IF EXISTS esquina_noreste');
        DB::statement('ALTER TABLE zonas DROP COLUMN IF EXISTS esquina_suroeste');
        DB::statement('ALTER TABLE zonas DROP COLUMN IF EXISTS esquina_sureste');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('zonas')) {
            return;
        }

        DB::statement('ALTER TABLE zonas ADD COLUMN IF NOT EXISTS esquina_noroeste geometry(Point, 4326)');
        DB::statement('ALTER TABLE zonas ADD COLUMN IF NOT EXISTS esquina_noreste geometry(Point, 4326)');
        DB::statement('ALTER TABLE zonas ADD COLUMN IF NOT EXISTS esquina_suroeste geometry(Point, 4326)');
        DB::statement('ALTER TABLE zonas ADD COLUMN IF NOT EXISTS esquina_sureste geometry(Point, 4326)');

        if (Schema::hasColumn('zonas', 'area')) {
            DB::statement(
                "
                UPDATE zonas
                SET esquina_noroeste = ST_PointN(ST_ExteriorRing(area), 1),
                    esquina_noreste  = ST_PointN(ST_ExteriorRing(area), 2),
                    esquina_sureste  = ST_PointN(ST_ExteriorRing(area), 3),
                    esquina_suroeste = ST_PointN(ST_ExteriorRing(area), 4)
                WHERE area IS NOT NULL
                "
            );

            DB::statement('ALTER TABLE zonas DROP COLUMN IF EXISTS area');
        }
    }
};
