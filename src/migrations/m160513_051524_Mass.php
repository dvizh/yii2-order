<?php

use yii\db\Schema;
use yii\db\Migration;

class m160513_051524_Mass extends Migration
{
    public function safeUp()
    {
        $connection = Yii::$app->db;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        else {
            $tableOptions = null;
        }
        
        try {
            $this->createTable('{{%order}}', [
                'id' => Schema::TYPE_PK . "",
                'client_name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'phone' => Schema::TYPE_STRING . "(20)",
                'email' => Schema::TYPE_STRING . "(100) NOT NULL",
                'promocode' => Schema::TYPE_STRING . "(100)",
                'count' => Schema::TYPE_INTEGER . "(11)",
                'cost' => Schema::TYPE_DECIMAL . "(11,2)",
				'base_cost' => Schema::TYPE_DECIMAL . "(11,2)",
                'payment_type_id' => Schema::TYPE_INTEGER . "(11)",
                'shipping_type_id' => Schema::TYPE_INTEGER . "(11)",
                'delivery_time_date' => Schema::TYPE_DATE,
                'delivery_time_hour' => Schema::TYPE_SMALLINT,
                'delivery_time_min' => Schema::TYPE_SMALLINT,
                'delivery_type' => "ENUM('fast', 'totime') NULL DEFAULT NULL",
                'status' => Schema::TYPE_STRING . "(155)",
                'order_info' => Schema::TYPE_TEXT . " COMMENT 'PHP serialize'",
                'time' => Schema::TYPE_STRING . "(50) NOT NULL",
                'user_id' => Schema::TYPE_INTEGER . "(11)",
                'seller_user_id' => Schema::TYPE_INTEGER . "(11)",
                'date' => Schema::TYPE_DATETIME . "",
                'payment' => "enum('yes','no')" . " NOT NULL DEFAULT 'no'",
                'timestamp' => Schema::TYPE_INTEGER . "(11)",
                'comment' => Schema::TYPE_TEXT . "",
                'address' => Schema::TYPE_STRING . "(255)",
                ], $tableOptions);

            $this->createTable('{{%order_element}}', [
                'id' => Schema::TYPE_PK . "",
                'model' => Schema::TYPE_STRING . "(255) NOT NULL",
                'order_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'item_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'count' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'price' => Schema::TYPE_DECIMAL . "(11,2)",
				'base_price' => Schema::TYPE_DECIMAL . "(11,2)",
                'description' => Schema::TYPE_TEXT . "",
                'options' => Schema::TYPE_TEXT . "",
                ], $tableOptions);

            $this->createTable('{{%order_field}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'type_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'description' => Schema::TYPE_TEXT . "",
                'required' => "enum('yes','no')" . " NOT NULL DEFAULT 'no'",
                'order' => Schema::TYPE_INTEGER . "(11) DEFAULT '0'",
                ], $tableOptions);

            $this->createTable('{{%order_field_type}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'widget' => Schema::TYPE_STRING . "(255)",
                'have_variants' => "enum('yes','no')" . " DEFAULT 'no'",
                ], $tableOptions);

            $this->createTable('{{%order_field_value}}', [
                'id' => Schema::TYPE_PK . "",
                'order_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'field_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'value' => Schema::TYPE_TEXT . "",
                ], $tableOptions);

            $this->createTable('{{%order_field_value_variant}}', [
                'id' => Schema::TYPE_PK . "",
                'field_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'value' => Schema::TYPE_STRING . "(255)",
                ], $tableOptions);

            $this->createTable('{{%order_payment_type}}', [
                'id' => Schema::TYPE_PK . "",
                'slug' => Schema::TYPE_STRING . "(255) NOT NULL",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'widget' => Schema::TYPE_STRING . "(255)",
                'order' => Schema::TYPE_INTEGER . "(11) DEFAULT '0'",
                ], $tableOptions);

