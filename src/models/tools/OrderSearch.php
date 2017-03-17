<?php
namespace dvizh\order\models\tools;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use dvizh\order\models\Order;
use dvizh\order\models\FieldValue;

class OrderSearch extends Order
{
    public function rules()
    {
        return [
            [['id', 'user_id', 'is_deleted', 'shipping_type_id', 'payment_type_id', 'seller_user_id'], 'integer'],
            [['payment', 'client_name', 'phone', 'email', 'status', 'time', 'date', 'promocode'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Order::find();

		$query->joinWith('elementsRelation')->groupBy('order.id');
		
		if($elementTypes = yii::$app->request->get('element_types')) {
			$query->andFilterWhere(['order_element.model' => $elementTypes])->groupBy('order.id');
		}

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'payment' => $this->payment,
            'status' => $this->status,
            'promocode' => $this->promocode,
            'seller_user_id' => $this->seller_user_id,
        ]);
        
        if(isset($this->is_deleted)) {
            $query->andWhere(['order.is_deleted' => $this->is_deleted]);
        }

        $query->andFilterWhere(['like', 'client_name', $this->client_name])
                ->andFilterWhere(['like', 'shipping_type_id', $this->shipping_type_id])
                ->andFilterWhere(['like', 'payment_type_id', $this->payment_type_id])
                ->andFilterWhere(['like', 'phone', $this->phone])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'date', $this->date])
                ->andFilterWhere(['like', 'time', $this->time]);

        if(yii::$app->request->get('promocode')) {
            $query->andWhere("promocode != ''");
            $query->andWhere("promocode IS NOT NULL");
        }

        //Убрать это все в контроллер
        if($customField = yii::$app->request->get('order-custom-field')) {
            $orderIds = [];
            foreach($customField as $id => $str) {
				if(!empty($str)) {
					if($values = FieldValue::find()->select('order_id')->where(['field_id' => $id])->andWhere(['LIKE', 'value', $str])->all()) {
						foreach($values as $value) {
							$orderIds[] = $value->order_id;
						}
					}
				}
            }
			
			if($orderIds) {
				$query->andWhere(['order.id' => $orderIds]);           
			}
        }

        $dateStart = yii::$app->request->get('date_start');
        
        if($dateStart) {
            $dateStop = yii::$app->request->get('date_stop');
            if($dateStop) {
                $dateStop = date('Y-m-d', strtotime($dateStop));
            }
            
            $dateStart = date('Y-m-d', strtotime($dateStart));

            if($dateStart == $dateStop) {
                $query->andWhere(['DATE_FORMAT(date, "%Y-%m-%d")' => $dateStart]);
            } else {
                if(!$dateStop) {
                    $query->andWhere('DATE_FORMAT(date, "%Y-%m-%d") = :dateStart', [':dateStart' => $dateStart]);
                } else {
                    $query->andWhere('date >= :dateStart', [':dateStart' => $dateStart]);
                }
                
                if($dateStop = yii::$app->request->get('date_stop')) {
                    $dateStop = date('Y-m-d', strtotime($dateStop));
                    $query->andWhere('date <= :dateStop', [':dateStop' => $dateStop]);
                }
            }
        } else {
            if($timeStart = yii::$app->request->get('time_start')) {
                $query->andWhere('date >= :timeStart', [':timeStart' => $timeStart]);
            }
            
            if($timeStop = yii::$app->request->get('time_stop')) {
                if(urldecode($timeStop) == '0000-00-00 00:00:00') {
                    $timeStop = date('Y-m-d H:i:s');
                }
                $query->andWhere('date <= :timeStop', [':timeStop' => $timeStop]);
            }
        }

        return $dataProvider;
    }
}
