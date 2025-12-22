<?php
namespace App\Helper;

class Validator
{
    private $errors = [];
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function make($data, $rules)
    {
        $validator = new self($data);
        return $validator->validate($rules);
    }

    public function validate($rules)
    {
        foreach ($rules as $field => $ruleList) {
            $rulesArray = explode('|', $ruleList);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return $this;
    }

    private function applyRule($field, $rule)
    {
        $value = $this->data[$field] ?? null;

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "$field is required");
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "$field must be a valid email");
                }
                break;

            default:
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $this->addError($field, "$field must be at least $min characters");
                    }
                } elseif (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $this->addError($field, "$field must not exceed $max characters");
                    }
                }
                break;
        }
    }

    private function addError($field, $message)
    {
        $this->errors[$field][] = $message;
    }

    public function fails()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }

    public function first($field)
    {
        return $this->errors[$field][0] ?? null;
    }
}