<?php
/**
 * User: alkuk
 * Date: 04.06.14
 * Time: 19:11
 */

namespace Core\Utils\Form;


class FormFieldInfo
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $data;

    public function __construct($slug, array $data)
    {
        $this->slug = $slug;
        $this->data = $data;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->has('label') ? $this->get('label') : humanize($this->slug);
    }

    /**
     * Check is field required
     *
     * @return bool
     */
    public function isRequired()
    {
        return !empty($this->data['required']);
    }

    /**
     * Check is description provided
     *
     * @return bool
     */
    public function hasDescription()
    {
        return $this->has('description');
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description', '');
    }

    /**
     * Check is key exists
     *
     * @param string $key
     *
     * @return bool
     */
    protected function has($key)
    {
        return !empty($this->data[$key]);
    }

    /**
     * Get value by key
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    protected function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }
}
