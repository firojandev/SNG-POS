<?php

namespace App\Services;

use Carbon\Carbon;

class SalesReportService
{
    /**
     * Parse date range from request
     * Handles user's configured date format properly
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string $defaultPeriod 'current_month', 'current_year', 'today', etc.
     * @return array ['start' => Carbon, 'end' => Carbon, 'startDate' => string, 'endDate' => string]
     */
    public function parseDateRange($startDate = null, $endDate = null, $defaultPeriod = 'current_month'): array
    {
        // Get user's configured date format
        $dateFormat = get_option('date_format', 'Y-m-d');

        // Set defaults based on period if dates not provided
        if (empty($startDate) || empty($endDate)) {
            $defaults = $this->getDefaultDateRange($defaultPeriod, $dateFormat);
            $startDate = $startDate ?? $defaults['startDate'];
            $endDate = $endDate ?? $defaults['endDate'];
        }

        // Parse dates using the configured format
        try {
            $start = Carbon::createFromFormat($dateFormat, $startDate)->startOfDay();
            $end = Carbon::createFromFormat($dateFormat, $endDate)->endOfDay();
        } catch (\Exception $e) {
            // Fallback: try to parse without format specification
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();

                // Re-format dates to user's format
                $startDate = $start->format($dateFormat);
                $endDate = $end->format($dateFormat);
            } catch (\Exception $e) {
                // Ultimate fallback: use current month
                $defaults = $this->getDefaultDateRange('current_month', $dateFormat);
                $startDate = $defaults['startDate'];
                $endDate = $defaults['endDate'];
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
            }
        }

        return [
            'start' => $start,
            'end' => $end,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateFormat' => $dateFormat
        ];
    }

    /**
     * Get default date range based on period
     *
     * @param string $period
     * @param string $dateFormat
     * @return array
     */
    private function getDefaultDateRange($period, $dateFormat): array
    {
        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay()->format($dateFormat);
                $endDate = now()->endOfDay()->format($dateFormat);
                break;

            case 'yesterday':
                $startDate = now()->subDay()->startOfDay()->format($dateFormat);
                $endDate = now()->subDay()->endOfDay()->format($dateFormat);
                break;

            case 'current_week':
                $startDate = now()->startOfWeek()->format($dateFormat);
                $endDate = now()->endOfWeek()->format($dateFormat);
                break;

            case 'last_week':
                $startDate = now()->subWeek()->startOfWeek()->format($dateFormat);
                $endDate = now()->subWeek()->endOfWeek()->format($dateFormat);
                break;

            case 'current_month':
                $startDate = now()->startOfMonth()->format($dateFormat);
                $endDate = now()->endOfMonth()->format($dateFormat);
                break;

            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth()->format($dateFormat);
                $endDate = now()->subMonth()->endOfMonth()->format($dateFormat);
                break;

            case 'current_year':
                $startDate = now()->startOfYear()->format($dateFormat);
                $endDate = now()->endOfYear()->format($dateFormat);
                break;

            case 'last_year':
                $startDate = now()->subYear()->startOfYear()->format($dateFormat);
                $endDate = now()->subYear()->endOfYear()->format($dateFormat);
                break;

            default: // current_month
                $startDate = now()->startOfMonth()->format($dateFormat);
                $endDate = now()->endOfMonth()->format($dateFormat);
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    /**
     * Format date for display
     *
     * @param Carbon|string $date
     * @param string|null $format
     * @return string
     */
    public function formatDate($date, $format = null): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        $format = $format ?? get_option('date_format', 'Y-m-d');

        return $date->format($format);
    }

    /**
     * Validate date range
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array ['valid' => bool, 'message' => string|null]
     */
    public function validateDateRange($start, $end): array
    {
        if ($start->gt($end)) {
            return [
                'valid' => false,
                'message' => 'Start date cannot be greater than end date.'
            ];
        }

        if ($end->isFuture()) {
            return [
                'valid' => false,
                'message' => 'End date cannot be in the future.'
            ];
        }

        // Check if date range is too large (optional, can be removed if not needed)
        $daysDifference = $start->diffInDays($end);
        if ($daysDifference > 365) {
            return [
                'valid' => false,
                'message' => 'Date range cannot exceed 365 days.'
            ];
        }

        return [
            'valid' => true,
            'message' => null
        ];
    }

    /**
     * Get date format for JavaScript datepicker
     * Converts PHP date format to jQuery UI format
     *
     * @return array ['php' => string, 'js' => string]
     */
    public function getDateFormats(): array
    {
        $phpFormat = get_option('date_format', 'Y-m-d');

        // Convert PHP format to jQuery UI format
        $jsFormat = $this->phpToJqueryDateFormat($phpFormat);

        return [
            'php' => $phpFormat,
            'js' => $jsFormat
        ];
    }

    /**
     * Convert PHP date format to jQuery UI datepicker format
     *
     * @param string $phpFormat
     * @return string
     */
    private function phpToJqueryDateFormat($phpFormat): string
    {
        $map = [
            // Day
            'd' => 'dd',    // Day of month with leading zero
            'j' => 'd',     // Day of month without leading zero
            'D' => 'D',     // Short day name
            'l' => 'DD',    // Full day name

            // Month
            'm' => 'mm',    // Month with leading zero
            'n' => 'm',     // Month without leading zero
            'M' => 'M',     // Short month name
            'F' => 'MM',    // Full month name

            // Year
            'Y' => 'yy',    // 4-digit year
            'y' => 'y',     // 2-digit year

            // Separators
            '/' => '/',
            '-' => '-',
            '.' => '.',
            ' ' => ' ',
        ];

        $jsFormat = '';
        for ($i = 0; $i < strlen($phpFormat); $i++) {
            $char = $phpFormat[$i];
            $jsFormat .= $map[$char] ?? $char;
        }

        return $jsFormat;
    }

    /**
     * Create CSV filename for reports
     *
     * @param string $reportType
     * @param string $startDate
     * @param string $endDate
     * @return string
     */
    public function createCsvFilename($reportType, $startDate, $endDate): string
    {
        // Convert dates to filename-safe format (YYYY-MM-DD)
        $dateFormat = get_option('date_format', 'Y-m-d');

        try {
            $start = Carbon::createFromFormat($dateFormat, $startDate);
            $end = Carbon::createFromFormat($dateFormat, $endDate);
        } catch (\Exception $e) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
        }

        $startFormatted = $start->format('Y-m-d');
        $endFormatted = $end->format('Y-m-d');

        return sprintf('%s_report_%s_to_%s.csv', $reportType, $startFormatted, $endFormatted);
    }
}
