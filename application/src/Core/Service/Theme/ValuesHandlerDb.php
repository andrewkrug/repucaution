<?php
/**
 * Author: Alex P.
 * Date: 08.04.14
 * Time: 15:43
 */

namespace Core\Service\Theme;


class ValuesHandlerDb implements ValuesHandlerInterface
{
    const TYPE_ARRAY = 'array';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';

    private $types = array(
        self::TYPE_ARRAY,
        self::TYPE_NUMBER =>  array('integer', 'float'),
        self::TYPE_STRING
    );

    /**
     * @param string $type
     * @param mixed $value
     * @throws \Exception
     * @return string
     */
    public function input($type, $value)
    {
        if (!$this->isValidType($type)) {
            throw new \Exception('Type is not supported');
        }

        switch($type) {
            case self::TYPE_ARRAY:
                    if (!is_array($value)) {
                        throw new \Exception('Array type chosen but ' . gettype($value) . ' value given');
                    }
                $value = base64_encode(serialize($value));
                break;
            case self::TYPE_STRING:
                $value = trim(strip_tags($value));
                if (empty($value)) {
                    $value = ' '; //!
                }
                $value = str_replace(chr(194), '', $value); //sceditor insert this symbols
                break;
            case self::TYPE_NUMBER:
                $value *= 1;
                break;
        }

        return $value;
    }

    /**
     * @param string $type
     * @param string $value
     * @throws \Exception
     * @return mixed
     */
    public function output($type, $value)
    {
        if (!$this->isValidType($type)) {
            throw new \Exception('Type is not supported');
        }
        switch($type) {
            case self::TYPE_ARRAY:
                $value = base64_decode($value);
                if (!$value || (!$value = @unserialize($value))) {
                    throw new \Exception('Conversion fails. Can not decode value.');
                }
                break;
            case self::TYPE_STRING:
                $value = trim((string) $value);
                break;
            case self::TYPE_NUMBER:
                $value *= 1;
                break;
        }

        return $value;
    }

    /**
     * Checks if type is acceptable
     * @param string $type
     * @return bool
     */
    protected function isValidType($type)
    {
        $isValid = false;
        foreach ($this->types as $value) {
            if (!is_array($value)) {
                if ($value === $type) {
                    $isValid = true;
                }
            } else {
                if (in_array($type, $value)) {
                    $isValid = true;
                }
            }
        }
        return $isValid;
    }
}
