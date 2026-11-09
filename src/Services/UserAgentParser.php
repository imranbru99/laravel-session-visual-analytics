<?php

namespace Imranbru99\SessionVisualAnalytics\Services;

class UserAgentParser
{
    public function parse(?string $userAgent): array
    {
        $ua = $userAgent ?: '';

        return [
            'device_type' => $this->deviceType($ua),
            'browser' => $this->browser($ua),
            'platform' => $this->platform($ua),
        ];
    }

    protected function deviceType(string $ua): string
    {
        $uaLower = strtolower($ua);

        if ($uaLower === '' ) {
            return 'unknown';
        }

        if (preg_match('/bot|crawl|spider|slurp|facebookexternalhit/i', $ua)) {
            return 'bot';
        }

        if (preg_match('/ipad|tablet|kindle|silk|(android(?!.*mobile))/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/mobi|iphone|ipod|android.*mobile|windows phone/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    protected function browser(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') || str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'Chrome/') && ! str_contains($ua, 'Edg/') => 'Chrome',
            str_contains($ua, 'Safari/') && ! str_contains($ua, 'Chrome/') => 'Safari',
            str_contains($ua, 'Firefox/') => 'Firefox',
            default => 'Other',
        };
    }

    protected function platform(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Other',
        };
    }
}
