<?php
/**
 * Author: Alex P.
 * Date: 08.04.14
 * Time: 14:33
 */

namespace Core\Service\Theme\Storage;

use Core\Service\Theme\ValuesHandlerInterface;
use Ikantam\Theme\Exception;

/**
 * Class DbUserDataStorage
 * @package Core\Service\Theme\Storage
 */
class DbUserDataStorage implements \Ikantam\Theme\Interfaces\UserDataStorageInterface
{
    /**
     * @var \Core\Service\Theme\ValuesHandlerInterface
     */
    protected $valuesHandler;

    /**
     * @var string
     */
    protected $tabId;

    /**
     * @param ValuesHandlerInterface $valuesHandler
     */
    public function __construct(ValuesHandlerInterface $valuesHandler)
    {
        $this->valuesHandler = $valuesHandler;
    }

    /**
     * Set FB tab id to relate template with concrete tab
     * @param string $tabId
     */
    public function setTabId($tabId)
    {
        $this->tabId = $tabId;
    }

    /**
     * Save user data in storage
     *
     * @param int $userId
     * @param int $templateId
     * @param string $componentId
     * @param mixed $componentValue
     * @throws \Exception
     * @return mixed
     */
    public function save($userId, $templateId, $componentId, $componentValue)
    {
        if (!$this->tabId) {
            throw new \Exception('Tab id is empty. Use the setTabId method before.');
        }
        $dataModel = $this->createUserDataModel();

        // overwrite if exist
        $dataModel->getFilter()
            ->template_id('=', $templateId)
            ->node_identity('=', $componentId)
            ->tab_id('=', $this->tabId)
            ->apply(1);

        $dataModel->user_id = $userId;
        $dataModel->template_id = $templateId;
        $dataModel->node_identity = $this->valuesHandler->input('string', $componentId);
        $dataModel->value_type = gettype($componentValue);
        $dataModel->value = $this->valuesHandler->input(gettype($componentValue), $componentValue);
        $dataModel->tab_id = $this->valuesHandler->input('string', $this->tabId);
        
        return $dataModel->save();
    }

    /**
     * Retrieve user data from storage
     *
     * @param $userId
     * @param $templateId
     * @param $componentId
     * @return mixed
     */
    public function retrieveComponentValue($userId, $templateId, $componentId)
    {
        if (!$this->tabId) {
            throw new \Exception('Tab id is empty. Use the setTabId method before.');
        }
        $dataModel = $this->createUserDataModel();

        $dataModel->getFilter()
            ->user_id('=', $userId)
            ->template_id('=', $templateId)
            ->tab_id('=', $this->tabId)
            ->node_identity('=', $componentId)
            ->apply(1);

        $valueType = $dataModel->value_type;
        if (!$dataModel->exists()) {
            $valueType = 'string';
        }

        return $this->valuesHandler->output($valueType, $dataModel->value);
    }

    /**
     * Create new data model
     *
     * @return \ThemeUserData
     */
    protected function createUserDataModel()
    {
        return new \ThemeUserData();
    }

}
