<?php

use yii\db\Migration;
use yii\db\Schema;

class m170311_230319_create_is_deleted_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order}}', 'is_deleted', Schema::TYPE_BOOLEAN." DEFAULT 0");
        $this->addColumn('{{%order_element}}', 'is_deleted', Schema::TYPE_BOOLEAN." DEFAULT 0");
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'is_deleted');
        $this->dropColumn('{{%order_element}}', 'is_deleted');
        
        return true;
    }
}
