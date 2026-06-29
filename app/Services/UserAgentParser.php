<?php

namespace App\Services;

use Jenssegers\Agent\Agent;

class UserAgentParser
{
    public function parse(string $userAgent): Agent|array
    {
        return new Agent(userAgent: $userAgent);
    }
}