<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add generated column for English name and unique index
        DB::statement("ALTER TABLE `categories` ADD COLUMN `category_name_en` VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(`category-name`, '$.en'))) STORED;");
        DB::statement("CREATE UNIQUE INDEX `category_name_en_unique` ON `categories` (`category_name_en`);");
    }

    public function down()
    {
        DB::statement("DROP INDEX `category_name_en_unique` ON `categories`;");
        DB::statement("ALTER TABLE `categories` DROP COLUMN `category_name_en`;");
    }
};
