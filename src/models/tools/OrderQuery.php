<?php
namespace dvizh\order\models\tools;

use yii;

class OrderQuery extends \yii\db\ActiveQuery
{
    public function my()
    {
        $userId = yii::$app->user->id;
        
        if(!$userId) {
            return false;
        }

        return $this->andWhere(['user_id' => $userId]);
    }

    public function unpayment()
    {
        return parent::andWhere(['payment' => 'no']);
    }

    public function byHour()
    {
        return parent::andWhere('timestamp>:time', [':time' => time() - 3600]);
    }
}
