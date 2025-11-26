<?php

namespace Oxygen\Core\Validation;

/**
 * OxygenValidator - Request Validation System
 * 
 * Provides a fluent, Laravel-like validation API for validating request data.
 * 
 * @package    Oxygen\Core\Validation
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 * 
 * @example
 * $validator = OxygenValidator::make($request->all(), [
 *     'email' => 'required|email',
 *     'password' => 'required|min:8',
 *     'age' => 'required|numeric|min:18'
 * ]);
 * 
 * if ($validator->fails()) {
 *     $errors = $validator->errors();
 * }
 */
class OxygenValidator
{
    /**
     * Data to validate
     * 
     * @var array
     */
    protected $data = [];

    /**
     * Validation rules
     * 
     * @var array
     */
    protected $rules = [];

    /**
     * Validation errors
     * 
     * @var array
     */
    protected $errors = [];

    /**
     * Custom error messages
     * 
     * @var array
     */
    protected $messages = [];

    /**
     * Constructor
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @param array $messages Custom error messages
     */
    public function __construct($data, $rules, $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
    }

    /**
     * Create a new validator instance
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @param array $messages Custom error messages
     * @return static
     */
    public static function make($data, $rules, $messages = [])
    {
        $validator = new static($data, $rules, $messages);
        $validator->validate();
        return $validator;
    }

    /**
     * Perform the validation
     * 
     * @return void
     */
    protected function validate()
    {
        foreach ($this->rules as $field => $rules) {
            $rulesArray = is_string($rules) ? explode('|', $rules) : $rules;

            foreach ($rulesArray as $rule) {
                $this->validateRule($field, $rule);
            }
        }
    }

    /**
     * Validate a single rule
     * 
     * @param string $field Field name
     * @param string $rule Rule to validate
     * @return void
     */
    protected function validateRule($field, $rule)
    {
        // Parse rule and parameters (e.g., "min:8" -> rule="min", params=["8"])
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

        $value = $this->data[$field] ?? null;

        // Call the appropriate validation method
        $method = 'validate' . ucfirst($ruleName);

        if (method_exists($this, $method)) {
            if (!$this->$method($field, $value, $params)) {
                $this->addError($field, $ruleName, $params);
            }
        }
    }

    /**
     * Validate required field
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $params Parameters
     * @return bool
     */
    protected function validateRequired($field, $value, $params)
    {
        return !empty($value) || $value === '0' || $value === 0;
    }

    /**
     * Validate email format
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $params Parameters
     * @return bool
     */
    protected function validateEmail($field, $value, $params)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate minimum length/value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $params Parameters
     * @return bool
     */
    protected function validateMin($field, $value, $params)
    {
        $min = $params[0] ?? 0;

        if (is_numeric($value)) {
            return $value >= $min;
        }

        return strlen($value) >= $min;
    }

    /**
     * Validate maximum length/value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $params Parameters
     * @return bool
     */
    protected function validateMax($field, $value, $params)
    {
        $max = $params[0] ?? 0;

        if (is_numeric($value)) {
            return $value <= $max;
        }

        return strlen($value) <= $max;
    }

    /**
     * Validate numeric value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $params Parameters
     * @return bool
     */
    protected function validateNumeric($field, $value, $params)
    {
        return is_numeric($value);
    }

    /**
     * Validate string value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $params Parameters
     * @return bool
     */
    protected function validateString($field, $value, $params)
    {
        return is_string($value);
    }

    /**
     * Validate confirmed field (e.g., password_confirmation)
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $params Parameters
     * @return bool
     */
    protected function validateConfirmed($field, $value, $params)
    {
        $confirmField = $field . '_confirmation';
        return isset($this->data[$confirmField]) && $this->data[$confirmField] === $value;
    }

    /**
     * Add a validation error
     * 
     * @param string $field Field name
     * @param string $rule Rule name
     * @param array $params Parameters
     * @return void
     */
    protected function addError($field, $rule, $params)
    {
        $message = $this->getErrorMessage($field, $rule, $params);
        $this->errors[$field][] = $message;
    }

    /**
     * Get error message for a rule
     * 
     * @param string $field Field name
     * @param string $rule Rule name
     * @param array $params Parameters
     * @return string
     */
    protected function getErrorMessage($field, $rule, $params)
    {
        // Check for custom message
        $key = "{$field}.{$rule}";
        if (isset($this->messages[$key])) {
            return $this->messages[$key];
        }

        // Default messages
        $messages = [
            'required' => "The {$field} field is required.",
            'email' => "The {$field} must be a valid email address.",
            'min' => "The {$field} must be at least {$params[0]}.",
            'max' => "The {$field} may not be greater than {$params[0]}.",
            'numeric' => "The {$field} must be a number.",
            'string' => "The {$field} must be a string.",
            'confirmed' => "The {$field} confirmation does not match.",
        ];

        return $messages[$rule] ?? "The {$field} is invalid.";
    }

    /**
     * Check if validation failed
     * 
     * @return bool
     */
    public function fails()
    {
        return !empty($this->errors);
    }

    /**
     * Check if validation passed
     * 
     * @return bool
     */
    public function passes()
    {
        return empty($this->errors);
    }

    /**
     * Get all validation errors
     * 
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Get validated data (only fields that passed validation)
     * 
     * @return array
     */
    public function validated()
    {
        if ($this->fails()) {
            return [];
        }

        $validated = [];
        foreach (array_keys($this->rules) as $field) {
            if (isset($this->data[$field])) {
                $validated[$field] = $this->data[$field];
            }
        }

        return $validated;
    }
}
