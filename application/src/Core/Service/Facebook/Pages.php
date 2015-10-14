<?php
/**
 * Author: Alex P.
 * Date: 25.04.14
 * Time: 18:56
 */

namespace Core\Service\Facebook;


use Core\Service\Facebook\Interact\Auth;
use Ikantam\Facebook\PageManager;
use Ikantam\Facebook\Types\Page\PageAccess;

/**
 * Class Pages
 * @package Core\Service\Facebook
 */
class Pages
{
    /**
     * @var \Ikantam\Facebook\PageManager
     */
    protected $pageManager;

    /**
     * @param PageManager $pageManager
     * @param Interact\Auth $facebookAuth
     */
    public function __construct(PageManager $pageManager, Auth $facebookAuth)
    {
        $this->pageManager = $pageManager;

        if (!$this->pageManager->isLoggedIn()) {
            $facebookAuth->setRedirectUri( site_url('fbbuilder/bootstrap/'));
            $facebookAuth->runAuth();
        }
    }

    /**
     * Return pages list
     * @return array
     */
    public function getPages()
    {
        return $this->getPageManager()->getPagesList();
    }

    /**
     * Get concrete page
     * @param $pageId
     * @return mixed :Ikantam\Facebook\Types\Page | null
     */
    public function getPage($pageId)
    {
        return $this->getPageManager()->getPageById($pageId);
    }

    /**
     * @return PageManager
     */
    public function getPageManager()
    {
        return $this->pageManager;
    }

    /**
     * Retrieve the list of pages where user can add the tabs
     * @return array
     */
    public function getAdminPages()
    {
        $result = array();
        foreach ($this->getPages() as $id => $page){
            $permissions = $page->getAccess()->getPermissions();
            if ($permissions[PageAccess::PERMISSION_ADMINISTER] || $permissions[PageAccess::PERMISSION_EDIT_PROFILE]) {
                $result[$id] = $page;
            }
        }

        return $result;
    }

    /**
     * Add application tab to page
     * @param $pageId
     * @return bool|mixed
     */
    public function addTabToPage($pageId)
    {
        $page = $this->getPageManager()->getPageById($pageId);
        $appId = get_instance()->container->param('facebook.tab.application-id');

        return $this->getPageManager()->addAppTab($page, $appId);
    }
}
