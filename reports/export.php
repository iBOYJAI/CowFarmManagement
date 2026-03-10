<?php
/**
 * CSV Export for various modules
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';

$auth = new Auth();
$auth->requireLogin();

$db = new DBHelper();

$type = $_GET['type'] ?? '';
$fromDate = $_GET['from'] ?? null;
$toDate = $_GET['to'] ?? null;

// Default to expenses export if called without type (e.g. from main Reports page)
if ($type === '') {
    $type = 'expenses';
}

$filename = 'report_' . $type . '_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

$params = [];

switch ($type) {
    case 'milk':
        $where = 'WHERE 1=1';
        if (!empty($fromDate)) {
            $where .= ' AND mp.production_date >= ?';
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where .= ' AND mp.production_date <= ?';
            $params[] = $toDate;
        }
        $rows = $db->fetchAll("
            SELECT mp.production_date, c.tag_number, c.name AS cow_name,
                   mp.session, mp.morning_yield, mp.evening_yield, mp.total_yield
            FROM milk_production mp
            JOIN cows c ON mp.cow_id = c.id
            $where
            ORDER BY mp.production_date DESC, c.tag_number
        ", $params);
        fputcsv($output, ['Date', 'Cow Tag', 'Name', 'Session', 'Morning (L)', 'Evening (L)', 'Total (L)']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['production_date'],
                $r['tag_number'],
                $r['cow_name'],
                $r['session'],
                $r['morning_yield'],
                $r['evening_yield'],
                $r['total_yield'],
            ]);
        }
        break;

    case 'health':
        $where = 'WHERE 1=1';
        if (!empty($fromDate)) {
            $where .= ' AND hr.record_date >= ?';
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where .= ' AND hr.record_date <= ?';
            $params[] = $toDate;
        }
        $rows = $db->fetchAll("
            SELECT hr.record_date, c.tag_number, c.name AS cow_name,
                   hr.record_type, hr.diagnosis, hr.treatment, hr.vet_name, hr.cost
            FROM health_records hr
            JOIN cows c ON hr.cow_id = c.id
            $where
            ORDER BY hr.record_date DESC, c.tag_number
        ", $params);
        fputcsv($output, ['Date', 'Cow Tag', 'Name', 'Type', 'Diagnosis', 'Treatment', 'Vet', 'Cost']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['record_date'],
                $r['tag_number'],
                $r['cow_name'],
                $r['record_type'],
                $r['diagnosis'],
                $r['treatment'],
                $r['vet_name'],
                $r['cost'],
            ]);
        }
        break;

    case 'expenses':
        $where = 'WHERE 1=1';
        if (!empty($fromDate)) {
            $where .= ' AND expense_date >= ?';
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where .= ' AND expense_date <= ?';
            $params[] = $toDate;
        }
        $rows = $db->fetchAll("
            SELECT expense_date, category, description, amount, vendor, payment_method
            FROM expenses
            $where
            ORDER BY expense_date DESC
        ", $params);
        fputcsv($output, ['Date', 'Category', 'Description', 'Amount', 'Vendor', 'Payment Method']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['expense_date'],
                $r['category'],
                $r['description'],
                $r['amount'],
                $r['vendor'],
                $r['payment_method'],
            ]);
        }
        break;

    case 'sales':
        $where = 'WHERE 1=1';
        if (!empty($fromDate)) {
            $where .= ' AND sale_date >= ?';
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where .= ' AND sale_date <= ?';
            $params[] = $toDate;
        }
        $rows = $db->fetchAll("
            SELECT sale_date, customer_name, milk_quantity, unit_price, total_amount, payment_status
            FROM sales
            $where
            ORDER BY sale_date DESC
        ", $params);
        fputcsv($output, ['Date', 'Customer', 'Quantity (L)', 'Unit Price', 'Total Amount', 'Payment Status']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['sale_date'],
                $r['customer_name'],
                $r['milk_quantity'],
                $r['unit_price'],
                $r['total_amount'],
                $r['payment_status'],
            ]);
        }
        break;

    case 'breeding':
        $where = 'WHERE 1=1';
        if (!empty($fromDate)) {
            $where .= ' AND br.breeding_date >= ?';
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where .= ' AND br.breeding_date <= ?';
            $params[] = $toDate;
        }
        $rows = $db->fetchAll("
            SELECT br.breeding_date, c.tag_number, c.name AS cow_name,
                   br.breeding_type, br.bull_tag, br.expected_calving_date,
                   br.actual_calving_date, br.pregnancy_status
            FROM breeding_records br
            JOIN cows c ON br.cow_id = c.id
            $where
            ORDER BY br.breeding_date DESC, c.tag_number
        ", $params);
        fputcsv($output, ['Breeding Date', 'Cow Tag', 'Name', 'Type', 'Bull Tag', 'Expected Calving', 'Actual Calving', 'Status']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['breeding_date'],
                $r['tag_number'],
                $r['cow_name'],
                $r['breeding_type'],
                $r['bull_tag'],
                $r['expected_calving_date'],
                $r['actual_calving_date'],
                $r['pregnancy_status'],
            ]);
        }
        break;

    default:
        fputcsv($output, ['Error', 'Unsupported export type']);
        break;
}

fclose($output);
exit;


