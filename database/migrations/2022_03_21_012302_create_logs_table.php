<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->timestamp('datetime');
        });

        DB::unprepared(
            'CREATE TRIGGER `LOG INSERT PICKUP`
                AFTER INSERT ON `pickups` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("INSERT PICKUP", NOW());
                END'
        );

        DB::unprepared(
            'CREATE TRIGGER `LOG UPDATE PICKUP`
                AFTER UPDATE ON `pickups` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("UPDATE PICKUP", NOW());
                END'
        );

        DB::unprepared(
            'CREATE TRIGGER `LOG DELETE PICKUP`
                BEFORE DELETE ON `pickups` FOR EACH ROW
                BEGIN
                    INSERT INTO `logs` (`action`, `datetime`) VALUES("DELETE PICKUP", NOW());
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
        Schema::dropIfExists('logs');
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG INSERT PICKUP`');
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG UPDATE PICKUP`');
        DB::unprepared('DROP TRIGGER IF EXISTS `LOG DELETE PICKUP`');
    }
}
