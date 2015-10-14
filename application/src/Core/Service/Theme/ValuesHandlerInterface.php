<?php
/**
 * Author: Alex P.
 * Date: 08.04.14
 * Time: 15:39
 */

namespace Core\Service\Theme;


/**
 * Interface ValuesHandlerInterface
 * @package Core\Service\Theme
 */
interface ValuesHandlerInterface
{
    /**
     * @param string $type
     * @param mixed $value
     * @return string
     */
    public function input($type, $value);

    /**
     * @param string $type
     * @param string $value
     * @return mixed
     */
    public function output($type, $value);
} 