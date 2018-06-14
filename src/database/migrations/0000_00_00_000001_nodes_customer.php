<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NodesCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Módulo General de Clientes
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable(); // Obligatorio
            $table->string('name')->nullable(); // Obligatorio
            $table->string('first_name')->nullable(); // Obligatorio
            $table->string('last_name')->nullable(); // Obligatorio
            $table->string('ci_number')->nullable(); // Obligatorio
            $table->enum('ci_expedition', ['LP','SC','CB','CH','TA','OR','PO','BE','PA','OTRO'])->default('OTRO'); // Obligatorio
            $table->string('email')->nullable(); // Obligatorio
            $table->string('phone')->nullable(); // Obligatorio
            $table->string('address')->nullable(); // Obligatorio
            $table->string('nit_number')->nullable(); // Obligatorio
            $table->string('nit_name')->nullable(); // Obligatorio
            $table->date('birth_date')->nullable(); // Obligatorio
            $table->string('password')->nullable(); // Obligatorio
            $table->boolean('active')->default(0); // Obligatorio
            if(config('customer.fields.member_code')){
                $table->string('member_code')->nullable();
            }
            if(config('customer.fields.age')){
                $table->integer('age')->nullable();
            }
            if(config('customer.fields.shirt')){
                $table->integer('shirt')->nullable();
            }
            if(config('customer.fields.shirt_size')){
                $table->string('shirt_size')->nullable();
            }
            if(config('customer.fields.emergency_short')){
                $table->string('emergency')->nullable();
            }
            if(config('customer.fields.emergency_long')){
                $table->string('emergency_name')->nullable();
                $table->string('emergency_number')->nullable();
            }
            $table->timestamps();
        });
        if(config('customer.dependants')){
            Schema::create('customer_dependants', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('customer_id')->nullable();
                $table->string('name')->nullable();
                $table->boolean('active')->nullable()->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Módulo General de Clientes
        Schema::dropIfExists('customer_dependants');
        Schema::dropIfExists('customers');

    }
}
