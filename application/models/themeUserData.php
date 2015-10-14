<?php
/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 16:30
 */

class ThemeUserData extends IkantamDataMapper
{
    public function __construct($id = null)
    {
        $this->table = get_instance()->container->param('ikantam.theme.table.prefix') . 'userdata';
        parent::__construct($id);
    }

    /**
     * Get filter for this model
     * @return \Template_Filter
     */
    public function getFilter()
    {
        if (!class_exists('Userdata_Filter')) {
            get_instance()->load->library('userdata_filter', $this);
        }

        return new Userdata_Filter($this);
    }

} 