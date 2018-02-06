<?php

use Illuminate\Database\Schema\Blueprint;
use Phinx\Migration\AbstractMigration;

class CreatePromotionSaleCustomerGroupsTable extends BaseMigration
{

    public function up()
    {
        $this->schema->create('promotion_sale_customer_group_relation', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('sale_id');
            $table->unsignedInteger('customer_group_id');

            $table->foreign('sale_id')
                ->references('id')->on('promotion_sale')
                ->onDelete('cascade');

            $table->unique(['sale_id', 'customer_group_id'], 'sale_customer_group_unique');

            $table->timestamps();
        });
    }
    
    public function down()
    {
        $this->schema->drop('promotion_sale_customer_group_relation');
    }

}
