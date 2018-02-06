<?php

use Illuminate\Database\Schema\Blueprint;
use Phinx\Migration\AbstractMigration;

class CreatePromotionSaleCategoryTable extends BaseMigration
{

    public function up()
    {
        $this->schema->create('promotion_sale_category_relation', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('sale_id');
            $table->unsignedInteger('category_id');

            $table->foreign('sale_id')
                ->references('id')->on('promotion_sale')
                ->onDelete('cascade');

            $table->unique(['sale_id', 'category_id'], 'sale_category_unique');

            $table->timestamps();
        });
    }
    
    public function down()
    {
        //
    }

}
