<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMeesageTypeIdToChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->integer('message_type_id')->unsigned()->nullable()->after('message_date');
            $table->foreign('message_type_id')->references('id')->on('message_types');
            $table->integer('attachment_id')->unsigned()->nullable()->after('message_type_id');
            $table->foreign('attachment_id')->references('id')->on('attachments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign('message_type_id');
            $table->dropIndex('message_type_id');
            $table->dropColumn('message_type_id');

            $table->dropForeign('attachment_id');
            $table->dropIndex('attachment_id');
            $table->dropColumn('attachment_id');
        });
    }
}
