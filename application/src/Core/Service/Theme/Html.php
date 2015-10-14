<?php
/**
 * Author: Alex P.
 * Date: 08.04.14
 * Time: 13:50
 */

namespace Core\Service\Theme;


use Ikantam\Theme\Interfaces\StorageInterface;
use Ikantam\Theme\Interfaces\BbcodeParserInterface;

/**
 * Class Html
 * @package Core\Service\Theme
 */
class Html
{
    /**
     * @var \Ikantam\Theme\Interfaces\StorageInterface
     */
    protected $storage;

    /**
     * @var UserDataInterface;
     */
    protected $userData;

    /**
     * @var array
     */
    protected $htmlHandlers = array();

    /**
     * @var \Ikantam\Theme\Interfaces\BbcodeParserInterface
     */
    protected $bbcodeParser;

    /**
     * Contains additional options
     * @var array
     */
    protected $componentOptions = array();

    /**
     * HTML code might be used in few cases:
     * pure code - raw, without any touches (for installation)
     * edit mode - shows when user edit his page data
     * view mode - shows when guest view user's page
     *
     * @param StorageInterface $storage
     * @param UserDataInterface $userData
     * @param BbcodeParserInterface $bbcodeParser
     * @param string $editModeHandlerClassName
     * @param string $viewModeHandlerClassName
     */
    public function __construct(
        StorageInterface $storage,
        UserDataInterface $userData,
        BbcodeParserInterface $bbcodeParser,
        $editModeHandlerClassName,
        $viewModeHandlerClassName

    ) {
        $this->storage = $storage;
        $this->userData = $userData;
        $this->bbcodeParser = $bbcodeParser;
        $this->setHandlerClass('edit', $editModeHandlerClassName);
        $this->setHandlerClass('view', $viewModeHandlerClassName);
    }

    /**
     * Set handler class name (class which will be handle html code|component parser)
     * @param $mode
     * @param $className
     */
    public function setHandlerClass($mode, $className)
    {
        $this->htmlHandlers[$mode] = $className;
    }

    /**
     * Retrieve html code of installed theme
     * This is pure html code
     *
     * @param $themeName
     * @param $layoutName
     * @param $templateName
     * @return string
     */
    public function get($themeName, $layoutName, $templateName)
    {
        return $this->storage->retrieveHtml($themeName, $layoutName, $templateName);
    }

    /**
     * Fills elements with user data and attaches necessary controls structure
     *
     * @param int $userId
     * @param string $tabId
     * @param string $themeName
     * @param string $layoutName
     * @param string $templateName
     * @return string html
     */
    public function getEdit($userId, $tabId, $themeName, $layoutName, $templateName)
    {
        return $this->getHtml($this->htmlHandlers['edit'], $userId, $tabId, $themeName, $layoutName, $templateName);
    }

    /**
     * Fills elements with user data
     *
     * @param int $userId
     * @param string $tabId
     * @param string $themeName
     * @param string $layoutName
     * @param string $templateName
     * @return string html
     */
    public function getView($userId, $tabId, $themeName, $layoutName, $templateName)
    {
        return $this->getHtml($this->htmlHandlers['view'], $userId, $tabId, $themeName, $layoutName, $templateName);
    }

    /**
     * Set option to concrete component
     * @param $componentId
     * @param $optionName
     * @param $value
     * @return $this
     */
    public function setComponentOptionByNodeId($componentId, $optionName, $value)
    {
        $this->componentOptions['id'][$componentId][$optionName] = $value;
        return $this;
    }

    /**
     * Set option to component group
     * @param $componentName
     * @param $optionName
     * @param $value
     * @return $this
     */
    public function setComponentOptionByName($componentName, $optionName, $value)
    {
        $this->componentOptions['name'][$componentName][$optionName] = $value;
        return $this;
    }

    /**
     * @param $optionName
     * @param $value
     * @return $this
     */
    public function setOptionToAllComponents($optionName, $value)
    {
        $this->componentOptions['all'][$optionName] = $value;
        return $this;
    }

    /**
     * @param string $handlerClass - class which handles html
     * @param int $userId
     * @param string $tabId
     * @param string $themeName
     * @param string $layoutName
     * @param string $templateName
     * @return mixed
     */
    protected function getHtml($handlerClass, $userId, $tabId, $themeName, $layoutName, $templateName)
    {
        $userData = $this->userData;
        $handlerClass::addComponentsDirectory(
            APPPATH . 'src/Core/Service/Theme/Component',
            'Core\\Service\\Theme\\Component'
        );
        $componentOptions = $this->componentOptions;
        $bbcodeParser = $this->bbcodeParser;
        $html = $handlerClass::processHtml(
            $this->get($themeName, $layoutName, $templateName),
            function ($component, $componentName, $node) use (
                $userData,
                $userId,
                $tabId,
                $themeName,
                $layoutName,
                $templateName,
                $componentOptions,
                $bbcodeParser
            ) {
                $nodeId = $node->getAttribute('id');
                if (isset($componentOptions['id'][$nodeId])) {
                    foreach($componentOptions['id'][$nodeId] as $option => $value) {
                        $component->setOption($option, $value);
                    }
                }
                if (isset($componentOptions['name'][$componentName])) {
                    foreach($componentOptions['name'][$componentName] as $option => $value) {
                        $component->setOption($option, $value);
                    }
                }
                if(isset($this->componentOptions['all'])) {
                    foreach($this->componentOptions['all'] as $option => $value) {
                        $component->setOption($option, $value);
                    }
                }


                $value = $userData->retrieveComponentValue(
                    $userId,
                    $tabId,
                    $themeName,
                    $layoutName,
                    $templateName,
                    $nodeId
                );
                if ($componentName === 'reachText') {
                    $component->setOption('bbcodeParser', $bbcodeParser);
                }
                if ($value) {
                    $component->setOption('value', $value);
                }

            }
        );

        return $html;
    }

} 