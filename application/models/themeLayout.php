<?php
/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 16:24
 */

class ThemeLayout extends IkantamDataMapper
{
    /**
     * @var array
     */
    public $has_many = array(
        'templates' => array(
            'class' => 'ThemeTemplate',
            'other_field' => 'layout'
        )
    );
    /**
     * @var array
     */
    public $has_one = array(
        'theme' =>array(
            'class' => 'Theme',
            'other_field' => 'layouts',
        )
    );

    /**
     * @param int $id -  optional
     */
    public function __construct($id = null)
    {
        $this->table = get_instance()->container->param('ikantam.theme.table.prefix') . 'layouts';
        parent::__construct($id);
    }

    /**
     * Get filter for this model
     * @return \Layout_Filter
     */
    public function getFilter()
    {
        if (!class_exists('Layout_Filter')) {
            get_instance()->load->library('layout_filter', $this);
        }

        return new Layout_Filter($this);
    }

    /**
     * Get related theme
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme->get();
    }

    /**
     * Get related theme config
     * @return array
     */
    public function getThemeConfig()
    {
        return $this->getTheme()->getConfig();
    }

    /**
     * Get part of theme config considered to this layout
     * @return array
     */
    public function getLayoutConfig()
    {
        $themeConfig  = $this->getThemeConfig();
        return @$themeConfig['options']['layout'][$this->name];
    }

    /**
     * Get path to layout directory
     * @return string
     */
    public function getPath()
    {
        return $this->getTheme()->getPath();
    }

    /**
     * Get image url
     * @return string
     */
    public function getImageUrl()
    {
        $layoutConfig = $this->getLayoutConfig();
        return site_url($this->getPath() . '/' . $layoutConfig['thumb']);
    }
} 