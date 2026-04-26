<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->string('email');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->bigInteger('verification_campaign_id')->unsigned()->nullable();
            $table->foreign('verification_campaign_id')->references('id')->on('verification_campaigns')->onDelete('set null');
            $table->string('verification_status');
            $table->dateTime('last_verification_at')->nullable();
            $table->string('last_verification_by', 100)->nullable();
            $table->longText('last_verification_result')->nullable();
            $table->longText('verification_error')->nullable();
            $table->bigInteger('api_key_id')->unsigned()->nullable();
            $table->timestamps();

            $table->index(['verification_campaign_id', 'verification_status'], 'email_addrs_camp_status_idx');
            $table->index(['customer_id'], 'email_addrs_customer_idx');
            $table->index(['email'], 'email_addrs_email_idx');
        });

        // FK to api_keys is optional — only added if the host app ships an api_keys table.
        // Develop core does not, but the athena/evs (client) plugin or another plugin may.
        if (Schema::hasTable('api_keys')) {
            Schema::table('email_addresses', function (Blueprint $table) {
                $table->foreign('api_key_id')->references('id')->on('api_keys')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_addresses');
    }
};
