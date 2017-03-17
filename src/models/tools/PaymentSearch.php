<?php
namespace dvizh\order\models\tools;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use dvizh\order\models\Payment;

class PaymentSearch extends Payment
{
    public function rules()
    {
        return [
            [['order_id', 'amount', 'payment_type_id'], 'integer'],
            [['ip', 'description'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Payment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'payment_type_id' => $this->payment_type_id,
        ]);

        $query->andFilterWhere(['like', 'ip', $this->ip])
                ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
