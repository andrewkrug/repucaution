<!--<div class="user-pager">
    <?php /*if ($page != 1) :*/?>
        <div class="pagi-prev" >
            <a class="page prev" href="" data-page="<?php /*echo ($page-1);*/?>">Prev page</a>
        </div>
    <?php /*endif; */?>
    <div class="pagi-next">
        <a class="page next" href="" data-page="<?php /*echo ($page+1);*/?>">Next page</a>
    </div>
</div>-->

<div class="row">
    <div class="col-xs-12">
        <ul class="pagination pull-right">
            <?php if ($page != 1) :?>
                <li class="pagination_item active">
                    <a href="" class="page prev pagination_link" data-page="<?php echo ($page-1);?>">Previous</a>
                </li>
            <?php endif; ?>
            <li class="pagination_item active">
                <a href="" class="page next pagination_link" data-page="<?php echo ($page+1);?>">Next</a>
            </li>
        </ul>
    </div>
</div>