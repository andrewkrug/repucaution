<?php
/**
 * Created by PhpStorm.
 * User: FleX
 * Date: 09.04.14
 * Time: 13:13
 */

namespace Core\Service\Theme;


class DbUserData implements UserDataInterface
{

    protected $data = array();

    protected $valuesHandler;

    public function __construct(ValuesHandlerInterface $valuesHandler)
    {
        $this->valuesHandler = $valuesHandler;
    }

    /**
     * @param mixed $userId
     * @param string $tabId
     * @param string $themeName
     * @param string $layoutName
     * @param string $templateName
     * @param string $componentId
     * @param mixed $options - optional. any additional data
     * @return mixed
     */
    public function retrieveComponentValue(
        $userId,
        $tabId,
        $themeName,
        $layoutName,
        $templateName,
        $componentId,
        $options = null
    ) {
        $key = crc32($userId . $themeName . $layoutName . $templateName);
        if (!isset($this->data[$key])) {
            $userDataModel = $this->createUserDataModel()
                ->getFilter()
                ->user_id('=', $userId)
                ->tab_id('=', $tabId)
                ->theme_name('=', $themeName)
                ->layout_name('=', $layoutName)
                ->template_name('=', $templateName)
                ->apply();

            $this->data[$key] = $userDataModel;
        }

        foreach ($this->data[$key] as $data) {
            if ($data->node_identity === $componentId) {
                return $this->valuesHandler->output($data->value_type, $data->value);
            }
        }

        return null;
    }


    protected function createUserDataModel()
    {
        return new \ThemeUserData();
    }
}
