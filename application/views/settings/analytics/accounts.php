<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('settings_analytics') ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumbs">
                <li class="breadcrumbs_item">
                    <a href="" class="breadcrumbs_link"><?= lang('settings') ?></a>
                </li>
                <li class="breadcrumbs_item active">
                    <a href="" class="breadcrumbs_link"><?= lang('settings_analytics') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <?php echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
        <div class="col-md-10 col-lg-8">
            <div class="row">
                <div class="col-xs-12">
                    <p class="text_color strong-size"><?= lang('google_analytics') ?></p>
                    <p class="black smallText"><?= lang('select_google_analytics') ?></p>
                </div>
            </div>
            <form action="<?php echo site_url('settings/analytics/accounts'); ?>" method="POST">
                <div class="row custom-form">
                    <div class="col-xs-12 loading_ga_accounts">
                        <div class="col-xs-12 text-center p-tb10">
                            <img src="<?php echo site_url('public/theme/images/loading/loading.gif');?>" alt="">
                        </div>

                    </div>
                    <input type="hidden" name="account_name" value="<?php echo isset($account_info['account_name']) ? HTML::chars($account_info['account_name']) : ''; ?>">
                    <input type="hidden" name="webproperty_name" value="<?php echo isset($account_info['webproperty_name']) ? HTML::chars($account_info['webproperty_name']) : ''; ?>">
                    <input type="hidden" name="profile_name" value="<?php echo isset($account_info['profile_name']) ? HTML::chars($account_info['profile_name']) : ''; ?>">


                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="b-Top clearfix m-b10 m-t40 p-t20">
                            <div class="pull-sm-right">
                                <button class="btn btn-save" id="save"><?= lang('save') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script id="accounts-template" type="text/x-handlebars-template">
    <i class="icon-folder"></i>
    <ul class="tree-folder">
        {{#eachkeys accounts}}
        <li class="tree-folder_item">
            <p>
                <span class="tree-folder_item_name">
                    {{this.key}}
                </span>
            </p>
            <ul class="tree-folder_item_list">
                {{#eachkeys this.value}}
                <li class="tree-folder_item_list_point">
                    <p>
                        <span class="tree-folder_item_name">{{this.key}}</span>
                    </p>
                    <ul class="tree-folder_item_list m-t10">
                        {{#eachkeys this.value}}
                        <li class="tree-folder_item_list_point">
                            <label for="ga_{{this.key}}" class="deepest cb-radio" >
                                <input type="radio" name="profile" value="{{this.key}}" {{#if_eq this.key compare=../../../current}}checked="checked"{{/if_eq}} id="ga_{{this.key}}" data-account="{{../../key}}" data-webproperty="{{../key}}" data-profile="{{this.value}}">
                                {{this.value}}
                            </label>
                        </li>
                        {{/eachkeys}}
                    </ul>
                </li>
                {{/eachkeys}}
            </ul>
        </li>
        {{/eachkeys}}
    </ul>
</script>

<script id="alert-template" type="text/x-handlebars-template">
    <div class="alert alert-{{type}}">
        <div class="message"> <i class="icon"></i> <span>{{message}}</span></div>
        <div class="clear clear-20"></div>
    </div>
</script>