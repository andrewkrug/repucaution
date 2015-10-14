<?php
/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 16:29
 */

class TemplateTag extends IkantamDataMapper
{
    public function __construct($id = null)
    {
        $this->table = get_instance()->container->param('ikantam.theme.table.prefix') . 'templates_tags';
        parent::__construct($id);
    }
} 