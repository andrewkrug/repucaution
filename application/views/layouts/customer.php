<?php
/**
 * @var bool $piwik_enabled
 * @var string $piwik_domain
 * @var string $piwik_site_id
 */
?>
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
                
    <?php echo $this->template->block('_header', 'blocks/header/header'); ?>

    <div class="page-wrapper">
        <div class="wrapper">
            <?php echo $this->template->block('_sidebar', 'blocks/sidebar/sidebar'); ?>
            <?php echo $this->template->block('_alert', 'blocks/alert'); ?>
            <div class="main">
                <?php if(isset($no_subscription) && $no_subscription): ?>
                <div class="p-rl30 p-tb20">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="alert alert-danger" role="alert">
                                Your free trial expired, please <a href="<?php echo site_url("subscript/subscribe/{$c_user->id}/{$last_plan_id}") ?>">
                                    proceed with the payment to continue using the service</a> <br/>
                                <strong>IMPORTANT</strong>: we will keep your account and data within the next 30 days. If the subscription is not resumed, the account and its data will be deleted entirely.
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <?php echo $this->template->layout_yield(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php echo $this->template->block('_alert', 'blocks/modal/application/question_modal'); ?>
                
    <?php echo CssJs::getInst()->get_footer_js() ?>
	<script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-46232921-1', 'smintly.com');
      ga('send', 'pageview');

    </script>

</body>
</html>