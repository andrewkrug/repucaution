<?php
/**
 * Author: Alex P.
 * Date: 08.04.14
 * Time: 19:53
 */

namespace Core\Service\Theme;

/**
 * Adapter between storage
 * Interface UserDataAdapterInterface
 * @package Core\Service\Theme\Storage
 */
interface UserDataInterface
{
    /**
     * @param mixed $userId
     * @param string $tabId
     * @param string $themeName
     * @param string $layoutName
     * @param string $templateName
     * @param string $componentId - tag attribute id
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
    );
}

