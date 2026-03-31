<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Predicts next month's energy consumption based on historical data using simple linear regression.
 * In a real-world scenario, you might use more complex ML models (ARIMA, RNN, etc.).
 * Here, we use the trend of the last 6 months to project the next month's usage.
 */
function predictNextMonthUsage($pdo, $user_id) {
    // Fetch monthly consumption for the last 6 months
    $stmt = $pdo->prepare("
        SELECT 
            SUM(consumption_kwh) as total,
            MONTH(date_recorded) as m,
            YEAR(date_recorded) as y
        FROM energy_consumption 
        WHERE user_id = ? 
        GROUP BY y, m
        ORDER BY y DESC, m DESC
        LIMIT 6
    ");
    $stmt->execute([$user_id]);
    $history = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

    $n = count($history);
    if ($n < 2) {
        // Not enough data for prediction, return current monthly usage or a default
        $stmt = $pdo->prepare("SELECT SUM(consumption_kwh) as total FROM energy_consumption WHERE user_id = ? AND MONTH(date_recorded) = MONTH(CURRENT_DATE()) AND YEAR(date_recorded) = YEAR(CURRENT_DATE())");
        $stmt->execute([$user_id]);
        return $stmt->fetch()['total'] ?? 0;
    }

    $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;
    foreach ($history as $i => $row) {
        $x = $i + 1; // x = 1, 2, 3...
        $y = (float)$row['total'];
        $sumX += $x;
        $sumY += $y;
        $sumXY += ($x * $y);
        $sumX2 += ($x * $x);
    }

    // Slope (m) and Intercept (b) for y = mx + b
    $denominator = ($n * $sumX2 - ($sumX * $sumX));
    if ($denominator == 0) return $history[$n-1]['total']; // Flattened trend
    
    $m = ($n * $sumXY - $sumX * $sumY) / $denominator;
    $b = ($sumY - $m * $sumX) / $n;

    // Predict for the next month (index n + 1)
    $prediction = $m * ($n + 1) + $b;
    
    return max(0, $prediction); // Ensure no negative prediction
}

function getUsageAlerts($pdo, $user_id, $current_monthly_usage) {
    $predicted = predictNextMonthUsage($pdo, $user_id);
    $alerts = [];

    // Simple threshold example (Example: User threshold could be 500 Units)
    $threshold = 500; 

    if ($predicted > $threshold) {
        $alerts[] = [
            'type' => 'warning',
            'message' => "<strong>ML Insight:</strong> Based on your usage patterns, next month's consumption is predicted to be <strong>" . number_format($predicted, 1) . " Units</strong>, which exceeds your ideal limit of 500 Units. Consider reducing heavy appliance usage."
        ];
    }

    // High current usage alert
    if ($current_monthly_usage > 400) {
        $alerts[] = [
            'type' => 'danger',
            'message' => "<strong>Alert:</strong> Your current monthly usage (" . number_format($current_monthly_usage, 1) . " Units) has reached a high-cost slab. Avoid simultaneous use of Air Conditioners and Heaters."
        ];
    }

    return $alerts;
}
?>
