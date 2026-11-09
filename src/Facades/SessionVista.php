<?php

namespace Imranbru99\SessionVisualAnalytics\Facades;

use Illuminate\Support\Facades\Facade;
use Imranbru99\SessionVisualAnalytics\Services\SessionAnalyticsService;

/**
 * @method static array overviewMetrics()
 * @method static array chartSeries(int $hours = 24)
 * @method static array deviceMix()
 * @method static array browserMix()
 * @method static array topPages(int $limit = 10)
 * @method static array topCountries(int $limit = 8)
 * @method static string formatDuration(int $seconds)
 *
 * @see SessionAnalyticsService
 */
class SessionVista extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SessionAnalyticsService::class;
    }
}
