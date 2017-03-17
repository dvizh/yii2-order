<?php
namespace dvizh\order;

use dvizh\order\behaviors\ShippingCost;
use yii;

class Module extends \yii\base\Module
{
    const EVENT_ORDER_CREATE = 'create';
    const EVENT_ORDER_DELETE = 'delete';
    const EVENT_ELEMENT_DELETE = 'delete_element';

    public $orderStatuses = ['new' => 'Новый', 'approve' => 'Подтвержден', 'cancel' => 'Отменен', 'process' => 'В обработке', 'done' => 'Выполнен'];
    public $defaultStatus = 'new';
    public $successUrl = '/order/info/thanks/';
    public $orderCreateRedirect = 'order/view';
    public $robotEmail = "no-reply@localhost";
    public $dateFormat = 'd.m.Y H:i:s';
    public $robotName = 'Robot';
    public $ordersEmail = false;
    public $currency = ' р.';
    public $currencyPosition = 'after';
    public $priceFormat = [2, '.', ''];
    public $adminRoles = ['admin', 'superadmin'];
    public $cartCustomFields = ['Остаток' => 'amount'];

    public $paymentFormAction = false;
    public $paymentFreeTypeIds = false;

    public $adminMenu = ['orders', 'field', 'payment-type', 'payment'];
    public $superadminRole = 'superadmin';
    
    public $createOrderButton = true;
    
    public $operatorRoles = ['manager', 'admin', 'superadmin'];
    public $operatorOpenStatus = 'process';
    
    public $userModel = '\common\models\User';
    public $userSearchModel = 'backend\models\search\UserSearch';
    
    public $userModelCustomFields = [];
    
    public $productModel = 'dvizh\shop\models\Product';
    public $productSearchModel = 'dvizh\shop\models\product\ProductSearch';
    public $productCategories = null;
    
    public $orderColumns = ['client_name', 'phone', 'email', 'payment_type_id', 'shipping_type_id'];
    
    public $elementModels = []; //depricated
	
    public $sellers = null; //collable, return seller list
    
    public $sellerModel = '\common\models\User';
    
    public $workers = [];
    
    public $elementToOrderUrl = false;

    public $showPaymentColumn = false;
    
    private $mail;
    
    public $discountDescriptionCallback = '';

    public function init()
    {
        if(yii::$app->has('cart') && $orderShippingType = yii::$app->session->get('orderShippingType')) {
            if($orderShippingType > 0) {
                yii::$app->cart->attachBehavior('ShippingCost', new ShippingCost);
            }
        }
        
        return parent::init();
    }
    
    public function getMail()
    {
        if ($this->mail === null) {
            $this->mail = yii::$app->getMailer();
            $this->mail->viewPath = __DIR__ . '/mails';
            
            if ($this->robotEmail !== null) {
                $this->mail->messageConfig['from'] = $this->robotName === null ? $this->robotEmail : [$this->robotEmail => $this->robotName];
            }
        }
        
        return $this->mail;
    }
    
    public function getWorkersList()
    {
        if(is_callable($this->workers)) {
            $values = $this->workers;
            
            return $values();
        } else {
            return $this->workers;
        }
        
        return [];
    }
    
    public function getProductCategoriesList()
    {
        if(is_callable($this->productCategories))
        {
            $values = $this->productCategories;
            
            return $values();
        }
        
        return [];
    }
    
    public function getSellerList()
    {
        if(is_callable($this->sellers)) {
            $values = $this->sellers;
            
            return $values();
        }
        
        return [];
    }
}
