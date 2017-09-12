<?php
namespace dvizh\order;

use dvizh\order\behaviors\ShippingCost;
use yii;

class Module extends \yii\base\Module
{
    const EVENT_ORDER_CREATE = 'create';
    const EVENT_ORDER_DELETE = 'delete';
    const EVENT_ELEMENT_DELETE = 'delete_element';
    const EVENT_ORDER_UPDATE_STATUS = 'update_status';

    public $countryCode = 'RU';

    public $adminRoles = ['admin', 'superadmin'];
    public $operatorRoles = ['manager', 'admin', 'superadmin'];
    public $operatorOpenStatus = 'process';

    public $orderStatuses = ['new' => 'Новый', 'approve' => 'Подтвержден', 'cancel' => 'Отменен', 'process' => 'В обработке', 'done' => 'Выполнен'];
    public $defaultStatus = 'new';

    public $successUrl = '/order/info/thanks/';
    public $orderCreateRedirect = 'order/view';

    public $robotEmail = "no-reply@localhost";
    public $dateFormat = 'd.m.Y H:i:s';
    public $robotName = 'Robot';
    public $adminNotificationEmail = false;
    public $clientEmailNotification = true;

    public $currency = ' р.';
    public $currencyPosition = 'after';
    public $priceFormat = [2, '.', ''];

    public $cartCustomFields = ['Остаток' => 'amount'];

    public $paymentFormAction = false;
    public $paymentFreeTypeIds = false;

    public $superadminRole = 'superadmin';

    public $createOrderUrl = false;

    public $userModel = '\dvizh\client\models\Client';
    public $userSearchModel = '\dvizh\client\models\client\ClientSearch';

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
    public $showCountColumn = true;

    private $mail;

    public $discountDescriptionCallback = '';

    public $searchByElementNameArray = null;

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
