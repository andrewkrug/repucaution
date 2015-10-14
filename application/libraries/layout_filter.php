<?php
/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 17:20
 */
require_once  APPPATH.'libraries/Ikantam_Filter/Ikantam_Filter.php';

/**
 * @method Layout_Filter name($operator, $value) filter model by name
 * @method Layout_Filter theme_id($operator, $value) filter model by theme id
 */
class Layout_Filter extends Ikantam_Filter
{
    protected $_acceptClass = 'ThemeLayout';

    protected $table = 'layouts';

    public function __construct($model)
    {
        $prefix = get_instance()->container->param('ikantam.theme.table.prefix');
        $themesTable = $prefix . 'themes';
        $this->table = $table = $prefix . $this->table;


        $this->_joins = array(
            "theme" => "INNER JOIN `{$themesTable}` ON `{$table}`.`theme_id` `{$themesTable}`.`id`"
        );

        $this->_rules = array(
            'theme_name' => array('theme' => "`{$themesTable}`.`name`"),
        );

        $this->_select = array(

        );

        parent::__construct($model);

    }
}
