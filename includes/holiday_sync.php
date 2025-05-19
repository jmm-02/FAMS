<?php
/**
 * Holiday Synchronization Functions
 * This file contains functions to handle holiday synchronization with attendance records
 */

/**
 * Sync holidays for a specific date
 * @param PDO $pdo Database connection
 * @param string $date Date to sync holidays for
 * @return bool True if sync was successful
 */
function syncHolidaysForDate($pdo, $date) {
    try {
        // Check if the date is a holiday
        $stmt = $pdo->prepare("SELECT * FROM holidays WHERE DATE = ?");
        $stmt->execute([$date]);
        $holiday = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($holiday) {
            // Update all attendance records for this date to mark as holiday
            $stmt = $pdo->prepare("UPDATE emp_rec SET HOLIDAY = 1, HOLIDAY_DESC = ? WHERE DATE = ?");
            $stmt->execute([$holiday['DESCRIPTION'], $date]);
            return true;
        } else {
            // If not a holiday, ensure holiday flag is removed
            $stmt = $pdo->prepare("UPDATE emp_rec SET HOLIDAY = 0, HOLIDAY_DESC = NULL WHERE DATE = ?");
            $stmt->execute([$date]);
            return true;
        }
    } catch (PDOException $e) {
        error_log("Error syncing holidays for date $date: " . $e->getMessage());
        return false;
    }
}

/**
 * Sync holidays for a date range
 * @param PDO $pdo Database connection
 * @param string $startDate Start date
 * @param string $endDate End date
 * @return bool True if sync was successful
 */
function syncHolidaysForDateRange($pdo, $startDate, $endDate) {
    try {
        // Get all holidays in the date range
        $stmt = $pdo->prepare("SELECT * FROM holidays WHERE DATE BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create a map of holiday dates to descriptions
        $holidayMap = [];
        foreach ($holidays as $holiday) {
            $holidayMap[$holiday['DATE']] = $holiday['DESCRIPTION'];
        }

        // Update all attendance records in the date range
        $stmt = $pdo->prepare("SELECT DISTINCT DATE FROM emp_rec WHERE DATE BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($dates as $date) {
            if (isset($holidayMap[$date])) {
                // This date is a holiday
                $stmt = $pdo->prepare("UPDATE emp_rec SET HOLIDAY = 1, HOLIDAY_DESC = ? WHERE DATE = ?");
                $stmt->execute([$holidayMap[$date], $date]);
            } else {
                // This date is not a holiday
                $stmt = $pdo->prepare("UPDATE emp_rec SET HOLIDAY = 0, HOLIDAY_DESC = NULL WHERE DATE = ?");
                $stmt->execute([$date]);
            }
        }
        return true;
    } catch (PDOException $e) {
        error_log("Error syncing holidays for date range $startDate to $endDate: " . $e->getMessage());
        return false;
    }
}
?> 