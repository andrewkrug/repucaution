<?php
/**
 * Created by PhpStorm.
 * User: FleX
 * Date: 09.04.14
 * Time: 19:56
 */

class Tag extends IkantamDataMapper
{
    public $has_many = array(
        'themeTemplate' => array(
            'class' => 'ThemeTemplate',
            'other_field' => 'tag',
            'join_self_as' => 'tag',
            'join_other_as' => 'template'
            //'join_table' => see __construct
        )
    );

    /**
     * @param int $id optional
     */
    public function __construct($id = null)
    {
        $templateJoinTable = get_instance()->container->param('ikantam.theme.table.prefix') . 'templates_tags';
        $this->has_many['themeTemplate']['join_table'] = $templateJoinTable;

        parent::__construct($id);
    }
} 