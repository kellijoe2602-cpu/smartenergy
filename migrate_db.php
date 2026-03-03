<?php
require_once 'includes/db.php';

try {
    $pdo->exec("ALTER TABLE energy_consumption ADD COLUMN IF NOT EXISTS start_date DATE NULL");
    $pdo->exec("ALTER TABLE energy_consumption ADD COLUMN IF NOT EXISTS end_date DATE NULL");
    $pdo->exec("UPDATE energy_consumption SET start_date = date_recorded, end_date = date_recorded WHERE start_date IS NULL");
    echo "<h1>Database Migrated Successfully!</h1>";
    echo "<p>Columns <code>start_date</code> and <code>end_date</code> have been added.</p>";
    echo "<a href='dashboard.php'>Return to Dashboard</a>";
} catch (PDOException $e) {
    echo "<h1>Migration Failed!</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>