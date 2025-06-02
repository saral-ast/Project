<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Osiset\ShopifyApp\Util;

class CreateChargesTable extends Migration
{
    public function up()
    {
        Schema::create(Util::getShopifyConfig('table_names.charges', 'charges'), function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('charge_id');
            $table->boolean('test')->default(false);
            $table->string('status')->nullable();
            $table->string('name')->nullable();
            $table->string('terms')->nullable();
            $table->string('type');
            $table->decimal('price', 8, 2);
            $table->decimal('capped_amount', 8, 2)->nullable();
            $table->integer('trial_days')->nullable();
            $table->timestamp('billing_on')->nullable();
            $table->timestamp('activated_on')->nullable();
            $table->timestamp('trial_ends_on')->nullable();
            $table->timestamp('cancelled_on')->nullable();
            $table->timestamp('expires_on')->nullable();
            $table->integer('plan_id')->unsigned()->nullable()->index(); // Index instead of foreign key
            $table->string('description')->nullable();
            $table->bigInteger('reference_charge')->nullable();
            $table->timestamps();
            $table->softDeletes();

            if ($this->getLaravelVersion() < 5.8) {
                $table->integer(Util::getShopsTableForeignKey())->unsigned()->index();
            } else {
                $table->bigInteger(Util::getShopsTableForeignKey())->unsigned()->index();
            }

        });
    }

    public function down()
    {
        Schema::drop(Util::getShopifyConfig('table_names.charges', 'charges'));
    }

    private function getLaravelVersion()
    {
        $version = Application::VERSION;
        return (float) substr($version, 0, strrpos($version, '.'));
    }
}