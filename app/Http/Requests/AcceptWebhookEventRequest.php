<?php

namespace App\Http\Requests;

use App\Domain\Webhooks\Enums\WebhookErrorCode;
use App\Domain\Webhooks\Rules\SecureEndpointUrl;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

/**
 * Validation for POST /api/internal/v1/webhook-events (spec §31).
 * Renders failures in the §9.6 error envelope.
 */
class AcceptWebhookEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authentication is handled by the AuthenticateInternalClient middleware
        return true;
    }

    public function rules(): array
    {
        return [
            /****** Source ******/
            'source' => 'required|array',
            'source.system' => 'required|string|max:50',
            'source.instance_id' => 'required|string|max:50',
            'source.event_id' => 'required|string|max:100',
            'source.occurred_at' => 'required|date',

            /****** Tenant and merchant ******/
            'tenant' => 'required|array',
            'tenant.id' => 'required|string|max:50',
            'tenant.code' => 'nullable|string|max:50',
            'merchant' => 'required|array',
            'merchant.id' => 'required|string|max:50',
            'merchant.name' => 'nullable|string|max:100',

            /****** Event ******/
            'event' => 'required|array',
            'event.type' => ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)*$/'],
            'event.api_version' => 'required|string|max:20',
            'event.aggregate_type' => 'required|string|max:30',
            'event.aggregate_id' => 'required|string|max:100',
            'event.resource_version' => 'nullable|integer|min:0',

            /****** Endpoint ******/
            'endpoint' => 'required|array',
            'endpoint.legacy_webhook_id' => 'required|string|max:50',
            'endpoint.name' => 'required|string|max:100',
            'endpoint.url' => ['required', 'string', 'url', 'max:2048', new SecureEndpointUrl()],
            'endpoint.signing_secret' => 'required|string|max:255',
            'endpoint.timeout_seconds' => ['nullable', 'integer', 'min:3', 'max:' . config('webhook.max_timeout', 30)],
            'endpoint.max_attempts' => ['nullable', 'integer', 'min:1', 'max:' . config('webhook.default_max_attempts', 8)],
            'endpoint.status' => 'required|in:active,paused,disabled',

            /****** Alert configuration ******/
            'alert' => 'nullable|array',
            'alert.emails' => 'nullable|array|max:10',
            'alert.emails.*' => 'required|email|max:191',
            'alert.notify_after_attempt' => 'nullable|integer|min:1',
            'alert.notify_on_permanent_failure' => 'nullable|boolean',
            'alert.notify_on_recovery' => 'nullable|boolean',

            /****** Payload (raw byte size already enforced by the auth middleware) ******/
            'payload' => 'required|array',
        ];
    }

    /**
     * Render validation failures in the spec §9.6 envelope.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => [
                'code' => WebhookErrorCode::VALIDATION_ERROR->name,
                'message' => 'The webhook event request is invalid.',
                'fields' => $validator->errors(),
            ],
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
