<!DOCTYPE html>
<!--[if !IE]><!--><script>if(/*@cc_on!@*/false){document.documentElement.className+=' ie10';}</script><!--<![endif]-->
<!--[if lt IE 9]><script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<!--[if IE 8 ]><html class="ie8"><![endif]-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Smintly email template</title>
</head>
<body>

<table style="width: 100%;">

    <tr>
        <th align="left" style="padding: 13px;" bgcolor="#434343" >
            <img src="<?php echo base_url(); ?>public/images/logo_email.png" alt="Smintly" />
        </th>
    </tr>

    <tr>
        <td>
            <?php echo $content;?>
        </td>
    </tr>

</table>

</body>
</html>