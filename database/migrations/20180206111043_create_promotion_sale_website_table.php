<?php

use Illuminate\Database\Schema\Blueprint;
use Phinx\Migration\AbstractMigration;

class CreatePromotionSaleWebsiteTable extends BaseMigration
{

    public function up()
    {
        $this->schema->create('promotion_sale_website_relation', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('sale_id');
            $table->unsignedInteger('website_id');

            $table->foreign('sale_id')
                ->references('id')->on('promotion_sale')
                ->onDelete('cascade');

            $table->unique(['sale_id', 'website_id'], 'sale_website_id_unique');

            $table->timestamps();
        });
    }
    
    public function down()
    {
        $this->schema->drop('promotion_sale_website_relation');
    }
}
