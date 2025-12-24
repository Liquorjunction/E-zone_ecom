<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add generated column for English sub-category name and composite unique index with category_id
        DB::statement("ALTER TABLE `sub-categories` ADD COLUMN `sub_category_name_en` VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(`sub-category-name`, '$.en'))) STORED;");
        DB::statement("CREATE UNIQUE INDEX `subcategories_category_name_unique` ON `sub-categories` (`category_id`, `sub_category_name_en`);");
    }

    public function down()
    {
        DB::statement("DROP INDEX `subcategories_category_name_unique` ON `sub-categories`;");
        DB::statement("ALTER TABLE `sub-categories` DROP COLUMN `sub_category_name_en`;");
    }
};
