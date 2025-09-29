<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // adicion de columna state a la tabla batches
            $table->unsignedBigInteger('batch_state_id')->after('totalBatch');

            $table->foreign('batch_state_id')
            ->references('id')
            ->on('batch_states')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // Eliminacion de llave foranea
            $table->dropForeign('batch_state_id');
            // ELiminacion de columna state a la tabla batches
            $table->dropColumn('batch_state_id');
        });
    }
};

