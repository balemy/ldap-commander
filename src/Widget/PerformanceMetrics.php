<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Widget;

use Balemy\LdapCommander\Timer;
use Yiisoft\Widget\Widget;

final class PerformanceMetrics extends Widget
{
    public function __construct(private Timer $timer)
    {
    }

    protected function run(): string
    {
        $time = round($this->timer->get('overall'), 3);
        $memory = round(memory_get_peak_usage() / (1024 * 1024), 2);

        $out = "Total Time: $time sec";
        if ($this->timer->has('schema')) {
            $out .= ' &middot; Schema Parsing: ' . round($this->timer->get('schema'), 3) . ' sec';
        }

        return $out . " &middot; Memory Usage: $memory MB";
    }
}
