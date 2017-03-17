<?php

use yii\db\Migration;

class m170311_234119_create_element_name_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order_element}}', 'name', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('{{%order_element}}', 'name');
        
        return true;
    }
}
