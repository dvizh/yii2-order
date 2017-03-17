<?php
use yii\helpers\Html;
use dvizh\order\assets\Asset;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?=$this->title ?></title>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?=Html::csrfMetaTags() ?>
	<?php $this->head() ?>
	<style>
	body {
		padding: 0px;
		margin: 0px;
	}
	h1,h2,h3 {
		padding: 1px;
		margin: 1px;
	}
	#columns {
		width: 100%;
		font-size: 12px;
		margin-bottom: 100px;
	}
	#columns table {
		width: 99%;
	}
	#columns td, th {
		font-family: Lucida Console;
		padding: 3px;
		text-align: left;
	}
	</style>
</head>
<body>
<?php $this->beginBody() ?>
    <div class="columns-container">
        <div id="columns" class="container">
            <?= $content ?>
        </div>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>