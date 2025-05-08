<?php

namespace App\Exceptions\Custom;

use Exception;

class BusinessException extends Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * BusinessException constructor.
     *
     * @param string $message
     * @param array $errors
     * @param int $code
     */
    public function __construct(string $message = "", array $errors = [], int $code = 0)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
