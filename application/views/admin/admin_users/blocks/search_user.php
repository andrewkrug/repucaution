<div class="row">
    <form class="directory" action="" >
        <div class="col-sm-3 ">
            <div class="form-group autocomplete_block">
                <input type="text" name="search" class="search-text form-control" autocomplete="off" placeholder="<?= lang('enter_text') ?>"/>
                <ul class="search-user ui-autocomplete" style="display: none"></ul>
            </div>
        </div>
        <div class="col-sm-3">

                <select name="filter" class="chosen-select">
                    <option value=""><?= lang('all') ?></option>
                    <option value="0"><?= lang('blocked') ?></option>
                    <option value="1"><?= lang('active') ?></option>
                </select>

        </div>
        <div class="col-sm-3">
            <input type="hidden" name="group" value="<?php echo $group;?>"/>
            <input type="hidden" name="limit" value="<?php echo $limit;?>"/>
            <button class="btn btn-save"><?= lang('search') ?></button>
        </div>
    </form>
</div>