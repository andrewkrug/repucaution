<?php
/**
 * User: alkuk
 * Date: 30.05.14
 * Time: 17:15
 */

class System_setting extends DataMapper
{
    public $table = 'system_settings';

    /**
     * @var bool
     */
    private static $isInstantiated;

    /**
     * {@inheritdoc}
     */
    public function __construct($id = null)
    {
        $trace = debug_backtrace();
        if ((empty($trace[2]['object']) || !$trace[2]['object'] instanceof DataMapper) && self::$isInstantiated) {
            throw new RuntimeException('System_setting can be instantiated only once!');
        }

        if ($this->hasTable($this->prefix.$this->table)) {
            parent::__construct($id);
        }


        self::$isInstantiated = true;
    }

    /**
     * Check is table exists
     *
     * @return bool
     */
    public function hasTable($table = null)
    {
        return $this->db->table_exists(!empty($table) ? $table : $this->table);
    }

    /**
     * Get data from System settings
     *
     * @param string $slug
     *
     * @return string|null
     */
    final public function getData($slug)
    {
        if (!$this->hasTable()) {
            return null;
        }

        $this->clear();
        $this->where('slug', $slug)->get(1);
        if ($this->exists()) {
            return $this->data;
        }

        return null;
    }

    /**
     * Set Data to System settings
     *
     * @param string $slug
     * @param string $data
     */
    final public function setData($slug, $data)
    {
        $this->clear();
        $this->where('slug', $slug)->get(1);
        if (!$this->exists()) {
            $this->slug = $slug;
        }

        $this->data = $data;
        $this->save();
    }
}
