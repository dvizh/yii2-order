<?php

use yii\db\Migration;

class m161110_050319_create_organization_fields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%order}}', 'organization_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'organization_id');
        
        return true;
    }
}
