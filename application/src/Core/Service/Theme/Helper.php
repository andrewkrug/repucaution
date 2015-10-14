<?php
/**
 * Author: Alex P.
 * Date: 15.04.14
 * Time: 11:12
 */

namespace Core\Service\Theme;


use Ikantam\Theme\Interfaces\StorageInterface;
use Ikantam\Theme\Interfaces\UserDataStorageInterface;

/**
 * Class Helper
 * Helps to work with installed themes
 * @package Core\Service\Theme
 */
class Helper
{
    /**
     * @var \Ikantam\Theme\Interfaces\UserDataStorageInterface
     */
    protected $userDataStorage;

    /**
     * @var string
     */
    protected $handlerClassName;

    /**
     * @var \Ikantam\Theme\Interfaces\StorageInterface
     */
    protected $themeStorage;

    /**
     * cache
     * @var array
     */
    protected $models = array(
        'themes' => array(),
        'layouts' => array(),
        'templates' => array(),
        'components' => array(),
    );

    /**
     * @param UserDataStorageInterface $userDataStorage
     * @param StorageInterface $themeStorage
     * @param string $pureModeHandlerClassName - component handler class name (to retrieve component id's)
     */
    public function __construct(
        UserDataStorageInterface $userDataStorage,
        StorageInterface $themeStorage,
        $pureModeHandlerClassName
    ) {
        $this->userDataStorage = $userDataStorage;
        $this->themeStorage = $themeStorage;
        $this->handlerClassName = $pureModeHandlerClassName;
    }

    /**
     * Check if theme is installed
     * @param $themeName
     * @return mixed
     */
    public function isThemeInstalled($themeName)
    {
        return $this->getThemeByName($themeName)->exists();
    }

    /**
     * Check if theme is active
     * @param $themeName
     * @return bool
     */
    public function isThemeActive($themeName)
    {
        return $this->isThemeInstalled($themeName) && $this->getThemeByName($themeName)->is_active;
    }

    /**
     * Check if layout is active
     * @param $themeName
     * @param $layoutName
     * @return bool
     */
    public function isLayoutActive($themeName, $layoutName)
    {
        return (bool)$this->getLayoutByName($themeName, $layoutName)->is_active;
    }

    /**
     * Check if template is active
     * @param $themeName
     * @param $layoutName
     * @param $templateName
     * @return bool
     */
    public function isTemplateActive($themeName, $layoutName, $templateName)
    {
        return (bool)$this->getTemplateByName($themeName, $layoutName, $templateName)->is_active;
    }

    /**
     * Save theme with user data (Component values)
     * @param int $userId
     * @param string $tabId
     * @param string $themeName
     * @param string $layoutName
     * @param string $templateName
     * @param array $data - where key is component id and value is component value
     * @return bool - true if at least one component value saved otherwise false
     */
    public function saveThemeForUser($userId, $tabId, $themeName, $layoutName, $templateName, array $data)
    {
        $componentIdList = $this->getComponentIdListByTemplate($themeName, $layoutName, $templateName);
        if (empty($componentIdList)) {
            return false;
        }
        $this->userDataStorage->setTabId($tabId);

        $result = false;
        $template = $this->getTemplateByName($themeName, $layoutName, $templateName);
        foreach ($data as $componentId => $value) {
            // save data only if component id exists in this template
            if (in_array($componentId, $componentIdList)) {
                $result = true;
                $this->userDataStorage->save($userId, $template->id, $componentId, $value);
            }
        }

        return $result;
    }

    /**
     * Retrieve all component ids from template html code (need to check user input)
     * @param string $themeName
     * @param string $layoutName
     * @param string $templateName
     * @return array
     */
    public function getComponentIdListByTemplate($themeName, $layoutName, $templateName)
    {
        $key = $layoutName . '->' . $layoutName . '->' . $templateName;
        if (!$idList = @$this->models['components'][$key]) {
            $idList = array();
            if (!$html = $this->themeStorage->retrieveHtml($themeName, $layoutName, $templateName)) {
                return $idList;
            }
            $handler = $this->handlerClassName;
            $handler::addComponentsDirectory(
                APPPATH . 'src/Core/Service/Theme/Component',
                'Core\\Service\\Theme\\Component'
            );
            $handler::processHtml(
                $html,
                function ($component, $componentName, $node) use (&$idList) {
                    $idList[] = $node->getAttribute('id');
                }
            );

            $this->models['components'][$key] = $idList;
        }
        return $idList;
    }

    /**
     * Set current html handler class name
     * @param string $className
     * @return $this
     */
    public function setHandlerClassName($className)
    {
        $this->handlerClassName = $className;
        return $this;
    }

    /**
     * @param $themeName
     * @return \Theme()
     */
    protected function getThemeByName($themeName)
    {
        if (!$theme = @$this->models['themes'][$themeName]) {
            $theme = $this->createThemeModel();
            $theme->get_by_name($themeName);
            $this->models['themes'][$themeName] = $theme;
        }
        return $this->models['themes'][$themeName];
    }

    /**
     * @param $themeName
     * @param $layoutName
     * @return \ThemeLayout
     */
    protected function getLayoutByName($themeName, $layoutName)
    {
        $key = $themeName . '->' . $layoutName;
        if (!$layout = @$this->models['layouts'][$key]) {
            $layout = $this->createLayoutModel();
            $layout->getFilter()->theme_name('=', $themeName)
                ->name('=', $layoutName)
                ->apply(1);
            $this->models['layouts'][$key] = $layout;
        }
        return $layout;
    }

    /**
     * @param $themeName
     * @param $layoutName
     * @param $templateName
     * @return \ThemeTemplate
     */
    protected function getTemplateByName($themeName, $layoutName, $templateName)
    {
        $key = $layoutName . '->' . $layoutName . '->' . $templateName;
        if (!$template = @$this->models['templates'][$key]) {
            $template = $this->createTemplateModel();
            $template->getFilter()
                ->name('=', $templateName)
                ->theme_name('=', $themeName)
                ->layout_name('=', $layoutName)
                ->apply(1);
            $this->models['themes'][$key] = $template;
        }
        return $template;
    }

    /**
     * @return \Theme
     */
    protected function createThemeModel()
    {
        return new \Theme();
    }

    /**
     * @return \ThemeTemplate
     */
    protected function createTemplateModel()
    {
        return new \ThemeTemplate();
    }

    /**
     * @return \ThemeLayout
     */
    protected function createLayoutModel()
    {
        return new \ThemeLayout();
    }
}
