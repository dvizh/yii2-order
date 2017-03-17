<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="modal fade" id="usersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg choose-user-modal-window">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=yii::t('order', 'Clients');?></h4>
            </div>
            <div class="modal-body">
                <iframe src="<?=Url::toRoute(['/order/tools/find-users-window']);?>" id="users-list-window"></iframe>
            </div>
        </div>
    </div>
</div>
