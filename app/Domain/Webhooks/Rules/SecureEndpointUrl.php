<?php

namespace App\Domain\Webhooks\Rules;

use App\Domain\Webhooks\Services\EndpointSecurityValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Form request rule wrapping the EndpointSecurityValidator service,
 * so URL security is enforced declaratively during validation (spec §31).
 */
class SecureEndpointUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $error = app(EndpointSecurityValidator::class)->validateUrl((string) $value);

        if ($error !== null) {
            $fail($error);
        }
    }
}
