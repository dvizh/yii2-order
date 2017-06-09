Yii2-order
==========
Это модуль для реализации функцинала заказа на сайте. Сейчас в заказ попадают элементы корзины, советую в качестве сервиса корзины использовать модуль [dvizh/yii2-cart](https://github.com/dvizh/yii2-cart).

Функционал:

* Просмотр и управление заказами в админке
* Управление полями заказа в админке
* Управление способами доставки и оплаты в админке

Установка
---------------------------------
Выполнить команду

```
php composer require dvizh/yii2-order "@dev"
```

Или добавить в composer.json

```
"dvizh/yii2-order": "@dev",
```

И выполнить

```
php composer update
```

Далее, мигрируем базу:

```
php yii migrate --migrationPath=vendor/dvizh/yii2-order/src/migrations
```

Далее, связываем модуль с корзиной. Устанавливаем [dvizh/yii2-cart](https://github.com/dvizh/yii2-cart)

Создаем аспект:

```
<?php
namespace common\aspects;

use dvizh\order\models\Element;
use Yii;

class OrderFilling extends \yii\base\Behavior
{
    public function events()
    {
        return [
            'create' => 'putElements'
        ];
    }

    public function putElements($event)
    {
        $order = $event->model;

        foreach(yii::$app->cart->elements as $element) {
            $elementModel = new Element;

            $elementModel->setOrderId($order->id);
            $elementModel->setAssigment($order->is_assigment);
            $elementModel->setModelName($element->getModelName());
            $elementModel->setName($element->getName());
            $elementModel->setItemId($element->getItemId());
            $elementModel->setCount($element->getCount());
            $elementModel->setBasePrice($element->getPrice(false));
            $elementModel->setPrice($element->getPrice());
            $elementModel->setOptions(json_encode($element->getOptions()));
            $elementModel->setDescription('');
            $elementModel->saveData();
        }

        $order->base_cost = 0;
        $order->cost = 0;

        foreach($order->elements as $element) {
            $order->base_cost += ($element->base_price*$element->count);
            $order->cost += ($element->price*$element->count);
        }
        $order->save();

        yii::$app->cart->truncate();
    }
}
```

Чтобы связать модуль заказа с корзиной, присваиваем аспект модулю заказа в конфиге:
```
        'order' => [
            'class' => 'dvizh\order\Module',
            //...
            'as order_filling' => '\common\aspects\OrderFilling',
        ],
```

Установка как модуля
---------------------------------
Если Вы планируете вносить множество измененией в код, лучшей практикой будет установить расширение как модуль.

Клонируете с Github расширение в нужную папку:
```
git clone https://github.com/dvizh/yii2-order.git
```

В composer.json добавляете путь до модуля:
```
"autoload": {
    "psr-4": {
      "dvizh\\order\\": "common/modules/order"
    }
  }
```
Запускаете update:
```
php composer update
```

Делаете миграции:
```
php yii migrate --migrationPath=common/modules/order/migrations
```

Добавляете в конфиг:
```
'bootstrap' => ['dvizh\order\Bootstrap'],
```
Связываете с корзиной, как описано выше.

Подключение и настройка
---------------------------------
В конфигурационный файл приложения добавить модуль order

```php
    'modules' => [
        'order' => [
            'class' => 'dvizh\order\Module',
            'layoutPath' => 'frontend\views\layouts',
            'successUrl' => '/page/thanks', //Страница, куда попадает пользователь после успешного заказа
            'adminNotificationEmail' => 'test@yandex.ru', //Мыло для отправки заказов
        ],
        //...
    ]
```


Сервисы
---------------------------------

Модуль автоматически инжектит в yii2 (в Service locator) компонент order (сервис), который доступен глобально через yii::$app->order и предоставляет следующие сервисы:

* get($id) - получить заказ по ID
* getStatInMoth($month) - получить статистику заказов за месяц
* getStatByDate($date) - получить статичтику заказов за день
* getStatByDatePeriod($dateStart, $dateStop) - получить статистику заказов за период
* getStatByModelAndDatePeriod($model, $dateStart, $dateStop) - получить статистику заказов определенной модели за период

Виджеты
---------------------------------
За вывод формы заказа отвечает виджет dvizh\order\widgets\OrderForm

```php
<?=OrderForm::widget();?>
```

Кнопка "заказ в один клик" - dvizh\order\widgets\OneClick:

```php
<?=OneClick::widget(['model' => $model]);?>
```

Триггеры
---------------------------------

В Module:

 * create - создание заказа
 * delete_order - удаление заказа
 * delete_element - удаление элемента заказа

Пример использования через конфиг:

```php
    'order' => [
        'class' => 'dvizh\order\Module',
        'on create' => function($event) {
            send_sms(...); //Отправляем СМС оповещение
        },
    ],
```

Онлайн оплата
---------------------------------
Чтобы добавить способ оплаты, перейдите в ?r=/order/payment-type/index, добавьте новый способ, где в поле "Виджет" укажите класс виджета, который будет отдавать форму оплаты. Виджеты оплаты устанавливаются отдельно.

* Paymaster.ru: [dvizh/yii2-paymaster](https://github.com/dvizh/yii2-paymaster)
* Liqpay: [dvizh/yii2-liqpay](https://github.com/dvizh/yii2-liqpay)
* Сбербанк: [dvizh/yii2-sberbank-payment](https://github.com/dvizh/yii2-sberbank-payment)

Скриншоты
---------------------------------
_**Форма заказа**_
![1](https://cloud.githubusercontent.com/assets/27691515/25094364/7dbb15a6-239f-11e7-992d-1325560546f3.png)
_**Главная панель**_
![2](https://cloud.githubusercontent.com/assets/27691515/25094385/91f5be9a-239f-11e7-8803-1c0f491f2f13.png)
_**Поиск по заказам**_
![3](https://cloud.githubusercontent.com/assets/27691515/25094387/91f8d224-239f-11e7-8ec4-88c93598121e.png)
_**Статистика**_
![4](https://cloud.githubusercontent.com/assets/27691515/25094384/91f52e44-239f-11e7-803b-f3454a74eed1.png)
