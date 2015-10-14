<?php

/**
 * Service Config Example
 *
 *  $config['service.name'] = array(
 *      'type' => PimpleContainer::TYPE_SERVICE, //required for service
 *      'class' => 'Core\Service\Payment\Gateway\PayPal\Merchant', //required for service
 *      'arguments' => array('@service.name.one', '@service.name.two', '%parameter.from.parameters.php%','string', 1, array()), //optional
 *  );
 *
 * To receive service in constructor - use @ and service name.
 * Example: @service.name.one -> fetch service "service.name.one"
 *
 * To specify the container that dependency is a factory, specify the type: PimpleContainer::TYPE_FACTORY
 * with type factory container will create a new instance of class on every request
 *
 * To get dependency as a function use 'type' => PimpleContainer::TYPE_PARAM and as a key use "function" instead of "class":
 * ...  'type' => PimpleContainer::TYPE_PARAM,
 *      'function' => function ($name) { return 'Hello, ' . $name; } ...
 *
 *
 * Use @container to receive Service Container
 *
 */

use Core\Service\DIContainer\PimpleContainer;


$config['codeigniter'] = function(){
    return get_instance();
};

$config['core.user.manager'] = function(){
    return get_instance()->ion_auth;
};

$config['core.request.current'] = function(){
    return get_instance()->config->getRequest();
};

$config['isAjax'] = array(
    'type' => PimpleContainer::TYPE_PARAM,
    'function' => function()
        {
            return get_instance()->input->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
        }
);

$config['renderJson'] = array(
    'type' => PimpleContainer::TYPE_PARAM,
    'function' => function ($data = array())
        {
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }
);


$config['core.subscriber'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Subscriber\Subscriber'
);

$config['core.radar'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Radar\Radar'
);
$config['core.filesystem'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Gaufrette\Filesystem',
    'arguments' => array('@core.filesystem.adapter')
);
$config['core.filesystem.adapter'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\File\FileSystemAdapterInflect',
    'arguments' => array('%filesystem.base.path%')
);
$config['core.file.upload'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\File\Upload',
    'arguments' => array('@core.filesystem', '@core.filesystem', '@codeigniter', '@current_user.model')
);
$config['core.job.queue.manager'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Job\MysqlQueueManager',
    'arguments' => array('@core.job.queue.launcher')
);
$config['core.job.queue.launcher'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Job\ModuleLauncher'

);

$config['core.image.manipulator'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Image\Manipulator'
);
$config['ikantam.theme.factory'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Ikantam\Theme\Factory'
);
$config['core.image.manipulator'] = array(
    'type' => 'service',
    'class' => 'Core\Service\Image\Manipulator'
);

//Mailer service
$config['core.mailer'] = array(
    'type' => 'service',
    'class' => 'Core\Service\Mail\MailerSwiftInflect',
    'arguments' => array('@swift.mailer', '%email.config%', '@template', '@swift.message')
);
$config['swift.mailer'] = array(
    'type' => 'service',
    'class' => 'Swift_Mailer',
    'arguments' => array('@swift.transport')
);
$config['swift.transport'] = function($container){
    return Core\Service\Mail\SwiftTransportFactory::create($container['parameters.container']->get('email.config', array()));
};
$config['core.theme.storage.db'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\Storage\DbStorage',
    'arguments' => array('@core.service.theme.values.handler'),
);
$config['core.service.theme.installer'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\Installer',
    'arguments' => array('@core.theme.storage.db', '%theme.install.html.component.handler.class.name%'),
);
$config['core.service.theme.user.data'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\DbUserData',
    'arguments' => array('@core.service.theme.values.handler'),
);
$config['jbbcode.parser'] = function() {
    $parser = new \JBBCode\Parser();
    $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
    return $parser;
};
$config['core.service.theme.bbcode.parser'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\BbcodeParser',
    'arguments' => array('@jbbcode.parser', '%theme.custom.bbcodes%'),
);
$config['core.service.theme.html'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\Html',
    'arguments' => array(
        '@core.theme.storage.db',
        '@core.service.theme.user.data',
        '@core.service.theme.bbcode.parser',
        '%theme.edit.html.component.handler.class.name%',
        '%theme.view.html.component.handler.class.name%',
    ),
);
$config['core.service.theme.values.handler'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\ValuesHandlerDb',
);
$config['core.service.theme.user.data.storage.db'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\Storage\DbUserDataStorage',
    'arguments' => array('@core.service.theme.values.handler'),
);
$config['core.service.theme.helper'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\Helper',
    'arguments' => array(
        '@core.service.theme.user.data.storage.db',
        '@core.theme.storage.db',
        '%theme.install.html.component.handler.class.name%'),
);
$config['core.service.theme.session.helper'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\SessionHelper',
    'arguments' => array('@codeigniter', '@core.service.theme.values.handler'),
);
$config['core.servce.theme.image.uploader'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Theme\Image\Uploader',
    'arguments' => array('@core.file.upload')
);
$config['current_user.model'] = function ($container)
{
    return $container['codeigniter']->ion_auth->getActiveUser();
};

