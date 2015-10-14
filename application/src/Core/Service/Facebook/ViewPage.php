<?php
/**
 * Author: Alex P.
 * Date: 28.04.14
 * Time: 18:00
 */

namespace Core\Service\Facebook;


use Core\Service\Theme\Html;

/**
 * Class ViewPage
 * @package Core\Service\Facebook
 */
class ViewPage
{
    /**
     * @var \Core\Service\Theme\Html
     */
    protected $htmlService;

    /**
     * @var \Facebook
     */
    protected $facebookApi;

    /**
     * @var \ThemeTemplate
     */
    protected $template;

    /**
     * @var \ThemeLayout
     */
    protected $layout;

    /**
     * @var \Theme
     */
    protected $theme;

    /**
     * @var \ThemeUserData
     */
    protected $dataModel;

    /**
     * @param Html $html
     * @param \Facebook $api
     */
    public function __construct(Html $html, \Facebook $api)
    {
        $this->htmlService = $html;
        $this->facebookApi = $api;
    }

    /**
     * Return html code filled with user data
     * @return string
     */
    public function getHtml()
    {
        $userId = $this->getOwnerId();
        $templateName = $this->getTemplate()->name;
        $layoutName = $this->getLayout()->name;
        $themeName = $this->getTheme()->name;
        $signedRequest = $this->getSignedRequest();

        $this->htmlService->setComponentOptionByNodeId('likeGate', 'signed_request', $signedRequest);

        if (isset($signedRequest['page']['id'])) {
            return $this->htmlService->getView(
                $userId,
                $signedRequest['page']['id'],
                $themeName,
                $layoutName,
                $templateName
            );
        }
        return null;
    }

    /**
     * Get requested theme
     * @return mixed
     */
    public function getTheme()
    {
        if (!$this->theme) {
            $this->theme = $this->getLayout()->theme->get();
        }

        return $this->theme;
    }

    /**
     * Get requested layout
     * @return mixed
     */
    public function getLayout()
    {
        if (!$this->layout) {
            $this->layout = $this->getTemplate()->layout->get();
        }

        return $this->layout;
    }

    /**
     * Get requested template
     * @return object
     */
    public function getTemplate()
    {
        if (!$this->template) {
            $this->template = \ThemeTemplate::instance_factory(
                $this->getFirstMatchedUserData()->template_id
            );
        }
        return $this->template;
    }

    /**
     * Get user id
     * @return mixed
     */
    protected function getOwnerId()
    {
        return $this->getFirstMatchedUserData()->user_id;
    }

    /**
     * Creates empty data model
     * @return \ThemeUserData
     */
    protected function getUserdataModel()
    {
        return new \ThemeUserData();
    }

    /**
     * Find first matched record (by tab id)
     * @return \ThemeUserData
     */
    protected function getFirstMatchedUserData()
    {
        if (!$this->dataModel) {
            $signedRequest = $this->getSignedRequest();
            $this->dataModel = $this->getUserdataModel()
                ->getFilter()
                ->tab_id('=', $signedRequest['page']['id'])
                /* TODO: implement
                 * ->template_active('=', 1)
                ->layout_active('=', 1)
                ->theme_active('=', 1)*/
                ->apply(1);
        }

        return $this->dataModel;
    }

    /**
     * Get decoded facebook signed request
     * @return array
     */
    protected function getSignedRequest()
    {
        return array('page' => array('id' => '467559170011758', 'liked' => true, 'admin' => true));
       // return $this->facebookApi->getSignedRequest();
    }
} 