<?php

use Illuminate\Database\Schema\Blueprint;
use Phinx\Migration\AbstractMigration;

class CreatePromotionSaleTable extends BaseMigration
{
    public function up()
    {
        $this->schema->create('promotion_sale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('dateFrom');
            $table->date('dateTo');
            $table->enum('discountType', ['percent', 'flat']);

            $table->decimal('discountAmount');

            $table->tinyInteger('enabled');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->drop('promotion_sales');
    }
}
