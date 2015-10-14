<?php
/**
 * User: alkuk
 * Date: 23.05.14
 * Time: 14:01
 */

namespace Core\Service\AccessControl;


interface AccessControlInterface
{
    /**
     * Check access
     *
     * @param string $attributes
     * @param mixed $object
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null);

    /**
     * Check access and throw error
     *
     * @param string $attributes
     * @param mixed $object
     *
     * @throw \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function isGrantedWithException($attributes, $object = null);
}
