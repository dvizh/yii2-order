<?php
namespace dvizh\order\controllers;

use yii;
use dvizh\order\models\tools\OrderSearch;
use dvizh\order\models\Order;
use dvizh\order\models\Payment;
use dvizh\order\models\tools\ElementSearch;
use dvizh\order\models\Field;
use dvizh\order\models\FieldValue;
use dvizh\order\models\PaymentType;
use dvizh\order\models\ShippingType;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use dvizh\order\events\OrderEvent;

class OrderController  extends Controller
{
    public function behaviors()
    {
        return [
            'adminAccess' => [
                'class' => AccessControl::className(),
				'only' => ['create', 'update', 'index', 'view', 'push-elements', 'print', 'delete', 'editable', 'to-order', 'update-status', 'fast-create'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'push-elements' => ['post'],
                    'to-order' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'print' | $action->id == 'create') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex($tab = 'orders')
    {
        $searchModel = new OrderSearch();

        $searchParams = yii::$app->request->queryParams;
        $searchParams['OrderSearch']['is_deleted'] = 0;
        
        //if(!yii::$app->user->can(current(yii::$app->getModule('order')->adminRoles))) {
        //    $searchParams['OrderSearch']['seller_user_id'] = yii::$app->user->id;
        //}

        $dataProvider = $searchModel->search($searchParams);

        if(!yii::$app->request->get('sort')) {
            $dataProvider->query->orderBy('id DESC');
        }
        
        if(yii::$app->request->get('time_start')) {
            $dataProvider->query->orderBy('order.timestamp ASC');
            $dataProvider->pagination = ["pageSize" => 100]; 
        }
        
        if($tab == 'assigments') {
            $dataProvider->query->andWhere(['order.is_assigment' => '1']);
        } else {
            $dataProvider->query->andWhere('(order.is_assigment IS NULL OR order.is_assigment = 0)');
        }

        $paymentTypes = ArrayHelper::map(PaymentType::find()->all(), 'id', 'name');
        $shippingTypes = ArrayHelper::map(ShippingType::find()->all(), 'id', 'name');

		$this->getView()->registerJs('dvizh.orders_list.elementsUrl = "'.Url::toRoute(['/order/tools/ajax-elements-list']).'";');

        return $this->render('index', [
            'tab' => Html::encode($tab),
            'searchModel' => $searchModel,
            'shippingTypes' => $shippingTypes,
            'paymentTypes' => $paymentTypes,
			'module' => $this->module,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPushElements($id)
    {
        if($model = $this->findModel($id)) {
            yii::createObject('\dvizh\order\services\Order')->filling();
        }

        $this->redirect(['/order/order/view', 'id' => $id]);
    }
    
    public function actionView($id, $push_cart = false)
    {
        $model = $this->findModel($id);
        
        $searchModel = new ElementSearch;
        $params = yii::$app->request->queryParams;
        if(empty($params['ElementSearch'])) {
            $params = ['ElementSearch' => ['order_id' => $model->id]];
        }

        $dataProvider = $searchModel->search($params);

        $paymentTypes = ArrayHelper::map(PaymentType::find()->all(), 'id', 'name');
        $shippingTypes = ArrayHelper::map(ShippingType::find()->all(), 'id', 'name');

        $fieldFind = Field::find();

        $this->getView()->registerJs('dvizh.order.outcomingAction = "' . Url::toRoute(['/order/tools/outcoming']) . '";');

        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'shippingTypes' => $shippingTypes,
            'fieldFind' => $fieldFind,
            'paymentTypes' => $paymentTypes,
            'model' => $model,
        ]);
    }

    public function actionPrintLast()
    {
        $this->layout = 'print';

        $this->enableCsrfValidation = false;

        $model = Order::find()->orderBy('id DESC')->limit(1)->one();
        
        $searchModel = new ElementSearch;
        $params = yii::$app->request->queryParams;
        if(empty($params['ElementSearch'])) {
            $params = ['ElementSearch' => ['order_id' => $model->id]];
        }

        $dataProvider = $searchModel->search($params);

        $paymentTypes = ArrayHelper::map(PaymentType::find()->all(), 'id', 'name');
        $shippingTypes = ArrayHelper::map(ShippingType::find()->all(), 'id', 'name');

        $fieldFind = Field::find();

        return $this->render('print', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'shippingTypes' => $shippingTypes,
            'fieldFind' => $fieldFind,
            'paymentTypes' => $paymentTypes,
            'module' => $this->module,
            'model' => $model,
        ]);
    }
    
    public function actionPrint($id)
    {
        $this->layout = 'print';

        $this->enableCsrfValidation = false;

        $model = $this->findModel($id);
        $searchModel = new ElementSearch;
        $params = yii::$app->request->queryParams;
        if(empty($params['ElementSearch'])) {
            $params = ['ElementSearch' => ['order_id' => $model->id]];
        }

        $dataProvider = $searchModel->search($params);

        $paymentTypes = ArrayHelper::map(PaymentType::find()->all(), 'id', 'name');
        $shippingTypes = ArrayHelper::map(ShippingType::find()->all(), 'id', 'name');

        $fieldFind = Field::find();

        return $this->render('print', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'shippingTypes' => $shippingTypes,
            'fieldFind' => $fieldFind,
            'paymentTypes' => $paymentTypes,
            'module' => $this->module,
            'model' => $model,
        ]);
    }

    public function actionFastCreate()
    {
        $model = new Order;

        if ($model->load(yii::$app->request->post()) && $model->validate() && $model->save()) {
            yii::createObject('\dvizh\order\services\Order')->filling();
            
            if($ordersEmail = yii::$app->getModule('order')->ordersEmail) {
                $sender = yii::$app->getModule('order')->mail
                    ->compose('admin_notification', ['model' => $model])
                    ->setTo($ordersEmail)
                    ->setFrom(yii::$app->getModule('order')->robotEmail)
                    ->setSubject(Yii::t('order', 'New order')." #{$model->id} ({$model->client_name})")
                    ->send();
            }

            $module = $this->module;
            $orderEvent = new OrderEvent(['model' => $model, 'elements' => $model->elements]);
            $this->module->trigger($module::EVENT_ORDER_CREATE, $orderEvent);
        } else {
            
        }
        
        return $this->redirect(yii::$app->request->referrer);
    }

    public function actionCreate()
    {
        $model = new Order;

        $this->getView()->registerJs("jQuery('.buy-by-code-input').focus();");

        if ($model->load(yii::$app->request->post()) && $model->save()) {
            yii::createObject('\dvizh\order\services\Order')->filling();

            if($ordersEmail = yii::$app->getModule('order')->ordersEmail) {
                $sender = yii::$app->getModule('order')->mail
                    ->compose('admin_notification', ['model' => $model])
                    ->setTo($ordersEmail)
                    ->setFrom(yii::$app->getModule('order')->robotEmail)
                    ->setSubject(Yii::t('order', 'New order')." #{$model->id} ({$model->client_name})")
                    ->send();
            }

            $module = $this->module;
            $orderEvent = new OrderEvent(['model' => $model, 'elements' => $model->elements]);
            $this->module->trigger($module::EVENT_ORDER_CREATE, $orderEvent);

            return $this->redirect([$this->module->orderCreateRedirect, 'id' => $model->id]);
        } else {
            //yii::$app->cart->truncate();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionGetOrderFormLight($useAjax = false)
    {
        return $this->renderAjax('formLight', [
            'useAjax' => $useAjax,
        ]);
    }

    public function actionCreateAjax()
    {
        $model = new Order;

        if ((yii::$app->has('worksess')) && $session = yii::$app->worksess->soon()) {
            $model->sessionId = $session->id;
        } else {
            $model->sessionId = null;
        }

        $order = yii::$app->request->post('Order');

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($model->load(yii::$app->request->post()) && $model->save()) {
            yii::createObject('\dvizh\order\services\Order')->filling();

            if($ordersEmail = yii::$app->getModule('order')->ordersEmail) {
                $sender = yii::$app->getModule('order')->mail
                    ->compose('admin_notification', ['model' => $model])
                    ->setTo($ordersEmail)
                    ->setFrom(yii::$app->getModule('order')->robotEmail)
                    ->setSubject(Yii::t('order', 'New order')." #{$model->id} ({$model->client_name})")
                    ->send();
            }

            $module = $this->module;
            $orderEvent = new OrderEvent(['model' => $model, 'elements' => $model->elements]);
            $this->module->trigger($module::EVENT_ORDER_CREATE, $orderEvent);

            $nextStepAction = false;

            return [
                'status' => 'success',
                'nextStep' => $nextStepAction
            ];

        } else {
            return [
                'status' => 'error'
            ];
        }
    }

    public function actionCustomerCreate()
    {
        $model = new Order(['scenario' => 'customer']);

        if ($model->load(yii::$app->request->post())) {
            $model->date = date('Y-m-d');
            $model->time = date('H:i:s');
            $model->timestamp = time();
            $model->status = $this->module->defaultStatus;
            $model->payment = 'no';
            $model->user_id = yii::$app->user->id;

            if($model->save()) {
                yii::createObject('\dvizh\order\services\Order', [$model])->filling();
                
                if($ordersEmail = yii::$app->getModule('order')->ordersEmail) {
                    $sender = yii::$app->getModule('order')->mail
                        ->compose('admin_notification', ['model' => $model])
                        ->setTo($ordersEmail)
                        ->setFrom(yii::$app->getModule('order')->robotEmail)
                        ->setSubject(Yii::t('order', 'New order')." #{$model->id} ({$model->client_name})")
                        ->send();
                }

                $module = $this->module;
                $orderEvent = new OrderEvent(['model' => $model]);
                $this->module->trigger($module::EVENT_ORDER_CREATE, $orderEvent);

                if($paymentType = $model->paymentType) {
                    $payment = new Payment;
                    $payment->order_id = $model->id;
                    $payment->payment_type_id = $paymentType->id;
                    $payment->date = date('Y-m-d H:i:s');
                    $payment->amount = $model->getCost();
                    $payment->description = yii::t('order', 'Order #'.$model->id);
                    $payment->user_id = yii::$app->user->id;
                    $payment->ip = yii::$app->getRequest()->getUserIP();
                    $payment->save();

                    if($widget = $paymentType->widget) {
                        return $widget::widget([
                            'autoSend' => true,
                            'orderModel' => $model,
                            'description' => yii::t('order', 'Order #'.$model->id),
                        ]);
                    }
                }

                return $this->redirect([yii::$app->getModule('order')->successUrl, 'id' => $model->id, 'payment' => $model->payment_type_id]);
            } else {
                yii::$app->session->setFlash('orderError', yii::t('order', 'Error (check required fields)'));
                return $this->redirect(yii::$app->request->referrer);
            }
        } else {
            yii::$app->session->setFlash('orderError', yii::t('order', 'Error (check required fields)'));
            return $this->redirect(yii::$app->request->referrer);
        }
    }

    public function actionUpdateStatus()
    {
        if($id = yii::$app->request->post('id')) {
            $model = Order::findOne($id);

            $status = yii::$app->request->post('status');

            yii::createObject('\dvizh\order\services\Order', [$model])->setStatus($status);

            die(json_encode(['result' => 'success']));
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(yii::$app->request->post()) && $model->save()) {
            if($fieldValues = yii::$app->request->post('FieldValue')['value']) {
                foreach($fieldValues as $field_id => $fieldValue) {
                    $fieldValueModel = new FieldValue;
                    $fieldValueModel->value = $fieldValue;
                    $fieldValueModel->order_id = $this->id;
                    $fieldValueModel->field_id = $field_id;
                    $fieldValueModel->save();
                }
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $module = $this->module;
        $orderEvent = new OrderEvent(['model' => $model]);
        $this->module->trigger($module::EVENT_ORDER_DELETE, $orderEvent);

        yii::createObject('\dvizh\order\services\Order', [$model])->cancel();

        return $this->redirect(['index']);
    }

    public function actionEditable()
    {
        $name = yii::$app->request->post('name');
        $value = yii::$app->request->post('value');
        $pk = unserialize(base64_decode(yii::$app->request->post('pk')));
        OrderElement::editField($pk, $name, $value);
    }

    public function actionToOrder()
    {
        if($order = $this->findModel(yii::$app->request->post('id'))) {
            $order->is_assigment = 0;
            $order->date = date('Y-m-d H:i:s');
            $order->timestamp = time();

            $order->save(false);

            $module = $this->module;
            $orderEvent = new OrderEvent(['model' => $order, 'elements' => $order->elements]);
            $this->module->trigger($module::EVENT_ORDER_CREATE, $orderEvent);

            $json = ['result' => 'success', 'order_view_location' => Url::toRoute(['/order/order/view', 'id' => $order->id])];
        } else {
            $json = ['result' => 'fail'];
        }

        return json_encode($json);
    }

    protected function findModel($id)
    {
        $orderModel = new Order;

        if (($model = $orderModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested order does not exist.');
        }
    }
}
