<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddItemUsesTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'CREATE TRIGGER `LOG_INSERT_ITEM_USES`
                AFTER INSERT ON `item_uses` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("INSERT ITEM USES", NOW());
                END'
        );

        DB::unprepared(
            'CREATE TRIGGER `LOG_UPDATE_ITEM_USES`
                AFTER UPDATE ON `item_uses` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("UPDATE ITEM USES", NOW());
                END'
        );

        DB::unprepared(
            'CREATE TRIGGER `LOG_DELETE_ITEM_USES`
                BEFORE DELETE ON `item_uses` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("DELETE ITEM USES", NOW());
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
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG_INSERT_ITEM_USES`');
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG_UPDATE_ITEM_USES`');
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG_DELETE_ITEM_USES`');
    }
}
