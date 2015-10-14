<div class="row">
    <div class="col-xs-12">
        <ul class="pagination pull-right">
            <?php if ($page != 1) :?>
                <li class="pagination_item active">
                    <a href="" class="page prev pagination_link" data-page="<?php echo ($page-1);?>"><?= lang('previous') ?></a>
                </li>
            <?php endif; ?>
            <li class="pagination_item active">
                <a href="" class="page next pagination_link" data-page="<?php echo ($page+1);?>"><?= lang('next') ?></a>
            </li>
        </ul>
    </div>
</div>