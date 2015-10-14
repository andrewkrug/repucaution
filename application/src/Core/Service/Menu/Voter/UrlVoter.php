<?php
/**
 * User: alkuk
 * Date: 26.05.14
 * Time: 15:29
 */

namespace Core\Service\Menu\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;

class UrlVoter implements VoterInterface
{
    private $uri;

    public function __construct($uri = null)
    {
        $this->uri = $uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function matchItem(ItemInterface $item)
    {
        if (null === $this->uri || null === $item->getUri()) {
            return null;
        }

        $urlLength = strlen($item->getUri());
        $currentUrlTrimmed = substr($this->uri, 0, $urlLength);

        if ($item->getUri() === $currentUrlTrimmed) {
            return true;
        }

        return null;
    }
}
