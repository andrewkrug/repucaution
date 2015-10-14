<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?php echo $site_name ?></title>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="shortcut icon" href="<?php echo base_url();?>favicon.png">

    <?php echo CssJs::getInst()->get_css() ?>
    <?php echo CssJs::getInst()->get_header_js() ?>
    <?php echo JsSettings::instance()->get_settings_string(); ?>
</head>

<body>
<?php echo $this->template->block('app_navigation', 'layouts/block/application/navigation'); ?>
<!-- ======[ main container ]====== -->
<div class="main-container" id="main-container">


    <div class="main-container-inner">
        <a class="menu-toggler" id="menu-toggler" href="#">
            <span class="menu-text"></span>
        </a>

        <!-- ======[ Sidebar ]====== -->
        <div class="sidebar" id="sidebar">
            <?php if($website_part == 'settings'):?>
                <?php echo menu_render('customer.settings');?>
            <?php else: ?>
                <?php echo menu_render('customer.main');?>
            <?php endif;?>
        </div>
        <!-- ======[ /sidebar ]====== -->

        <!-- ======[ main content ]====== -->
        <div class="main-content">
            <?php if($website_part == 'dashboard' && $breadcrumbs): ?>
                <?php echo $this->template->block('app_breadcrumbs', 'layouts/block/application/breadcrumbs', array('menu' => 'customer.main')); ?>
            <?php endif; ?>

            <?php echo $this->template->layout_yield(); ?>
        </div>
        <!-- ======[ main content ]====== -->

    </div>
    <!-- /.main-container-inner -->

    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="icon-double-angle-up icon-only bigger-110"></i>
    </a>

</div>
<!-- ======[ /main container ]====== -->


<!-- basic scripts -->
<?php echo $this->template->block('app_navigation', 'layouts/block/application/js_scripts_footer'); ?>

</body>
</html>
