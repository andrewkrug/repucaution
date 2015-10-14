<?php
/**
 * User: alkuk
 * Date: 14.04.14
 * Time: 14:33
 */

namespace Core\Utils\Collection;

class SimpleArrayCollection
{
    /**
     * @var array
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Add new data to collections
     *
     * @param array $data
     */
    public function add(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Check is key exists
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get value by key if exists, or return default value
     *
     * @param $key
     * @param null $defaultValue
     *
     * @return null
     */
    public function get($key, $defaultValue = null)
    {
        if (!$this->has($key)) {
            return $defaultValue;
        }

        return $this->data[$key];
    }

    /**
     * Check if collection has all keys
     *
     * @param array $keys
     *
     * @return bool
     */
    public function hasAll(array $keys)
    {
        foreach ($keys as $key) {
           if (!$this->has($key)) {
               return false;
           }
        }

        return true;
    }

}
 