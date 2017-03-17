Yii2-order
==========
Это модуль для реализации функцинала заказа на сайте. Сейчас в заказ попадают элементы корзины, советую в качестве сервиса корзины использовать модуль [dvizh/yii2-cart](https://github.com/dvizh/yii2-cart).

Функционал:

* Добавление заказа, просмотр и управление заказами в админке
* Управление полями заказа в админке
* Управление способами доставки и оплаты в админке

Установка
---------------------------------
Выполнить команду

```
php composer require dvizh/yii2-order "*"
```

Или добавить в composer.json

```
"dvizh/yii2-order": "*",
```

И выполнить

```
php composer update
```

Далее, мигрируем базу:

```
php yii migrate --migrationPath=vendor/dvizh/yii2-order/migrations
```

Далее, связываем модуль с корзиной. Устанавливаем [dvizh/yii2-cart](https://github.com/dvizh/yii2-cart) и добавляем в начало конфига вашего приложения:

```
yii::$container->set('dvizh\order\interfaces\Cart', 'dvizh\order\drivers\Pistol88Cart');
```

Чтобы связать модуль с другой корзиной:
```
yii::$container->set('dvizh\order\interfaces\Cart', 'app\objects\Cart');
```

app\objects\Cart должен содержать класс, имплементирующий \dvizh\order\interfaces\Cart.

Подключение и настройка
---------------------------------
В конфигурационный файл приложения добавить модуль order

```php
    'modules' => [
        'order' => [
            'class' => 'dvizh\order\Module',
            'layoutPath' => 'frontend\views\layouts',
            'successUrl' => '/page/thanks', //Страница, куда попадает пользователь после успешного заказа
            'ordersEmail' => 'test@yandex.ru', //Мыло для отправки заказов
        ],
        //...
    ]
```

Все настройки модуля:

* orderStatuses - статусы (по умолчанию: 'new' => 'Новый', 'approve' => 'Подтвержден', 'cancel' => 'Отменен', 'process' => 'В обработке', 'done' => 'Выполнен')
* defaultStatus - статус нового заказа (по умолчанию 'new')
* successUrl - урл, куда будет перенаправлен покупатель в случае успешной покупки (по умолчанию /order/info/thanks/)
* ordersEmail - почта администратора, туда уходят письма с заказами
* robotEmail - e-mail робота (по умолчанию no-reply@localhost)
* robotName - имя почтового робота (по умолчанию Robot)
* orderColumns - массмв полей для вывода. Кастомные поля добавляются как массив, содержащий ID и наименование поля: ['field' => 2, 'label' => 'Автомобиль']
* dateFormat - формат даты (по умолчанию d.m.Y H:i:s)
* cartService - имя компонента, в которой реализована корзина (по умолчанию cart). Интерфейс смотреть в dvizh/yii2-cart.

* currency - валюта, по умолчанию рубли
* currencyPosition - позиция значка валюты относительно цены (before или after)
* priceFormat - формат цены (по умолчанию [2, '.', ''])
* adminRoles - список ролей, которые имеют доступ в CRUD заказа (по умолчанию ['admin', 'superadmin'])

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
