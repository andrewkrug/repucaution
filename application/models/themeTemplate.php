<?php

/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 16:25
 */
class ThemeTemplate extends IkantamDataMapper
{
    /**
     * @var array
     */
    public $has_one = array(
        'layout' => array(
            'class' => 'ThemeLayout',
            'other_field' => 'templates'
        )
    );

    public $has_many = array(
        'tag' => array(
            'class' => 'Tag',
            'other_field' => 'themeTemplate',
            'join_self_as' => 'template',
            'join_other_as' => 'tag'
            //'join_table' => see __construct()
        )
    );

    /**
     * @param int $id optional
     */
    public function __construct($id = null)
    {
        $prefix = get_instance()->container->param('ikantam.theme.table.prefix');
        $this->table = $prefix . 'templates';
        $this->has_many['tag']['join_table'] = $prefix . 'templates_tags';
        parent::__construct($id);
    }

    /**
     * Get filter for this model
     * @return \Template_Filter
     */
    public function getFilter()
    {
        if (!class_exists('Template_Filter')) {
            get_instance()->load->library('template_filter', $this);
        }
        return new Template_Filter($this);
    }

    /**
     * Get related layout model
     * @return ThemeLayout
     */
    public function getLayout()
    {
        return $this->layout->get();
    }

    /**
     * Get related theme model
     * @return Theme
     */
    public function getTheme()
    {
        return $this->getLayout()->getTheme();
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
     * Get image url
     * @return string
     */
    public function getImageUrl()
    {
        $layoutConfig = $this->getLayout()->getLayoutConfig();
        $imagePath = $layoutConfig['templateImages'][$this->name];
        $url = site_url($this->getLayout()->getPath() . '/' . $imagePath) . '?v=0.1';

        return $url;
    }

    /**
     * Get list of colors
     * @param bool $samples - color sample is css hex number or alias such as #ffffff or white
     *  if this param is false(default) then return raw data where value is css file name
     * @return array
     */
    public function getColors($samples = false)
    {
        $layoutConfig = $this->getLayout()->getLayoutConfig();
        $colors = $layoutConfig['colors'][$this->name];
        $result = array();
        if ($samples) {
            foreach ($colors as $value) {
                if (is_array($value)) {
                    $result[] = key($value);
                } else {
                    $result[] = $value;
                }
            }
        } else {
            foreach ($colors as $value) {
                if (is_array($value)) {
                    $result[] = $value[key($value)];
                } else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }

    public function getDefaultColor($sample = false)
    {
        $colors = $this->getColors($sample);
        return array_shift($colors);
    }

    /**
     * Return list of tag names
     * @return array
     */
    public function getTags()
    {
        $layoutConfig = $this->getLayout()->getLayoutConfig();
        $result = array();
        if (isset($layoutConfig['tags'][$this->name])) {
            $result = $layoutConfig['tags'][$this->name];
        }

        return $result;
    }
} 