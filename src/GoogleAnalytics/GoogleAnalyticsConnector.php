<?php

namespace Softelebyte\GoogleAnalytics;

use Exception;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Protobuf\Internal\RepeatedField;
use Softelebyte\GoogleAnalytics\Exceptions\GoogleAnalyticsException;

class GoogleAnalyticsConnector
{
    private BetaAnalyticsDataClient $client;

    public function __construct(GoogleAnalyticsClientFactory $clientFactory)
    {
        $this->client = $clientFactory->build();
    }

    public function report(GaReportConfig $config): array
    {
        $request = [
            ...[
                'property' => "properties/{$config->property()}",
                'dateRanges' => [$config->dateRange()],
                'metrics' => $config->metrics(),
                'dimensions' => $config->dimensions(),
                'dimensionFilter' => $config->dimensionsFilters(),
                'metricFilter' => $config->metricsFilters(),
            ],
            ...$config->extra()
        ];

        $rows = [];
        $hasMorePages = true;
        $limit = $config->limit();
        $requestsCount = 0;

        do {
            $request['limit'] = $limit;
            $request['offset'] = ($requestsCount * $limit);

            try {
                $response = $this->client->runReport($request);
                $numRows = count($response->getRows());

                $rows = [
                    ...$rows,
                    ...$this->parseRows($response->getRows(), $config->metrics(), $config->dimensions()),
                ];

                $requestsCount++;

                if ($numRows == 0 || $numRows == $response->getRowCount()) {
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                throw new GoogleAnalyticsException($e);
            }
        } while ($hasMorePages);

        return $rows;
    }

    private function parseRows(RepeatedField $rows, array $metrics, array $dimensions): array
    {
        $data = [];

        foreach ($rows as $row) {
            $rowData = [];

            foreach ($row->getMetricValues() as $i => $value) {
                $rowData[$metrics[$i]->getName()] = $value->getValue();
            }

            foreach ($row->getDimensionValues() as $i => $value) {
                $rowData[$dimensions[$i]->getName()] = $value->getValue();
            }

            $data[] = $rowData;
        }

        return $data;
    }
}
