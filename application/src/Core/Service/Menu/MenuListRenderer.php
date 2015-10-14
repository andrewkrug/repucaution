<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 12:46
 */

namespace Core\Service\Menu;

use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\ItemInterface;

class MenuListRenderer extends ListRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge(
            array(
                'currentClass' => 'current active',
                'ancestorClass' => 'current_ancestor active',
                'allow_safe_labels' => true,
            ),
            $options
        );


        return parent::render($item, $options);
    }
}
