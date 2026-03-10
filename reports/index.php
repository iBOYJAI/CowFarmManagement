<?php
/**
 * Reports Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$pageTitle = 'Reports';
include __DIR__ . '/../includes/header.php';
?>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Reports</h2>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="card">
                    <h3>Milk Production Report</h3>
                    <p>Generate milk production reports by date range, cow, or overall statistics.</p>
                    <a href="<?php echo BASE_URL; ?>reports/milk_report.php" class="btn btn-primary">Generate Report</a>
                </div>
                
                <div class="card">
                    <h3>Health Records Report</h3>
                    <p>View health records, treatments, and vaccination history.</p>
                    <a href="<?php echo BASE_URL; ?>reports/health_report.php" class="btn btn-primary">Generate Report</a>
                </div>
                
                <div class="card">
                    <h3>Financial Report</h3>
                    <p>View expenses, sales, and profit/loss statements.</p>
                    <a href="<?php echo BASE_URL; ?>reports/financial_report.php" class="btn btn-primary">Generate Report</a>
                </div>
                
                <div class="card">
                    <h3>Breeding Report</h3>
                    <p>View breeding records, pregnancy status, and calving schedules.</p>
                    <a href="<?php echo BASE_URL; ?>reports/breeding_report.php" class="btn btn-primary">Generate Report</a>
                </div>
                
                <div class="card">
                    <h3>Export Data (CSV)</h3>
                    <p>Export cow data, milk production, or other records to CSV.</p>
                    <a href="<?php echo BASE_URL; ?>reports/export.php" class="btn btn-primary">Export CSV</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

