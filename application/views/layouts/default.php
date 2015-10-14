
<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<!--[if IE 8 ]><html class="ie8"><![endif]-->
<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge"/>
    <title><?php echo $site_name ?></title>

    <link rel="shortcut icon" href="<?php echo base_url();?>favicon.png">
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <?php echo CssJs::getInst()->get_css() ?>
    <?php echo CssJs::getInst()->get_header_js() ?>
    <style type="text/css">.fancybox-margin{margin-right:21px;}</style>
    <?php echo JsSettings::instance()->get_settings_string();?>
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o), m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-48438195-1', 'smintly.com');
        ga('send', 'pageview');
    </script>
</head>

<body>

<?php if(empty($c_user)):?>
    <?php echo $this->template->block('_header', 'blocks/header/auth'); ?>
<?php else: ?>
    <?php echo $this->template->block('_header', 'blocks/header/header'); ?>
<?php endif;?>

<div class="page-wrapper">
    <div class="wrapper">

        <?php echo $this->template->block('_alert', 'blocks/alert'); ?>

        <?php echo $this->template->layout_yield(); ?>

    </div>

</div>

<?php echo CssJs::getInst()->get_footer_js() ?>

</body>
</html>