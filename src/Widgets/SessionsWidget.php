<?php

namespace BezhanSalleh\FilamentGoogleAnalytics\Widgets;

use Illuminate\Support\Arr;
use Filament\Widgets\Widget;
use BezhanSalleh\FilamentGoogleAnalytics\Traits;
use BezhanSalleh\FilamentGoogleAnalytics\FilamentGoogleAnalytics;

class SessionsWidget extends Widget
{
    use Traits\Sessions;
    use Traits\CanViewWidget;

    protected static string $view = 'filament-google-analytics::widgets.sessions-widget';

    protected static ?int $sort = 3;

    public ?string $filter = 'T';

    public $readyToLoad = false;

    public function init()
    {
        $this->readyToLoad = true;
    }

    public function label(): ?string
    {
        return 'Sessions';
    }

    protected static function filters(): array
    {
        return [
            'T' => 'Today',
            'Y' => 'Yesterday',
            'LW' => 'Last Week',
            'LM' => 'Last Month',
            'LSD' => 'Last 7 Days',
            'LTD' => 'Last 30 Days',
        ];
    }

    protected function initializeData()
    {
        $lookups = [
            'T' => $this->sessionsToday(),
            'Y' => $this->sessionsYesterday(),
            'LW' => $this->sessionsLastWeek(),
            'LM' => $this->sessionsLastMonth(),
            'LSD' => $this->sessionsLastSevenDays(),
            'LTD' => $this->sessionsLastThirtyDays(),
        ];

        $data = Arr::get(
            $lookups,
            $this->filter,
            [
                'result' => 0,
                'previous' => 0,
            ],
        );

        return FilamentGoogleAnalytics::for($data['result'])
            ->previous($data['previous'])
            ->format('%');
    }

    protected function getData(): array
    {
        return [
            'value' => $this->initializeData()->trajectoryValue(),
            'icon' => $this->initializeData()->trajectoryIcon(),
            'color' => $this->initializeData()->trajectoryColor(),
            'description' => $this->initializeData()->trajectoryDescription(),
            'chart' => [],
            'chartColor' => '',
        ];
    }

    protected function getViewData(): array
    {
        return [
            'data' => $this->readyToLoad ? $this->getData() : [],
            'filters' => static::filters()
        ];
    }
}