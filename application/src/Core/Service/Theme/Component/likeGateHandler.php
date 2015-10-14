<?php
/**
 * Author: Alex P.
 * Date: 21.04.14
 * Time: 18:29
 */

namespace Core\Service\Theme\Component;


use Ikantam\Theme\Abstracts\ComponentAbstract;
use Ikantam\Theme\Component\EditModeHandler;

class likeGateHandler extends EditModeHandler
{
    /**
     * Prepare components (set classes to nodes etc.)
     * @param ComponentAbstract $component
     * @param \simple_html_dom_node $node
     */
    protected function beforeHandle(ComponentAbstract $component, \simple_html_dom_node $node)
    {
        $this->attachAngularApp();

        if ($class = $component->getOption('class')) {
            if (is_array($class)) {
                $class = implode(' ', $class);
            } elseif (is_callable($class)) {
                $class = $class();
            }
            if (is_string($class)) {
                $node->setAttribute('class', $class);
            }
        }

        $id = $component->getOption('id');
        if (!$id) {
            $id = $node->getAttribute('id');
        }
        if (!$id) {
            $id = $this->getId($node->getAttribute('data-component'));
        }
        if ($node->getAttribute('data-component') === 'likeGate'){
            $node->setAttribute('ng-init', 'likeGateEnabled = 1');
            $node->setAttribute('ng-show', 'likeGateEnabled');
            $header = $node->find('#likeGate-header', 0);
            $message = $node->find('#likeGate-message', 0);
            if ($header) {
                $header->setAttribute('ng-bind', 'likeGateHeader');
            }
            if ($message) {
                $message->setAttribute('ng-bind', 'likeGateMessage');
            }
            $node->setAttribute('ng-model', 'pageData.' . $id);
        }
    }
} 