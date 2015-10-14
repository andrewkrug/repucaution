<?php

namespace Core\Service\Menu\Extension;

use Knp\Menu\ItemInterface;
use Knp\Menu\Factory\ExtensionInterface;

/**
 * core factory extension with the main logic
 */
class CoreExtension implements ExtensionInterface
{
    /**
     * Builds the full option array used to configure the item.
     *
     * @param array $options
     *
     * @return array
     */
    public function buildOptions(array $options)
    {
        return array_merge(
            array(
                'path' => null,
                'icon_class' => null
            ),
            $options
        );
    }

    /**
     * Configures the newly created item with the passed options
     *
     * @param ItemInterface $item
     * @param array         $options
     */
    public function buildItem(ItemInterface $item, array $options)
    {

        if (!empty($options['path'])) {
            $item->setUri(site_url($options['path']));
        }
        if (!empty($options['icon_class'])) {
            $item->setLabel('<i class="'.$options['icon_class'].'"></i> '.$item->getLabel());
            $item->setExtra('safe_label', true);
        }

        $this->buildExtras($item, $options);
    }

    /**
     * Configures the newly created item's extras
     * Extras are processed one by one in order not to reset values set by other extensions
     *
     * @param ItemInterface $item
     * @param array         $options
     */
    private function buildExtras(ItemInterface $item, array $options)
    {
        if (!empty($options['extras'])) {
            foreach ($options['extras'] as $key => $value) {
                $item->setExtra($key, $value);
            }
        }
    }
}
