<?php
/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 15:41
 */

class Theme extends IkantamDataMapper implements \Ikantam\Theme\Interfaces\ThemeInfoInterface
{
    /**
     * @var array
     */
    public $has_many = array(
        'layouts' => array(
            'class' => 'ThemeLayout',
            'other_field' => 'theme',
        )
    );

    /**
     * Theme config
     * @var array
     */
    protected $themeConfig ;

    /**
     * @param int $id -  optional
     */
    public function __construct($id = null)
    {
        $this->table = get_instance()->container->param('ikantam.theme.table.prefix') . 'themes';
        parent::__construct($id);
    }

    /**
     * Theme config (theme.yml)
     * @return array
     */
    public function getConfig()
    {
        if (!$this->themeConfig) {
            $this->themeConfig = get_instance()
                ->container
                ->get('core.service.theme.values.handler')
                ->output('array', $this->config_data);
        }

        return $this->themeConfig;
    }

    /**
     * Get path to theme directory
     * @return string
     */
    public function getPath()
    {
        if (!$themesPath = $this->cache('theme.root.directory')) {
            $themesPath = $this->cache(
                'theme.root.directory',
                get_instance()->container->param('theme.root.directory')
            );
        }

        return $themesPath . strtolower($this->name);
    }

    public function getImageUrl()
    {
        $themeConfig = $this->getConfig();

        return site_url($this->getPath() . '/' . $themeConfig['thumb']);
    }

    /**
     * Retrieve path where custom css are located in
     * !RELATIVE PATH!
     * @return string
     */
    public function getCssPath()
    {
        $config = $this->getConfig();
        return $config['options']['customStyles']['path'];
    }

    /**
     * Retrieve list of css
     * @return array
     */
    public function getCssList()
    {
        $config = $this->getConfig();
        return $config['options']['customStyles']['names'];
    }

    /**
     * Retrieve path where custom js are located in
     * @return string
     */
    public function getJsPath()
    {
        $config = $this->getConfig();
        return $config['options']['customScripts']['path'];
    }

    /**
     * Retrieve list of js
     * @return array
     */
    public function getJsList()
    {
        $config = $this->getConfig();
        return $config['options']['customScripts']['names'];
    }

    /**
     * Declaration of layout
     * @param string $name
     * @return array
     */
    public function getLayoutInfo($name = null)
    {
        $config = $this->getConfig();
        return $config['options']['layout'];
    }

    /**
     * Get name of theme
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get description of theme
     * @return mixed
     */
    public function getDescription()
    {
        $config = $this->getConfig();
        return $config['description'];
    }

    /**
     * Retrieve version
     * @return mixed
     */
    public function getVersion()
    {
        $config = $this->getConfig();
        return $config['version'];
    }

    /**
     * Convert config to array
     * @return array
     */
    public function toArray()
    {
        return $this->getConfig();
    }
}