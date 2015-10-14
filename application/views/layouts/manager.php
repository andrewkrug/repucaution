<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<!--[if IE 8 ]><html class="ie8"><![endif]-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $site_name ?></title>

    <link rel="shortcut icon" href="<?php echo base_url();?>favicon.png">

    <?php echo CssJs::getInst()->get_css() ?>
    <?php echo CssJs::getInst()->get_header_js() ?>
    <?php echo JsSettings::instance()->get_settings_string();?>
</head> 

<body>
                
    <?php echo $this->template->block('_header', 'blocks/header/header'); ?>

    <div class="container">
        <div class="row-fluid">

            <div class="span9">

                <?php echo $this->template->block('_alert', 'blocks/alert'); ?>
                
                <div class="row-fluid">


                    <?php echo $this->template->layout_yield(); ?>

                </div>
            </div>

        </div>
    </div>
                
    <?php echo CssJs::getInst()->get_footer_js() ?>

</body>
</html>