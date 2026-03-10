<?php
/**
 * Dashboard Page
 * Main dashboard with KPIs, charts, and recent activity
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Helper.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();
$conn = $db->getConnection();

// Get dashboard statistics
$stats = [];

// Total Cows
$stats['total_cows'] = $db->fetchOne("SELECT COUNT(*) as count FROM cows WHERE status = 'active'")['count'] ?? 0;

// Total Milk Production (Today)
$stats['today_milk'] = $db->fetchOne("SELECT SUM(total_yield) as total FROM milk_production WHERE production_date = CURDATE()")['total'] ?? 0;

// Pregnant Cows
$stats['pregnant_cows'] = $db->fetchOne("SELECT COUNT(*) as count FROM breeding_records WHERE pregnancy_status = 'pregnant' AND expected_calving_date >= CURDATE()")['count'] ?? 0;

// Upcoming Vaccinations (Next 7 days)
$stats['upcoming_vaccinations'] = $db->fetchOne("SELECT COUNT(*) as count FROM vaccinations WHERE next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")['count'] ?? 0;

// Total Expenses (This Month)
$stats['month_expenses'] = $db->fetchOne("SELECT SUM(amount) as total FROM expenses WHERE MONTH(expense_date) = MONTH(CURDATE()) AND YEAR(expense_date) = YEAR(CURDATE())")['total'] ?? 0;

// Total Sales (This Month)
$stats['month_sales'] = $db->fetchOne("SELECT SUM(total_amount) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())")['total'] ?? 0;

// Recent Milk Production (Last 7 days average)
$stats['avg_milk'] = $db->fetchOne("SELECT AVG(total_yield) as avg FROM milk_production WHERE production_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")['avg'] ?? 0;

// Recent Activity
$recentActivity = $db->fetchAll("
    SELECT al.*, u.full_name as user_name 
    FROM activity_logs al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC 
    LIMIT 10
");

// Top Milk Producers (Last 7 days)
$topProducers = $db->fetchAll("
    SELECT c.tag_number, c.name, SUM(mp.total_yield) as total_yield
    FROM milk_production mp
    JOIN cows c ON mp.cow_id = c.id
    WHERE mp.production_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY c.id, c.tag_number, c.name
    ORDER BY total_yield DESC
    LIMIT 5
");

// Upcoming Appointments
$upcomingAppointments = $db->fetchAll("
    SELECT a.*, c.tag_number, c.name as cow_name
    FROM appointments a
    LEFT JOIN cows c ON a.cow_id = c.id
    WHERE a.appointment_date >= CURDATE() AND a.status = 'scheduled'
    ORDER BY a.appointment_date, a.appointment_time
    LIMIT 5
");

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="content-area">
    <!-- Dashboard Cards -->
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="dashboard-card-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                </svg>
            </div>
            <div class="dashboard-card-title">Total Cows</div>
            <div class="dashboard-card-value"><?php echo $stats['total_cows']; ?></div>
            <div class="dashboard-card-change positive">
                <span>Active</span>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                </svg>
            </div>
            <div class="dashboard-card-title">Today's Milk (Liters)</div>
            <div class="dashboard-card-value"><?php echo number_format($stats['today_milk'], 2); ?></div>
            <div class="dashboard-card-change">
                <span>7-day avg: <?php echo number_format($stats['avg_milk'], 2); ?>L</span>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <div class="dashboard-card-title">Pregnant Cows</div>
            <div class="dashboard-card-value"><?php echo $stats['pregnant_cows']; ?></div>
            <div class="dashboard-card-change positive">
                <span>Active pregnancies</span>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                </svg>
            </div>
            <div class="dashboard-card-title">Upcoming Vaccinations</div>
            <div class="dashboard-card-value"><?php echo $stats['upcoming_vaccinations']; ?></div>
            <div class="dashboard-card-change">
                <span>Next 7 days</span>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                </svg>
            </div>
            <div class="dashboard-card-title">Monthly Expenses</div>
            <div class="dashboard-card-value">₹<?php echo number_format($stats['month_expenses'], 2); ?></div>
            <div class="dashboard-card-change">
                <span>This month</span>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 18c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </div>
            <div class="dashboard-card-title">Monthly Sales</div>
            <div class="dashboard-card-value">₹<?php echo number_format($stats['month_sales'], 2); ?></div>
            <div class="dashboard-card-change positive">
                <span>This month</span>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <!-- Top Milk Producers -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Milk Producers (Last 7 Days)</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tag Number</th>
                            <th>Name</th>
                            <th>Total Yield (L)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topProducers)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #999;">No data available</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topProducers as $producer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producer['tag_number']); ?></td>
                                    <td><?php echo htmlspecialchars($producer['name'] ?? '-'); ?></td>
                                    <td><?php echo number_format($producer['total_yield'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Upcoming Appointments</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Cow</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcomingAppointments)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #999;">No upcoming appointments</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcomingAppointments as $apt): ?>
                                <tr>
                                    <td><?php echo Helper::formatDate($apt['appointment_date']); ?></td>
                                    <td><?php echo date('H:i', strtotime($apt['appointment_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($apt['cow_name'] ?? $apt['tag_number'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($apt['purpose']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Activity</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentActivity)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">No recent activity</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $activity): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($activity['user_name'] ?? 'System'); ?></td>
                                <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                <td><?php echo htmlspecialchars($activity['module']); ?></td>
                                <td><?php echo htmlspecialchars($activity['description']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

