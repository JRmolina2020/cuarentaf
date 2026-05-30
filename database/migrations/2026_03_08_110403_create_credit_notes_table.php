<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id(); // auto incremental

            $table->string('number', 50);
            $table->string('reference_code', 100)->nullable();
            $table->integer('status')->default(1);
            $table->text('qr')->nullable();
            $table->string('cude', 200)->nullable();
            $table->timestamp('validated')->nullable();
            $table->decimal('gross_value', 15, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('surcharge_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_notes');
    }
}
