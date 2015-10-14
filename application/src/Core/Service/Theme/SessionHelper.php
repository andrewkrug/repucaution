<?php
/**
 * Author: Alex P.
 * Date: 17.04.14
 * Time: 12:42
 */

namespace Core\Service\Theme;


/**
 * Class SessionHelper
 * @package Core\Service\Theme
 */
/**
 * Class SessionHelper
 * @package Core\Service\Theme
 */
/**
 * Class SessionHelper
 * @package Core\Service\Theme
 */
/**
 * Class SessionHelper
 * @package Core\Service\Theme
 */
class SessionHelper
{
    /**
     * @var \CI_Session
     */
    protected $session;

    /**
     * @var ValuesHandlerDb
     */
    protected $valuesHandler;

    /**
     * @var array
     */
    protected $templateImages;
    /**
     * @var array
     */
    protected $sessionKeys = array(
        'tab' => 'fb_tab_id',
        'tab_id' => 'fb_tab_id',
        'template_name' => 'theme_template_name',
        'template_id' => 'theme_template_id',
        'layout_name' => 'theme_layout_name',
        'layout_id' => 'theme_layout_id',
        'theme_name' => 'theme_theme_name',
        'theme_id' => 'theme_theme_id',
        'template_images' => 'theme_template_images'
    );

    /**
     * @param \CI $application
     * @param ValuesHandlerDb $valuesHandler
     */
    public function __construct(\CI $application, ValuesHandlerDb $valuesHandler)
    {
        $this->session = $application->session;
        $this->valuesHandler = $valuesHandler;
    }

    /**
     * @return string
     */
    public function getTabId()
    {
        $tabId = $this->session->userdata($this->sessionKeys['tab']);
        if (!$tabId) {
            $tabId = 'no_tab';
        }
        return $tabId;
    }

    /**
     * @param $tabId
     * @return $this
     */
    public function setTabId($tabId)
    {
        $this->session->set_userdata($this->sessionKeys['tab'], $tabId);
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setThemeName($name)
    {
        $this->session->set_userdata($this->sessionKeys['theme_name'], $name);
        return $this;
    }

    /**
     * @return null
     */
    public function getThemeName()
    {
        $name = $this->session->userdata($this->sessionKeys['theme_name']);
        return $name ?: null;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setThemeId($id)
    {
        $this->session->set_userdata($this->sessionKeys['theme_id'], $id);
        return $this;
    }

    /**
     * @return null
     */
    public function getThemeId()
    {
        $id = $this->session->userdata($this->sessionKeys['theme_id']);
        return $id ?: null;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setLayoutName($name)
    {
        $this->session->set_userdata($this->sessionKeys['layout_name'], $name);
        return $this;
    }

    /**
     * @return null
     */
    public function getLayoutName()
    {
        $name = $this->session->userdata($this->sessionKeys['layout_name']);
        return $name ?: null;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setLayoutId($id)
    {
        $this->session->set_userdata($this->sessionKeys['layout_id'], $id);
        return $this;
    }

    /**
     * @return null
     */
    public function getLayoutId()
    {
        $id = $this->session->userdata($this->sessionKeys['layout_id']);
        return $id ?: null;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setTemplateName($name)
    {
        $this->session->set_userdata($this->sessionKeys['template_name'], $name);
        return $this;
    }

    /**
     * @return null
     */
    public function getTemplateName()
    {
        $name = $this->session->userdata($this->sessionKeys['template_name']);
        return $name ?: null;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setTemplateId($id)
    {
        $this->session->set_userdata($this->sessionKeys['template_id'], $id);
        return $this;
    }

    /**
     * @return null
     */
    public function getTemplateId()
    {
        $id = $this->session->userdata($this->sessionKeys['template_id']);
        return $id ?: null;
    }

    /**
     * Retrieve all related data
     * @return array
     */
    public function getAll()
    {
        $result = array();
        foreach ($this->sessionKeys as $key => $sessKey)
        {
            $value = $this->session->userdata($sessKey);
            if ($value) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Add image info related with component id
     * @param string $componentId
     * @param array $data - image info
     * @param string $mark - to mark image as special (original used to edit crop params)
     * @return $this
     */
    public function addTemplateImage($componentId, array $data, $mark = 'original')
    {
        $images = $this->getTemplateImages();
        $images[$componentId][$mark] = $data;
        $this->setTemplateImages($images);
        return $this;
    }

    /**
     * Remove image info
     * @param string $componentId
     * @param string $mark - optional (remove all image paths if by component id if not passed)
     * @return $this
     */
    public function removeTemplateImage($componentId, $mark = null)
    {
        $images = $this->getTemplateImages();
        if ($mark = (string)$mark) {
            unset($images[$componentId][$mark]);
        } else {
            unset($mark[$componentId]);
        }
        $this->setTemplateImages($images);
        return $this;
    }

    /**
     * Retrieve image info by component id
     * @param string $componentId
     * @param null $mark
     * @return mixed
     */
    public function getTemplateImage($componentId, $mark = null)
    {
        $images = $this->getTemplateImages();
        if ($mark = (string)$mark) {
            return isset($images[$componentId][$mark]) ? $images[$componentId][$mark] : null;
        }
        return isset($images[$componentId]) ? $images[$componentId] : null;
    }

    /**
     * Return all images
     * @return array|mixed
     */
    public function getTemplateImages()
    {
        if (!$this->templateImages) {
            $imagesEncoded = $this->session->userdata($this->sessionKeys['template_images']);
            if (!$imagesEncoded) {
                return array();
            } else {
                $this->templateImages = $this->valuesHandler->output( 'array', $imagesEncoded);
            }
        }
        return $this->templateImages;
    }


    /**
     * Stores data
     * Save in session any "themes" data such as template, theme, layout
     * or directly: ['tab_id' => 5, 'theme_id' => 3, ...]
     * @param mixed $data,...
     * @return $this
     */
    public function store($data)
    {
        $data = func_get_args();
        foreach ($data as $item) {
            if (is_object($item)) {
                $this->storeObject($item);
            } elseif(is_array($item)) {
                foreach($item as $key => $value) {
                    if (array_key_exists($key, $this->sessionKeys)) {
                        $this->session->set_userdata($this->sessionKeys[$key], $value);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Clear session
     * @return $this
     */
    public function removeAll()
    {
        $keys = array_values($this->sessionKeys);
        $this->session->unset_userdata(array_combine($keys, $keys));
        return $this;
    }

    /**
     * Encode array of image info to string and save in session
     * @param array $images
     * @return $this
     */
    protected function setTemplateImages(array $images)
    {
        $this->templateImages = $images;
        $images = $this->valuesHandler->input('array', $images);
        $this->session->set_userdata($this->sessionKeys['template_images'], $images);
        $this->session->set_userdata('theme_images_deletion_queue', $images); // to delete extra files
        return $this;
    }



    /**
     * Stores object data
     * @param $object
     * @return bool
     */
    protected function storeObject($object)
    {
        if (!is_object($object) || !(method_exists($object, 'exists') && $object->exists())) {
            return false;
        }
        switch(get_class($object)) {
            case 'Theme':
                $this->setThemeName($object->name);
                $this->setThemeId($object->id);
                break;
            case 'ThemeLayout':
                $this->setLayoutName($object->name);
                $this->setLayoutId($object->id);
                $this->storeObject($object->getTheme());
                break;
            case 'ThemeTemplate':
                $this->setTemplateName($object->name);
                $this->setTemplateId($object->id);
                $this->storeObject($object->getLayout());
                break;
        }

        return true;
    }

} 