<?php
namespace dvizh\order\widgets;

use yii\helpers\Html;
use dvizh\order\models\Order;
use dvizh\order\models\tools\OrderSearch;
use yii;

class ClientOrders extends \yii\base\Widget
{
    public $client = null;
    
    public function init()
    {
        return parent::init();
    }

    public function run()
    {
        $searchModel = new OrderSearch();
        
        $params = Yii::$app->request->queryParams;
        
        if($this->client->id && empty($params['OrderSearch'])) {
            $params['OrderSearch']['user_id'] = $this->client->id;
        }
        
        $dataProvider = $searchModel->search($params);

        return $this->render('client_orders', [
            'client' => $this->client,
            'clientId' => (int)$this->client->id,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
