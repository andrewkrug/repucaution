<?php
/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 18:29
 */

require_once  APPPATH.'libraries/Ikantam_Filter/Ikantam_Filter.php';

/**
 * @method Template_Filter theme_name ($operator, $value) filter model by theme name
 * @method Template_Filter layout_name ($operator, $value) filter model by layout name
 * @method Template_Filter layout_id ($operator, $value) filter model by layout id
 * @method Template_Filter name ($operator, $value) filter model by name
 * @method Template_Filter id ($operator, $value) filter model by id
 */
class Template_Filter extends Ikantam_Filter
{
    protected $_acceptClass = 'ThemeTemplate';

    protected $table = 'templates';

    public function __construct($model)
    {
        $prefix = get_instance()->container->param('ikantam.theme.table.prefix');
        $this->table = $table = $prefix . $this->table;

        $themesTable = $prefix . 'themes';
        $layoutsTable = $prefix . 'layouts';
        $tagsRelTable = $prefix . 'templates_tags';
        $tagsTable = 'tags';

        $this->_joins = array(
            'theme' => array('layout' => "INNER JOIN `{$themesTable}` ON " .
                "`{$themesTable}`.`id` = `{$layoutsTable}`.`theme_id`"),
            'layout' => "INNER JOIN `{$layoutsTable}` ON `{$table}`.`layout_id` = `{$layoutsTable}`.`id`",
            'tags_rel' =>  "INNER JOIN `{$tagsRelTable}` ON  `{$tagsRelTable}`.`template_id` = `{$table}`.`id`",
            'tags' => array('tags_rel' => "INNER JOIN `{$tagsTable}` ON " .
                "`{$tagsRelTable}`.`tag_id` = `{$tagsTable}`.`id`")
        );

        $this->_rules = array(
            'theme_name' => array('theme' => "`{$themesTable}`.`name`"),
            'layout_name' => array('layout' => "`{$layoutsTable}`.`name`"),
            'tag_name' => array('tags' => "`{$tagsTable}`.`tag_name`"),
            'layout_is_active' => array('layout' => "`{$layoutsTable}`.`is_active`"),
            'theme_is_active' => array('theme' => "`{$themesTable}`.`is_active`"),
        );

        $this->_select = array(
            'layout' => "`{$layoutsTable}`.`name` as `layout_name`",
        );

        parent::__construct($model);
    }
} 