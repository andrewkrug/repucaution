<?php
/**
 * Author: Alex P.
 * Date: 08.04.14
 * Time: 14:37
 */

require_once  APPPATH.'libraries/Ikantam_Filter/Ikantam_Filter.php';

/**
 * @method Userdata_Filter layout_id ($operator, $value) filter model by layout id
 * @method Userdata_Filter theme_id ($operator, $value) filter model by theme id
 */
class Userdata_Filter extends Ikantam_Filter
{
    protected $_acceptClass = 'ThemeUserData';

    protected $table = 'userdata';

    public function __construct($model)
    {
        $prefix = get_instance()->container->param('ikantam.theme.table.prefix');
        $this->table = $table = $prefix . $this->table;

        $themesTable = $prefix . 'themes';
        $layoutsTable = $prefix . 'layouts';
        $templatesTable = $prefix . 'templates';

        $this->_joins = array(
            'template' => "INNER JOIN `{$templatesTable}` ON `{$table}`.`template_id` = `{$templatesTable}`.`id`",
            'layout' =>  array('template' => "INNER JOIN `{$layoutsTable}` ON  " .
                "`{$layoutsTable}`.`id` = `{$templatesTable}`.`layout_id`"),
            'theme' => array('layout' => "INNER JOIN `{$themesTable}` ON " .
                "`{$themesTable}`.`id` = `{$layoutsTable}`.`theme_id`")
        );

        $this->_rules = array(
            'layout_id' => array('layout' => "`{$layoutsTable}`.`id`"),
            'theme_id' => array('theme' => "`{$themesTable}`.`id`"),
            'layout_name' => array('layout' => "`{$layoutsTable}`.`name`"),
            'template_name' => array('template' => "`{$templatesTable}`.`name`"),
            'theme_name' => array('theme' => "`{$themesTable}`.`name`"),
        );

        $this->_select = array(

        );

        parent::__construct($model);
    }
} 