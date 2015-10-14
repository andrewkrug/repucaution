<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 12:46
 */

namespace Core\Service\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Util\MenuManipulator;

class MenuBreadcrumbRenderer extends ListRenderer
{

    /**
     * @var \Knp\Menu\Util\MenuManipulator
     */
    protected $manipulator;


    public function __construct(
        MatcherInterface $matcher,
        MenuManipulator $manipulator,
        array $defaultOptions = array(),
        $charset = null
    ) {

        parent::__construct($matcher, $defaultOptions, $charset);

        $this->manipulator = $manipulator;

    }

    /**
     * {@inheritdoc}
     */
    public function render(ItemInterface $item, array $options = array())
    {
        $activeItem = $this->getActiveItem($item);
        if (!$activeItem) {
            return '';
        }

        $options = array_merge($this->defaultOptions, $options, array(
            'currentClass' => 'active'
        ));

        $breadcrumbArray = $this->manipulator->getBreadcrumbsArray($activeItem);

        $html = $this->renderList($breadcrumbArray, $options);

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }

    /**
     * Render list
     *
     * @param array $breadcrumbArray
     * @param array $options
     *
     * @return string
     */
    public function renderList(array $breadcrumbArray, array $options)
    {

        $parentItem = array_shift($breadcrumbArray);

        if (!count($breadcrumbArray)) {
            return '';
        }

        $html = '<ul class="breadcrumbs">';
        $html .= $this->renderChildren($breadcrumbArray, $options);
        $html .= '</ul>';

        return $html;
    }

    /**
     * Render children
     *
     * @param array $breadcrumbItems
     * @param array $options
     *
     * @return string
     */
    public function renderChildren(array  $breadcrumbItems, array $options)
    {
        $html = '';
        foreach ($breadcrumbItems as $item) {
            $html .= $this->renderItem($item, $options);
        }

        return $html;
    }

    /**
     * Render one item
     *
     * @param array $item
     * @param array $options
     *
     * @return string
     */
    public function renderItem(array $item, array $options)
    {

        // create an array than can be imploded as a class list
        $class = array();
        $attributes = array();

        $label = trim(strip_tags($item['label'], ''));
        $labelText = strip_tags($label);
        $labelIcon = str_replace($labelText, '', $label);
        $class[] = 'breadcrumbs_item';
        if ($this->matcher->isCurrent($item['item'])) {
            $class[] = $options['currentClass'];

        }
        $link = $label;
        $link = $labelIcon.'&nbsp;'.'<a href="'.$item['uri'].'" class="breadcrumbs_link">'.trim($labelText).'</a>';


        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        $html = '<li '.$this->renderHtmlAttributes($attributes).'>';
        $html .= $link;
        $html .= '</li>';

        return $html;
    }

    /**
     * Try to get active item
     *
     * @param ItemInterface $item
     *
     * @return ItemInterface|null
     */
    protected function getActiveItem(ItemInterface $item)
    {

        if ($this->matcher->isCurrent($item)) {
            return $item;
        }

        if ($item->hasChildren()) {
            foreach ($item->getChildren() as $child) {
                $isChildActive = $this->getActiveItem($child);
                if ($isChildActive) {
                    return $isChildActive;
                }
            }
        }

        return null;
    }
}
