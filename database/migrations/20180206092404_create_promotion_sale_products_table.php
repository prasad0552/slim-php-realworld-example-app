<?php

use Illuminate\Database\Schema\Blueprint;
use Phinx\Migration\AbstractMigration;

class CreatePromotionSaleProductsTable extends BaseMigration
{

    public function up()
    {
        $this->schema->create('promotion_sale_product_relation', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('sale_id');
            $table->unsignedInteger('product_id');

            $table->foreign('sale_id')
                ->references('id')->on('promotion_sale')
                ->onDelete('cascade');

            $table->unique(['sale_id', 'product_id'], 'sale_product_unique');

            $table->timestamps();
        });
    }

    public function down()
    {
        //
    }

}
