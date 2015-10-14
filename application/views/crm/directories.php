
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('crm') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-sm-1 col-md-2 col-lg-1">
            <p class="p-t10 strong-size text_color"><?= lang('directory') ?></p>
        </div>
        <form class="directory" action="" >
            <div class="col-sm-3 ">
                <div class="form-group autocomplete_block">
                    <input type="text" class="form-control" placeholder="<?= lang('search_by_name') ?>" name="username" value="<?php echo $username; ?>" autocomplete="off"/>
                    <ul class="search-user ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" id="ui-id-1" style="display: none"></ul>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group autocomplete_block">
                    <input type="text" class="form-control" placeholder="<?= lang('search_by_company') ?>" name="company"  value="<?php echo $company; ?>" autocomplete="off"/>
                    <ul class="search-user ui-autocomplete ui-front ui-menu ui-widget ui-widget-content" style="display: none"></ul>
                </div>
            </div>
            <div class="col-sm-3">
                <button class="btn btn-save"><?= lang('search') ?></button>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <table class="responsive-table b-Bottom">
                <thead class="table_head">
                <th><?= lang('name') ?></th>
                <th><?= lang('company') ?></th>
                <th><?= lang('action') ?></th>
                </thead>
                <tbody  id="dir-container">
                    <?php echo $feed; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>