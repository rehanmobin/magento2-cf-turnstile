<?php
/**
 * @category  Mage4
 * @package   Mage4_Turnstile
 * @author    Rehan Mobin <m.rehan.mobin@gmail.com>
 * @copyright Copyright (c) Mage4. All rights reserved. (https://www.mage4.com)
 */

namespace Mage4\Turnstile\Model\Turnstile;

final class SiteverifyResponse
{

    private bool $success;
    private ?string $errorCode;
    private ?string $errorMessage;
    private ?string $action;
    private ?string $hostname;

    private function __construct(bool $success, ?string $errorCode = null, ?string $errorMessage = null, ?string $action = null, ?string $hostname = null)
    {
        $this->success = $success;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->hostname = $hostname;
        $this->action = $action;
    }
    public static function createFromResponse(array $data): self
    {
        if ($data['success'] === false) {
            return self::createFromError($data);
        }
        return self::createFromSuccess($data);
    }

    private static function createFromError(array $data): self
    {
        return new self(false, $data['error-codes'][0], self::getErrorMessageByCode($data['error-codes'][0]));
    }

    private static function createFromSuccess(array $data): self
    {
        return new self(true, null, null, $data['action'], $data['hostname']);
    }

    public function isFailure(): bool
    {
        return $this->success === false;
    }

    public function errorCode(): ?string
    {
        return $this->errorCode;
    }

    public function errorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function hostname(): ?string
    {
        return $this->hostname;
    }

    public function action(): ?string
    {
        return $this->action;
    }

    private static function getErrorMessageByCode(string $code): string
    {
        $messages = [
            'timeout-or-duplicate'   => 'the response parameter has already been validated before.',
            'missing-input-secret'   => 'the secret parameter was not passed.',
            'x-missing-secret-key'   => 'unable to validate the form, the secret key is missing.',
            'bad-request'            => 'the request was rejected because it was malformed.',
            'x-missing-response'     => 'please validate the security field.',
            'x-unavailable'          => 'unable to contact Cloudflare to validate the form.',
            'internal-error'         => 'an internal error happened while validating the response.',
            'invalid-input-secret'   => 'the secret parameter was invalid or did not exist.',
            'missing-input-response' => 'the response parameter was not passed.',
            'invalid-input-response' => 'the response parameter is invalid or has expired.',
        ];

        return $messages[$code] ?? 'unknown error.';
    }
}
