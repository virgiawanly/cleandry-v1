<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddItemsTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'CREATE TRIGGER `LOG_INSERT_ITEM`
                AFTER INSERT ON `items` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("INSERT ITEM", NOW());
                END'
        );

        DB::unprepared(
            'CREATE TRIGGER `LOG_UPDATE_ITEM`
                AFTER UPDATE ON `items` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("UPDATE ITEM", NOW());
                END'
        );

        DB::unprepared(
            'CREATE TRIGGER `LOG_DELETE_ITEM`
                BEFORE DELETE ON `items` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("DELETE ITEM", NOW());
                END'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG_INSERT_ITEM`');
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG_UPDATE_ITEM`');
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG_DELETE_ITEM`');
    }
}
