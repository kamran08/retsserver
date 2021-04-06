<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('LA1_Char100_1', 191);
            $table->string('LA1_LoginName', 191);
            $table->string('LA1_PhoneNumber1', 191);
            $table->string('LA1_WebPage', 191);
            $table->string('LA2_Char100_1', 191);
            $table->string('LA2_LoginName', 191);
            $table->string('LA2_PhoneNumber1', 191);
            $table->string('LA2_WebPage', 191);
            $table->string('LA3_Char100_1', 191);
            $table->string('LA3_LoginName', 191);
            $table->string('LA3_PhoneNumber1', 191);
            $table->string('L_ListOffice1', 191);
            $table->string('L_ListOffice2', 191);
            $table->string('L_ListOffice3', 191);
            $table->string('L_ListAgent1', 191);
            $table->string('L_ListAgent2', 191);
            $table->string('L_ListAgent3', 191);
            $table->string('LO1_ShortName', 191);
            $table->string('LO1_OrganizationName', 191);
            $table->string('LO1_PhoneNumber1', 191);
            $table->string('LO2_ShortName', 191);
            $table->string('LO2_OrganizationName', 191);
            $table->string('LO2_PhoneNumber1', 191);
            $table->string('LO3_ShortName', 191);
            $table->string('LO3_OrganizationName', 191);
            $table->string('LA1_Email', 191);
            $table->string('LA2_Email', 191);
            $table->string('LA3_Email', 191);
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
        Schema::dropIfExists('agent_details');
    }
}
