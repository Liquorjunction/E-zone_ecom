<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add generated column for English name and unique index if they don't exist
        if (!Schema::hasColumn('categories', 'category_name_en')) {
            DB::statement("ALTER TABLE `categories` ADD COLUMN `category_name_en` VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(`category-name`, '$.en'))) STORED;");
        }

        $indexExists = DB::select("SHOW INDEX FROM `categories` WHERE Key_name = ?", ['category_name_en_unique']);
        if (empty($indexExists)) {
            DB::statement("CREATE UNIQUE INDEX `category_name_en_unique` ON `categories` (`category_name_en`);");
        }
    }

    public function down()
    {
        $indexExists = DB::select("SHOW INDEX FROM `categories` WHERE Key_name = ?", ['category_name_en_unique']);
        if (!empty($indexExists)) {
            DB::statement("DROP INDEX `category_name_en_unique` ON `categories`;");
        }

        if (Schema::hasColumn('categories', 'category_name_en')) {
            DB::statement("ALTER TABLE `categories` DROP COLUMN `category_name_en`;");
        }
    }
};