$config['ci_image_lib'] = function($container)
{
    $container['codeigniter']->load->library('image_lib');
    return $container['codeigniter']->image_lib;
};
$config['template'] = function(){
    return new Template();
};

$config['swift.message'] = array(
    'type' => 'service',
    'class' => 'Core\Service\Mail\SwiftMailMessages'
);
$config['core.mail.sender'] = array(
    'type' => 'service',
    'class' => 'Core\Service\Mail\MailSender',
    'arguments' => array('@core.mailer')
);

$config['core.service.image.crop'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => '\Core\Service\Image\Crop',
    'arguments' => array('@ci_image_lib')
);

$config['core.service.app.access.control'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\AccessControl\AppAccessControl',
    'arguments' => array(
        '@core.service.role.access.control',
        '@core.service.plan.access.control',
        '@current_user.model'
    ),
);

$config['core.service.role.access.control'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\AccessControl\RoleAccessControl',
    'arguments' => array(
        '%parameters.role.access.control.abilities%'
    ),
);

$config['core.service.plan.access.control'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\AccessControl\PlanAccessControl',
    'arguments' => array(
       '@plan.features.acl.provider',
       '@user.feature.value.provider',
    ),
);

$config['plan.features.acl.provider'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'PlanFeaturesAcl\Provider',
    'arguments' => array(
         '@plan.features.acl.feature.validator',
    ),
);

$config['plan.features.acl.feature.validator'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'PlanFeaturesAcl\Validator\FeatureValidator',
    'arguments' => array(
        '@plan.features.acl.feature.constraint.factory',
    ),
);

$config['plan.features.acl.feature.constraint.factory'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'PlanFeaturesAcl\Validator\ConstraintFactory',
);

$config['user.feature.value.provider'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\AccessControl\UserFeatureValueProvider',
    'arguments' => array(
        '@core.status.user.info.mapper',
    ),
);

$config['core.firewall.route'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\FireWall\Route',
    'arguments' => array(
        '@core.user.manager',
        '@core.service.app.access.control',
        '%parameters.firewall.routes.restrict%',
    ),
);

$config['knp.menu.matcher'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Knp\Menu\Matcher\Matcher',
);

$config['knp.menu.manipulator'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Knp\Menu\Util\MenuManipulator',
);

$config['core.menu.renderer.list'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Menu\MenuListRenderer',
    'arguments' => array(
        '@knp.menu.matcher',
    ),
);

$config['core.menu.renderer.breadcrumb'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Menu\MenuBreadcrumbRenderer',
    'arguments' => array(
        '@knp.menu.matcher',
        '@knp.menu.manipulator',
    ),
);

$config['core.menu.builder.customer.main'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Menu\Builder\CustomerMainMenu',
    'arguments' => array(
        '@services.container',
    ),
);

$config['core.menu.builder.customer.settings'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Menu\Builder\CustomerSettingsMenu',
    'arguments' => array(
        '@services.container',
    ),
);

$config['core.menu.builder.admin.main'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Menu\Builder\AdminMainMenu',
    'arguments' => array(
        '@services.container',
    ),
);

$config['core.status.user.info.mapper'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Status\User\UsersInfoMapper',
);

$config['core.status.system'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Status\SystemStatus',
    'arguments' => array(
        '@core.system.settings.model'
    ),
);

$config['core.system.settings.model'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'System_setting',
);

$config['core.payment.gateway.factory'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Payment\Gateway\Factory',
);

$config['core.payment.system.provider'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Payment\PaymentSystemProvider',
    'arguments' => array(
        '@core.status.system',
        '@core.payment.gateway.factory',
    ),
);

$config['core.payment.transactions.manager'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Payment\TransactionsManager',
    'arguments' => array(
        '@core.payment.info.extractor.factory',
        '%parameters.payment.transaction.options%',
    ),
);

$config['core.payment.info.extractor.factory'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Payment\GatewayResponseInfoExtractorFactory',
);

$config['core.crm.manager'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Crm\Manager',
    'arguments' => array(
        '%parameters.crm.preview.options%',
    ),
);

$config['core.mailchimp.manager'] = array(
    'type' => PimpleContainer::TYPE_SERVICE,
    'class' => 'Core\Service\Mailchimp\Manager'
);