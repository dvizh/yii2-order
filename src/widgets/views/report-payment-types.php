<table class="table">
    <?php foreach($report as $name => $sum) { if($sum) { ?>
        <tr>
            <td width="200"><?=$name;?></td>
            <td><?=$sum;?></td>
        </tr>
    <?php } } ?>
</table>