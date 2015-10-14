<?php

use Core\Utils\Form\FormFieldInfo;

/**
 * User: alkuk
 * Date: 01.06.14
 * Time: 20:11
 */
class Payment_gateways extends DataMapper
{
    const ACTIVE_STATUS = 1;

    public $validation = array(
        'data' => array(
            'rules' => array(
                'requiredFieldValidate',
            )
        )
    );

    /**
     * Get all gateways which active
     *
     * @return DataMapper
     */
    public static function findAllActive()
    {
        return static::create()->where('status', static::ACTIVE_STATUS)->get();
    }

    /**
     * Find one active gateway by slug
     *
     * @param string $slug
     *
     * @return DataMapper
     */
    public static function findOneActiveBySlug($slug)
    {
        return static::create()
            ->where('status', static::ACTIVE_STATUS)
            ->where('slug', $slug)
            ->get();
    }

    /**
     * Get required fields for this payment gateway
     *
     * @return array
     */
    public function getDecodedRequiredFields()
    {
        return $this->decodeData($this->required_fields);
    }

    /**
     * Get array of field's information objects
     *
     * @return FormFieldInfo[]
     */
    public function getRequiredFieldsInfo()
    {
        $fieldsData = $this->getDecodedRequiredFields();
        $fieldsInfoArray = array();
        foreach ($fieldsData as $key => $data) {
            $fieldsInfoArray[$key] = new FormFieldInfo($key, $data);
        }

        return $fieldsInfoArray;
    }

    /**
     * Compare slug
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isSlug($slug){
        return $this->slug == $slug;
    }

    /**
     * Get payment gateway data
     *
     * @return array
     */
    public function getDecodedData()
    {

        return $this->decodeData($this->data);
    }

    /**
     * Get one fields from payment gateway data
     *
     * @param $field
     *
     * @return null
     */
    public function getFieldValue($field)
    {
        $gatewayData = $this->getDecodedData();
        if (!isset($gatewayData[$field])) {
            return null;
        }

        return $gatewayData[$field];
    }

    /**
     * handle data
     *
     * @param array $data
     */
    public function handleData(array $data)
    {
        $originalData = $this->getDecodedData();

        if (isset($data['enable'])) {
            $this->status = (int)$data['enable'];
        }

        $requiredFields = $this->getRequiredFields();
        $data = Arr::remove($data, $requiredFields);

        foreach ($data as $key => $value) {
            $data[$key] = trim($value);
        }

        $this->data = json_encode($data);
    }

    /**
     * Get array of required fields without unnecessary info
     *
     * @return array
     */
    protected function getRequiredFields()
    {
        return array_keys($this->getDecodedRequiredFields());
    }

    /**
     * Decode data from json
     *
     * @param $data
     *
     * @return array
     */
    protected function decodeData($data)
    {
        $data = @json_decode($data, true);
        if (!is_array($data)) {
            return array();
        }

        return $data;
    }

    protected function _requiredFieldValidate()
    {
        $requiredFields = $this->getRequiredFields();
        $requiredFieldsInfo = $this->getRequiredFieldsInfo();

        $data = Arr::remove($this->getDecodedData(), $requiredFields);

        if (count($data) != count($requiredFields)) {
            return 'All fields are required!';
        }

        foreach ($data as $key => $value) {
            $fieldInfo = $requiredFieldsInfo[$key];
            if ($fieldInfo->isRequired() && strlen($value) == 0) {
                return sprintf('Field %s is required.', $fieldInfo->GetLabel());
            }
        }

    }
}
