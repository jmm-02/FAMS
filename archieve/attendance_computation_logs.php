<?php
/**
 * FAMSS - Attendance Computation Logic
 * This file documents the computation processes, rules, and logic used for attendance calculations
 */

/**
 * COMPUTATION PROCESSES
 * 
 * The system handles the following calculations:
 * 1. Total working time computation
 * 2. Late minutes calculation 
 * 3. Undertime minutes calculation
 * 4. Special cases: Official Business (OB), Sick Leave (SL), and Holidays
 */

// ==========================================
// TOTAL TIME COMPUTATION
// ==========================================

/**
 * Function: computeTotalTime
 * Purpose: Calculates the total working hours based on time logs
 * 
 * @param string $am_in     Morning time in (format: HH:MM)
 * @param string $am_out    Morning time out (format: HH:MM)
 * @param string $pm_in     Afternoon time in (format: HH:MM)
 * @param string $pm_out    Afternoon time out (format: HH:MM)
 * @param string $department Department of the employee (affects rules)
 * @param int $isHoliday    1 if the day is a holiday, 0 otherwise
 * @param int $isOB         1 if marked as Official Business, 0 otherwise
 * @param int $isSL         1 if marked as Sick Leave, 0 otherwise
 * @return string           Total time in format "HH:MM hrs." or "—" if invalid
 */
function computeTotalTime($am_in, $am_out, $pm_in, $pm_out, $department = null, $isHoliday = 0, $isOB = 0, $isSL = 0) {
    // Rule 1: If it's OB or SL, return standard time based on department
    if ($isOB == 1 || $isSL == 1) {
        $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
        return $isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
    }
    
    // Rule 2: For holidays, only credit hours if there are actual time entries
    if ($isHoliday == 1) {
        // Check if there are any time entries
        if (!$am_in && !$am_out && !$pm_in && !$pm_out) {
            return '—'; // No time entries, no hours credited
        } else {
            // Has time entries, give standard time based on department
            $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
            return $isOtherPersonnel ? '12:00 hrs.' : '8:00 hrs.';
        }
    }
    
    // Convert time strings to minutes for calculation
    function toMinutes($time) {
        if (!$time) return null;
        $parts = explode(':', $time);
        return (int)$parts[0] * 60 + (int)$parts[1];
    }
    
    $amInMinRaw = toMinutes($am_in);
    
    // Rule 3: For AM Out and PM In, always use defaults if both AM In and PM Out are present (full-day attendance)
    $amOutVal = $am_out;
    $pmInVal = $pm_in;
    if ($am_in && $pm_out) {
        $amOutVal = '12:00';
        $pmInVal = '13:00';
    }
    
    $amOutMin = toMinutes($amOutVal);
    $pmInMin = toMinutes($pmInVal);
    $pmOutMin = toMinutes($pm_out);

    // Rule 4: For regular employees, if AM In is before 8:00, treat as 8:00
    // For Other_Personnel, if AM In is before 6:00, treat as 6:00
    $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
    $amInMin = $amInMinRaw;
    if ($isOtherPersonnel && $amInMinRaw !== null && $amInMinRaw < 360) {
        $amInMin = 360; // 6:00 AM in minutes
    } else if (!$isOtherPersonnel && $amInMinRaw !== null && $amInMinRaw < 480) {
        $amInMin = 480; // 8:00 AM in minutes
    }

    $total = 0;

    // Case 1: All four times present
    if (
        $amInMin !== null && $amOutMin !== null &&
        $pmInMin !== null && $pmOutMin !== null
    ) {
        $total = ($amOutMin - $amInMin) + ($pmOutMin - $pmInMin);
    }
    // Case 2: Only AM In and PM Out present (no AM Out, no PM In)
    else if (
        $amInMin !== null && $pmOutMin !== null &&
        $amOutMin === null && $pmInMin === null
    ) {
        $total = $pmOutMin - $amInMin - 60; // Subtract 1 hour break
    }
    // Case 3: AM In, AM Out, and PM In present, PM Out missing
    else if (
        $amInMin !== null && $amOutMin !== null &&
        $pmInMin !== null && $pmOutMin === null
    ) {
        $total = ($amOutMin - $amInMin) + 60; // Add 1 hour for lunch
    }
    // Case 4: Sum all valid pairs
    else {
        if ($amInMin !== null && $amOutMin !== null && $amOutMin > $amInMin) {
            $total += $amOutMin - $amInMin;
        }
        if ($pmInMin !== null && $pmOutMin !== null && $pmOutMin > $pmInMin) {
            $total += $pmOutMin - $pmInMin;
        }
    }

    // Return dash if no valid total
    if ($total <= 0) return '—';

    // Rule 5: Cap total time based on department
    $capMinutes = $isOtherPersonnel ? 720 : 480; // 12 hours or 8 hours
    $displayMinutes = $total > $capMinutes ? $capMinutes : $total;
    
    $hours = floor($displayMinutes / 60);
    $minutes = $displayMinutes % 60;
    return sprintf("%d:%02d hrs.", $hours, $minutes);
}

// ==========================================
// UNDERTIME COMPUTATION
// ==========================================

