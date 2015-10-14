<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('google_rank') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <?php if ( ! empty($available_periods_names) ): ?>
            <div class="col-sm-2 col-md-1">
                <p class="blue-color text-size p-t10"><?= lang('period') ?></p>
            </div>
            <div class="col-sm-4">
                <?php echo form_dropdown('period', $available_periods_names, '0', ' class="select_block" id="period"'); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-xs-12 hidden-phone">
            <table class="responsive-table rank">
                <thead class="table_head">
                <tr>
                    <th><?= lang('keyword_phrase') ?></th>
                    <th><?= lang('current_rank') ?></th>
                    <th><?= lang('rank_change') ?></th>
                </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        </div>
    </div>
</div>
<script id="tbody-hidden-phone-template" type="text/x-handlebars-template">
    {{#if result}}
    {{#each result}}
    <tr>
        <td data-th="<?= lang('keyword_phrase') ?>">{{this.keyword}}</td>
        <td data-th="<?= lang('current_rank') ?>">{{this.last_rank}}</td>
        <td data-th="<?= lang('rank_change') ?>">
            {{#if_gt this.rank_change compare=0}}
            <span class="rank_good">+{{this.rank_change}}</span>
            {{/if_gt}}
            {{#if_lt this.rank_change compare=0}}
            <span class="rank_bad">{{this.rank_change}}</span>
            {{/if_lt}}
            {{#if_eq this.rank_change compare=0}}
            {{this.rank_change}}
            {{/if_eq}}
        </td>
    </tr>
    {{/each}}
    {{else}}
    <tr>
        <td colspan="3" class="empty"><?= lang('no_mentioned') ?></td>
    </tr>
    {{/if}}
</script>
<script id="tbody-visible-phone-template" type="text/x-handlebars-template">
    {{#if result}}
    {{#each result}}
    <tr>
        <th><?= lang('keyword_phrase') ?></th>
        <td>{{this.keyword}}</td>
    </tr>
    <tr>
        <th><?= lang('current_rank') ?></th>
        <td>{{this.last_rank}}</td>
    </tr>
    <tr>
        <th><?= lang('rank_change') ?></th>
        <td>
            {{#if_gt this.rank_change compare=0}}
            <span class="rank_good">+{{this.rank_change}}</span>
            {{/if_gt}}
            {{#if_lt this.rank_change compare=0}}
            <span class="rank_bad">{{this.rank_change}}</span>
            {{/if_lt}}
            {{#if_eq this.rank_change compare=0}}
            {{this.rank_change}}
            {{/if_eq}}
        </td>
    </tr>
    <tr><td colspan="2" class="separator"></td></tr>
    {{/each}}
    {{else}}
    <tr>
        <td colspan="2">
            <?= lang('no_results') ?>
        </td>
    </tr>
    {{/if}}
</script>