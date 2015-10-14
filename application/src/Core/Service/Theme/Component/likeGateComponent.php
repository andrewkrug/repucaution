<?php
/**
 * Author: Alex P.
 * Date: 21.04.14
 * Time: 16:39
 */

namespace Core\Service\Theme\Component;


use Ikantam\Theme\Abstracts\ComponentAbstract;
use simple_html_dom_node as DomNode;

/**
 * Class likeGateComponent
 * Theme must have fade element: <div data-component="likeGate">... element will be removed from html
 * if user disable like gate or if guest already liked the page
 * @package Core\Service\Theme\Component
 */
class likeGateComponent extends ComponentAbstract
{

    /**
     * Manipulate node
     * @param DomNode $node
     * @return mixed
     */
    public function handleNode(DomNode $node)
    {
        if (!$this->isLikeGateEnabled()) {
            //no need to like gate be showed - remove node
            $node->outertext = '';
        } else {
            $data = $this->getValue();
            $header = $node->find('#likeGate-header', 0);
            $message = $node->find('#likeGate-message', 0);
            if (isset($data['header'])) {
                $header->innertext = $data['header'];
            }
            if (isset($data['message'])) {
                $message->innertext = $data['message'];
            }
            if (isset($data['enabled'])) {
                $node->setAttribute('ng-init', "likeGateEnabled = " . $data['enabled']);
            }
            $header->setAttribute('ng-init', "likeGateHeader = '". $header->innertext ."'");
            $message->setAttribute('ng-init', "likeGateMessage = '". $message->innertext ."'");
        }
    }

    /**
     * Check if need to show like gate
     * @return bool
     */
    protected function isLikeGateEnabled()
    {
        if (is_bool($forceToShow = $this->getOption('forceLikeGateShow'))) {
            return $forceToShow;
        }
        $data = $this->getValue();
        if (!$data || !@$data['enabled']) {
            return false;
        }
        return !$this->isGuestLikedPage();
    }

    /**
     * Check if guest of the page liked it
     * @return bool
     */
    protected function isGuestLikedPage()
    {
        $signedRequest = $this->getOption('signed_request');
        if (is_array($signedRequest) && isset($signedRequest['page']['liked'])) {
            return $signedRequest['page']['liked']; //TODO: how about admin?
        }
        return false;
    }
}