<?php

namespace Softelebyte\GoogleAnalytics;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;

class GoogleAnalyticsClientFactory
{
    public function build(): BetaAnalyticsDataClient
    {
        return new BetaAnalyticsDataClient([
            'credentials' => $this->credentialsFile(),
        ]);
    }

    private function credentialsFile(): string
    {
        $fileName = config('services.google_analytics.credentials');

        return storage_path('app/'.$fileName);
    }
}
