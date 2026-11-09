<?php

namespace Imranbru99\SessionVisualAnalytics\Tests;

use Imranbru99\SessionVisualAnalytics\Services\UserAgentParser;
use PHPUnit\Framework\TestCase;

class UserAgentParserTest extends TestCase
{
    public function test_it_detects_mobile_chrome_on_android(): void
    {
        $parser = new UserAgentParser();
        $result = $parser->parse('Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36');

        $this->assertSame('mobile', $result['device_type']);
        $this->assertSame('Chrome', $result['browser']);
        $this->assertSame('Android', $result['platform']);
    }

    public function test_it_detects_desktop_safari_on_macos(): void
    {
        $parser = new UserAgentParser();
        $result = $parser->parse('Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15');

        $this->assertSame('desktop', $result['device_type']);
        $this->assertSame('Safari', $result['browser']);
        $this->assertSame('macOS', $result['platform']);
    }
}
