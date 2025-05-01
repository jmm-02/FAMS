<?php
/**
 * Format time value from Excel to MySQL time format
 * 
 * @param mixed $timeValue The time value from Excel
 * @return string|null Formatted time string or NULL if empty
 */
function formatTimeValue($timeValue) {
    // If empty, return NULL
    if (empty($timeValue) || $timeValue === 'NULL') {
        return null;
    }
    
    // If it's already a formatted time string like "08:30:00"
    if (is_string($timeValue) && preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $timeValue)) {
        // Add seconds if not present
        if (substr_count($timeValue, ':') === 1) {
            $timeValue .= ':00';
        }
        return $timeValue;
    }
    
    // If it's a numeric value (Excel stores times as fractions of a day)
    if (is_numeric($timeValue)) {
        // Convert Excel time to seconds
        $seconds = round($timeValue * 86400); // 86400 seconds in a day
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        
        // Format as HH:MM:SS
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
    
    // If it's a DateTime object
    if ($timeValue instanceof DateTime) {
        return $timeValue->format('H:i:s');
    }
    
    // If we can't parse it, log and return NULL
    file_put_contents(__DIR__ . '/excel_debug.log', "Could not parse time value: " . print_r($timeValue, true) . "\n", FILE_APPEND);
    return null;
}
?>
