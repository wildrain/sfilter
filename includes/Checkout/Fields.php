<?php

namespace SFilter\Checkout;

class Fields
{
    /**
     * Get all checkout fields
     *
     * @return array
     */
    public static function get_fields()
    {
        return [
            'sf_full_name' => [
                'type'        => 'text',
                'label'       => __('Full Name', 'sfilter'),
                'required'    => true,
                'placeholder' => __('Enter your full name', 'sfilter'),
                'priority'    => 10,
            ],
            'sf_email' => [
                'type'        => 'email',
                'label'       => __('Email', 'sfilter'),
                'required'    => true,
                'placeholder' => __('Enter your email address', 'sfilter'),
                'priority'    => 20,
            ],
            'sf_address' => [
                'type'        => 'textarea',
                'label'       => __('Address', 'sfilter'),
                'required'    => true,
                'placeholder' => __('Enter your address', 'sfilter'),
                'priority'    => 30,
            ],
            'sf_phone' => [
                'type'        => 'tel',
                'label'       => __('Phone Number', 'sfilter'),
                'required'    => true,
                'placeholder' => __('Enter your phone number', 'sfilter'),
                'priority'    => 40,
            ],
            'sf_company' => [
                'type'        => 'text',
                'label'       => __('Company Name', 'sfilter'),
                'required'    => false,
                'placeholder' => __('Enter your company name (optional)', 'sfilter'),
                'priority'    => 50,
            ],
            'sf_region' => [
                'type'        => 'select',
                'label'       => __('Region', 'sfilter'),
                'required'    => true,
                'options'     => self::get_regions(),
                'priority'    => 60,
            ],
        ];
    }

    /**
     * Get region options
     *
     * @return array
     */
    public static function get_regions()
    {
        return [
            ''         => __('Select a region', 'sfilter'),
            'eastern'  => __('Eastern Province', 'sfilter'),
            'central'  => __('Central Province', 'sfilter'),
            'western'  => __('Western Province', 'sfilter'),
        ];
    }

    /**
     * Validate checkout fields
     *
     * @param array $data Posted data
     * @return array Array with 'valid' boolean and 'errors' array
     */
    public static function validate($data)
    {
        $errors = [];
        $fields = self::get_fields();

        foreach ($fields as $key => $field) {
            $value = isset($data[$key]) ? trim($data[$key]) : '';

            if ($field['required'] && empty($value)) {
                $errors[$key] = sprintf(
                    __('%s is required.', 'sfilter'),
                    $field['label']
                );
                continue;
            }

            if (!empty($value)) {
                switch ($field['type']) {
                    case 'email':
                        if (!is_email($value)) {
                            $errors[$key] = __('Please enter a valid email address.', 'sfilter');
                        }
                        break;

                    case 'tel':
                        if (!preg_match('/^[\+]?[0-9\s\-\(\)]{7,20}$/', $value)) {
                            $errors[$key] = __('Please enter a valid phone number.', 'sfilter');
                        }
                        break;

                    case 'select':
                        $valid_options = array_keys($field['options']);
                        if (!in_array($value, $valid_options) || empty($value)) {
                            $errors[$key] = sprintf(
                                __('Please select a valid %s.', 'sfilter'),
                                strtolower($field['label'])
                            );
                        }
                        break;
                }
            }
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Sanitize field value
     *
     * @param string $key Field key
     * @param mixed $value Field value
     * @return mixed Sanitized value
     */
    public static function sanitize($key, $value)
    {
        $fields = self::get_fields();

        if (!isset($fields[$key])) {
            return sanitize_text_field($value);
        }

        $field = $fields[$key];

        switch ($field['type']) {
            case 'email':
                return sanitize_email($value);

            case 'textarea':
                return sanitize_textarea_field($value);

            case 'select':
                $valid_options = array_keys($field['options']);
                return in_array($value, $valid_options) ? $value : '';

            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Get sanitized data from POST
     *
     * @return array
     */
    public static function get_sanitized_data()
    {
        $data = [];
        $fields = self::get_fields();

        foreach (array_keys($fields) as $key) {
            $value = isset($_POST[$key]) ? $_POST[$key] : '';
            $data[$key] = self::sanitize($key, $value);
        }

        return $data;
    }
}
