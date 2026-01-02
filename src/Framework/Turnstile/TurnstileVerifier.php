<?php
namespace Framework\Turnstile;

use Framework\Contracts\TurnstileContract;

class TurnstileVerifier
{
    public function __construct(
        private TurnstileContract $turnstile,
        private string $secret,
        private string $endpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
    ) {}

    public function verify(?string $token, ?string $remoteIp = null): array
    {
        if (!$token) {
            return ['ok' => false, 'errors' => ['missing-input-response']];
        }

        $payload = ['secret' => $this->secret, 'response' => $token];
        if ($remoteIp) $payload['remoteip'] = $remoteIp;

        $res = $this->turnstile->post($this->endpoint, $payload);
        if ($res['status'] !== 200) {
            return ['ok' => false, 'errors' => ['http-'.$res['status']]];
        }

        $data = json_decode($res['body'], true);
        if (!is_array($data)) {
            return ['ok' => false, 'errors' => ['bad-json']];
        }

        $ok = (bool)($data['success'] ?? false);
        return [
            'ok'     => $ok,
            'errors' => $ok ? [] : (array)($data['error-codes'] ?? ['unknown-error']),
            'host'   => $data['hostname'] ?? null,
            'ts'     => $data['challenge_ts'] ?? null,
            'raw'    => $data,
        ];
    }
}