            $this->createTable('{{%order_shipping_type}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'description' => Schema::TYPE_TEXT . " NOT NULL",
                'cost' => Schema::TYPE_DECIMAL . "(11,2)",
                'free_cost_from' => Schema::TYPE_DECIMAL . "(11,2)",
                'order' => Schema::TYPE_INTEGER . "(11) DEFAULT '0'",
                ], $tableOptions);

            $this->createTable('{{%order_payment}}', [
                'id' => Schema::TYPE_PK . "",
                'order_id' => Schema::TYPE_INTEGER . "(11)",
                'payment_type_id' => Schema::TYPE_INTEGER . "(11)",
                'user_id' => Schema::TYPE_INTEGER . "(11)",
                'description' => Schema::TYPE_STRING . "(255)",
                'ip' => Schema::TYPE_STRING . "(55)",
                'amount' => Schema::TYPE_DECIMAL . "(11,2)",
                'date' => Schema::TYPE_DATETIME . "",
                ], $tableOptions);
            
            $this->addForeignKey(
                'fk_order_payment', '{{%order}}', 'payment_type_id', '{{%order_payment_type}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->addForeignKey(
                'fk_order_shipping', '{{%order}}', 'shipping_type_id', '{{%order_shipping_type}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->addForeignKey(
                'fk_element_order', '{{%order_element}}', 'order_id', '{{%order}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->addForeignKey(
                'fk_field_type', '{{%order_field}}', 'type_id', '{{%order_field_type}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->addForeignKey(
                'fk_field_order', '{{%order_field_value}}', 'order_id', '{{%order}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->addForeignKey(
                'fk_value_field', '{{%order_field_value}}', 'field_id', '{{%order_field}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->addForeignKey(
                'fk_variant_field', '{{%order_field_value_variant}}', 'field_id', '{{%order_field}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->addForeignKey(
                'fk_payment_order', '{{%order_payment}}', 'order_id', '{{%order}}', 'id', 'CASCADE', 'CASCADE'
            );
            $this->addForeignKey(
                'fk_payment_payment_type', '{{%order_payment}}', 'payment_type_id', '{{%order_payment_type}}', 'id', 'CASCADE', 'CASCADE'
            );
            
            $this->insert('{{%order_field_type}}', [
                'id' => '1',
                'name' => 'Input',
                'widget' => 'dvizh\order\widgets\field_type\Input',
                'have_variants' => 'no',
            ]);
            $this->insert('{{%order_field_type}}', [
                'id' => '2',
                'name' => 'Textarea',
                'widget' => 'dvizh\order\widgets\field_type\Textarea',
                'have_variants' => 'no',
            ]);
            $this->insert('{{%order_field_type}}', [
                'id' => '3',
                'name' => 'Select',
                'widget' => 'dvizh\order\widgets\field_type\Select',
                'have_variants' => 'yes',
            ]);
            $this->insert('{{%order_field_type}}', [
                'id' => '4',
                'name' => 'Checkbox',
                'widget' => 'dvizh\order\widgets\field_type\Checkbox',
                'have_variants' => 'yes',
            ]);

            $this->insert('{{%order_shipping_type}}', [
                'id' => '1',
                'name' => 'Самовывоз',
                'description' => '',
                'cost' => '0.00',
                'order' => NULL,
            ]);
            $this->insert('{{%order_shipping_type}}', [
                'id' => '2',
                'name' => 'Доставка по России',
                'description' => '',
                'cost' => '0.00',
                'order' => NULL,
            ]);
            $this->insert('{{%order_shipping_type}}', [
                'id' => '3',
                'name' => 'Доставка курьером по городу',
                'description' => '',
                'cost' => '0.00',
                'order' => NULL,
            ]);


            $this->insert('{{%order_payment_type}}', [
                'id' => '1',
                'name' => 'Наличный расчет',
                'slug' => '',
                'widget' => '',
                'order' => NULL,
            ]);
            $this->insert('{{%order_payment_type}}', [
                'id' => '2',
                'name' => 'Безналичный расчет',
                'slug' => '',
                'widget' => '',
                'order' => NULL,
            ]);
            $this->insert('{{%order_payment_type}}', [
                'id' => '3',
                'name' => 'Онлайн',
                'slug' => '',
                'widget' => '',
                'order' => NULL,
            ]);
   
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage();
        }
    }

    public function safeDown()
    {
        $connection = Yii::$app->db;
        try {
            $this->dropTable('{{%order}}');
            $this->dropTable('{{%order_element}}');
            $this->dropTable('{{%order_field}}');
            $this->dropTable('{{%order_field_type}}');
            $this->dropTable('{{%order_field_value}}');
            $this->dropTable('{{%order_field_value_variant}}');
            $this->dropTable('{{%order_payment_type}}');
            $this->dropTable('{{%order_shipping_type}}');
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage();
        }
    }

}
