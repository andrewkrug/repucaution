
<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<!--[if IE 8 ]><html class="ie8"><![endif]-->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge"/>
    <title><?php echo $site_name ?></title>

    <link rel="shortcut icon" href="<?php echo base_url();?>favicon.png">

    <?php echo CssJs::getInst()->get_css() ?>
    <?php echo CssJs::getInst()->get_header_js() ?>
    <?php echo JsSettings::instance()->get_settings_string();?>
</head>

<body>

<?php echo $this->template->block('_header', 'blocks/header/admin'); ?>

<div class="page-wrapper">
    <div class="wrapper">
        <?php echo $this->template->block('_sidebar', 'blocks/sidebar/sidebar_admin'); ?>
        <?php echo $this->template->block('_alert', 'blocks/alert'); ?>
        <div class="main">
            <?php echo $this->template->layout_yield(); ?>
        </div>
    </div>
</div>

<?php echo CssJs::getInst()->get_footer_js() ?>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-46232921-1', 'repucaution.com');
    ga('send', 'pageview');

</script>

</body>
</html>