/**
 * Function: computeUndertime
 * Purpose: Calculates undertime minutes based on total working time
 * 
 * @param string $totalTime   Total time worked (format: "HH:MM hrs.")
 * @param string $department  Department of employee
 * @param string $am_in       Morning time in (format: HH:MM)
 * @param int $isHoliday      1 if the day is a holiday, 0 otherwise
 * @return string             Undertime in minutes or empty string if no undertime
 */
function computeUndertime($totalTime, $department, $am_in, $isHoliday = 0) {
    // Rule 1: If it's a holiday with no time entries (showing as —), there is no undertime
    if ($isHoliday == 1 && $totalTime === '—') {
        return '';
    }
    
    // Rule 2: If total time is not valid, no undertime
    if ($totalTime === '—') return '';
    
    // Remove ' hrs.' suffix if present
    $timeStr = str_replace(' hrs.', '', $totalTime);
    list($hours, $minutes) = explode(':', $timeStr);
    $totalMinutes = ($hours * 60) + $minutes;
    
    // Rule 3: Adjust base time for undertime calculation based on department
    $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';

    if ($isOtherPersonnel) {
        // Rule 3.1: For Other_Personnel department
        $baseStart = 360; // 6:00 AM in minutes
        if ($am_in) {
            $parts = explode(':', $am_in);
            $h = (int)$parts[0];
            $m = (int)$parts[1];
            if ($h < 6) {
                $baseStart = 360; // Still 6:00 AM
            } else {
                $baseStart = $h * 60 + $m;
            }
        }
        // Standard working time: 12 hours (720 minutes)
        $standardMinutes = 720;
        $undertime = $standardMinutes - $totalMinutes;
        return $undertime <= 0 ? '' : (string)$undertime;
    } else {
        // Rule 3.2: For regular employees
        $baseStart = 480; // 8:00 AM in minutes
        if ($am_in) {
            $parts = explode(':', $am_in);
            $h = (int)$parts[0];
            $m = (int)$parts[1];
            if ($h < 8) {
                $baseStart = 480; // Still 8:00 AM
            } else {
                $baseStart = $h * 60 + $m;
            }
        }
        
        // Rule 4: Different undertime rules based on total time worked
        if ($totalMinutes >= 480) { // 8 hours or more
            return ''; // No undertime
        } else if ($totalMinutes >= 360) { // Between 6 and 8 hours
            return (string)(480 - $totalMinutes); // Calculate undertime from 8 hours
        } else { // Less than 6 hours
            return (string)(480 - $totalMinutes); // Calculate undertime from 8 hours
        }
    }
}

// ==========================================
// LATE MINUTES COMPUTATION
// ==========================================

/**
 * Calculate late minutes based on base time
 * 
 * @param string $am_in       Morning time in (format: HH:MM)
 * @param string $department  Department of employee
 * @return int                Late minutes or 0 if not late
 */
function calculateLateMinutes($am_in, $department) {
    if (!$am_in) return 0;
    
    // Set base time according to department
    $isOtherPersonnel = $department && strtolower(trim($department)) === 'other_personnel';
    $baseHour = $isOtherPersonnel ? 6 : 8;
    $baseMinute = 0;
    
    // Calculate late minutes
    $parts = explode(':', $am_in);
    $h = (int)$parts[0];
    $m = (int)$parts[1];
    
    if ($h > $baseHour || ($h === $baseHour && $m > $baseMinute)) {
        return ($h - $baseHour) * 60 + ($m - $baseMinute);
    }
    
    return 0;
}

// ==========================================
// SPECIAL CASES & BUSINESS RULES
// ==========================================

/**
 * OFFICIAL BUSINESS (OB) RULES:
 * 1. When an employee is marked as OB:
 *    - Regular employees receive 8 hours credit
 *    - Other_Personnel employees receive 12 hours credit
 * 2. OB status overrides time calculations
 * 3. No late or undertime is calculated for OB days
 * 4. An employee cannot be marked as both OB and SL on the same day
 */

/**
 * SICK LEAVE (SL) RULES:
 * 1. When an employee is marked as SL:
 *    - Regular employees receive 8 hours credit
 *    - Other_Personnel employees receive 12 hours credit
 * 2. SL status overrides time calculations
 * 3. No late or undertime is calculated for SL days
 * 4. An employee cannot be marked as both SL and OB on the same day
 */

/**
 * HOLIDAY RULES:
 * 1. For holidays with no time entries:
 *    - No hours are credited
 *    - No undertime is calculated
 * 2. For holidays with time entries:
 *    - Regular employees receive 8 hours credit
 *    - Other_Personnel employees receive 12 hours credit
 *    - No late or undertime is calculated
 */

/**
 * ATTENDANCE DISPLAY RULES:
 * 1. For regular employees, if AM In is before 8:00, display 8:00 AM
 * 2. For Other_Personnel, if AM In is before 6:00, display 6:00 AM
 * 3. For AM Out and PM In, show defaults (12:00 and 13:00) if both AM In and PM Out are present
 * 4. Time is displayed in 12-hour format with AM/PM
 * 5. Date is displayed with day of week
 */ 