<?php
/**
 * Author: Alex P.
 * Date: 07.04.14
 * Time: 15:29
 */

namespace Core\Service\Theme\Storage;

use Core\Service\Theme\ValuesHandlerDb;
use Ikantam\Theme\Layout;
use Ikantam\Theme\Theme;
use Ikantam\Theme\Context;
use Ikantam\Theme\Exception;
use \Theme as ThemeModel;
use \ThemeLayout as LayoutModel;
use \ThemeTemplate as TemplateModel;
use \Tag as TagModel;

/**
 * Class DbStorage
 * @package Core\Service\Theme\Storage
 */
class DbStorage implements \Ikantam\Theme\Interfaces\StorageInterface
{
    /**
     * @var \Core\Service\Theme\ValuesHandlerDb
     */
    protected $valuesHandler ;

    /**
     * @param ValuesHandlerDb $valuesHandler
     */
    public function __construct(ValuesHandlerDb $valuesHandler)
    {
        $this->valuesHandler = $valuesHandler;

    }

    /**
     * Save theme in storage
     *
     * @param \Ikantam\Theme\Theme $theme
     * @param \Ikantam\Theme\Layout $layout
     * @param string $templateName
     * @param string $html
     * @throws \Ikantam\Theme\Exception
     * @return mixed
     */
    public function save(Theme $theme, Layout $layout, $templateName, $html)
    {
        $themeName = $theme->getName();
        $themeModel = $this->createThemeModel();

        $themeModel->get_by_name($themeName);

        if (!$themeModel->exists()) {
            $themeModel->name = $themeName;
            $themeModel->date_installed = time();
            $themeModel->config_data = $this->valuesHandler->input('array', $theme->getConfig()->toArray());
            $themeModel->save();
        }

        $layoutModel = $this->createLayoutModel();

        $layoutFilter = $layoutModel->getFilter();

        $layoutFilter->name('=', $layout->getName())
            ->theme_id('=', $themeModel->id)
            ->apply(1);

        if (!$layoutModel->exists()) {
            $layoutModel->name = $layout->getName();
            $layoutModel->theme_id = $themeModel->id;
            $layoutModel->save();
        }

        $templateModel = $this->createTemplateModel();
        $templateFilter = $templateModel->getFilter();

        $templateFilter->layout_id('=', $layoutModel->id)
            ->name('=', $templateName)
            ->apply(1);

        if ($templateModel->exists()) {
            throw new Exception("Template {$templateName} for layout {$layout->getName()} already installed");
        }

        $templateModel->layout_id = $layoutModel->id;
        $templateModel->name = $templateName;
        $templateModel->html = $html;

        $templateModel->save();

        if ($templateModel->exists()) {
            $this->tagTemplate($templateModel);
        }
    }

    /**
     * Delete theme from storage
     *
     * @param string $themeName
     * @return mixed
     */
    public function delete($themeName)
    {
        $themeModel = $this->createThemeModel();
        $themeModel->get_by_name($themeName);

        return $themeModel->delete();
    }

    /**
     * Retrieve html from storage
     *
     * @param string $themeName
     * @param string $layoutName
     * @param string $templateName
     * @return string
     */
    public function retrieveHtml($themeName, $layoutName, $templateName)
    {
        $templateModel = $this->createTemplateModel();
        $templateModel->getFilter()
            ->theme_name('=', $themeName)
            ->layout_name('=', $layoutName)
            ->name('=', $templateName)
            ->apply(1);

        /* This is an ugly solution to work with current theme */
        Context::setLayoutName($layoutName);
        Context::setTemplateName($templateName);
        Context::setThemeInfo($templateModel->getTheme());

        /* if (!$templateModel->exists()) {
             // throw exception ?
         } */

        return $templateModel->html;
    }

    /**
     * Create new theme model
     *
     * @return ThemeModel
     */
    protected function createThemeModel()
    {
        return new ThemeModel();
    }

    /**
     * Create new layout model
     *
     * @return LayoutModel
     */
    protected function createLayoutModel()
    {
        return new LayoutModel();
    }

    /**
     * Create new template model
     *
     * @return TemplateModel
     */
    protected function createTemplateModel()
    {
        return new TemplateModel();
    }

    /**
     * Create relation between template and tag
     * @param TemplateModel $template
     */
    protected function tagTemplate($template)
    {
        $tags = $template->getTags();
        $tags[] = 'all_themes';

        foreach($tags as $tagName) {
            $tag = $this->getCreateTagModel($tagName);
            $template->save($tag); // save relation
        }
    }

    /**
     * Retrieve tag model by name or save if not exist
     * @param string $name
     * @return \Tag
     */
    protected function getCreateTagModel($name)
    {
        $name = $this->valuesHandler->input('string', $name);
        $tagModel = new TagModel();
        $tagModel->get_by_tag_name($name);

        if (!$tagModel->exists()) {
            $tagModel->tag_name = $name;
            $tagModel->save();
        }

        return $tagModel;
    }

}
