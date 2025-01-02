<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummaryRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_records', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('year'); // Field for the year
            $table->string('semester'); // Field for the semester
            $table->string('mengetahui'); // Field for "mengetahui"
            $table->string('mengetahui_name'); // Field for the name of "mengetahui"
            $table->string('kaprodi_tpmo'); // Field for kaprodi_tpm or TPMO
            $table->string('kaprodi_topkr'); // Field for kaprodi_topkr
            $table->boolean('edom_lock'); // Field for edom_lock
            $table->boolean('layanan_lock'); // Field for layanan_lock
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summary_records');
    }
}
