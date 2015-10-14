<?php
/**
 * @var array $data
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('cron_posts') ?></h1>
            <div class="row">
                <div class="col-xs-12">
                    <?php echo $this->template->block('app_breadcrumbs', 'layouts/block/application/breadcrumbs', array('menu' => 'customer.main')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main_block" id="ajax-container">
    <div class="row">
        <div class="col-xs-12">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <?php foreach ($data as $day => $time_array) : ?>
                    <li class="tab tab-active">
                         <li role="presentation" <?php echo ($day=='Monday') ? 'class="active"' : '' ?>>
                            <a href="#<?= $day; ?>" aria-controls="<?= $day; ?>" role="tab" data-toggle="tab">
                                <?= lang(mb_strtolower($day)); ?>
                            </a>
                         </li>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <?php foreach ($data as $day => $time_array) : ?>
                    <div role="tabpanel" class="tab-pane <?php echo ($day=='Monday') ? 'active' : '' ?>" id="<?= $day; ?>">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <?php foreach ($time_array as $time => $data) : ?>
                                <?php if(!empty($data)): ?>
                                    <?php
                                        $id = $day.'_'.preg_replace('/[\s|:]/', '_',$time);
                                    ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="<?= $id ?>">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?= $id ?>" aria-expanded="true" aria-controls="collapse_<?= $id ?>">
                                                    <?= $time ?>
                                                    <span class="badge"><?= count($data) ?></span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_<?= $id ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?= $id ?>">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <div class="web_radar m-t20 pull_border">
                                                            <?php foreach($data as $post_data) : ?>
                                                                <?php
                                                                /**@var Social_post_cron $post */
                                                                $post = $post_data['post'];
                                                                ?>
                                                                <div class="post_content dTable cron_post_<?= $post->id ?>">
                                                                    <div class="dRow">
                                                                        <div class="dCell">
                                                                            <div class="clearfix">
                                                                                <p class="pull-sm-left">
                                                                                    <span class="post_date"><?= lang('post_on') ?>: </span>
                                                                                    <?php echo $post_data['post_time']; ?>
                                                                                </p>
                                                                                <p class="pull-sm-right">
                                                                                    <a  href=""
                                                                                        class="edit link m-r10"
                                                                                        data-id="<?= $post->id ?>"
                                                                                    >
                                                                                        <?= lang('edit') ?>
                                                                                    </a>
                                                                                    <a class="remove_link"
                                                                                       href="<?php echo site_url('social/cron_posts/delete/'.$post->id);?>"
                                                                                       data-id="<?= $post->id ?>"
                                                                                    >
                                                                                        <?= lang('remove') ?>
                                                                                    </a>
                                                                                </p>
                                                                            </div>
                                                                            <p class="web_radar_text ">
                                                                                <?php echo $post->description; ?>
                                                                            </p>
                                                                            <div class="clearfix">
                                                                                <p class="pull-sm-left m-b0"><span class="post_date"><?= lang('post_to') ?>: </span>
                                                                                    <?= $post->getSocialsString(); ?>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php echo $this->template->block('_modal_edit_cron', 'social/cron_posts/blocks/_modal_edit_cron'); ?>