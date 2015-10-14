<?php
/**
 * Author: Alex P.
 * Date: 08.04.14
 * Time: 11:22
 */

namespace Core\Service\Theme;

use Ikantam\Theme\Interfaces\StorageInterface;
use Ikantam\Theme\Theme;
use Ikantam\Theme\Abstracts\ComponentHandlerAbstract as Handler;

/**
 * Class Installer
 * @package Core\Service\Theme
 */
class Installer
{
    /**
     * @var \Ikantam\Theme\Interfaces\StorageInterface
     */
    protected $storage ;

    /**
     * @var string
     */
    protected $handlerClassName ;

    /**
     * @param StorageInterface $storage - to save or delete theme
     * @param string $handlerClassName
     */
    public function __construct(StorageInterface $storage, $handlerClassName)
    {
        $this->storage = $storage;
        $this->handlerClassName = $handlerClassName;
    }

    /**
     * Install theme via storage interface
     * @param Theme $theme
     * @return void
     */
    public function install(Theme $theme)
    {
        $handler = $this->handlerClassName;
        foreach ($theme->getLayouts() as $layoutName => $layout) {
            foreach ($layout->getTemplateNames() as $templateName) {
                $html = $handler::processHtml($theme->buildLayoutWithTemplate($layoutName, $templateName));
                $this->storage->save($theme, $layout, $templateName, $html);
            }
        }
    }

    /**
     * Uninstall theme
     * @param Theme $theme
     * @return void
     */
    public function uninstall(Theme $theme)
    {
        $this->storage->delete($theme->getName());
    }    
} 