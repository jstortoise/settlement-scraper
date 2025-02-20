<?php
require_once(dirname(__FILE__) ."/vendor/autoload.php");

use Model\Settlement;
use Service\Scraper;

$settlementModel = new Settlement();
$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';

if ($action === 'create_table') {
    try {
        $settlementModel->createTable();
        echo 'Successfully created a table for settlements!';
    } catch (\Throwable $th) {
        throw $th;
    }
} else if ($action === 'scrape_data') {
    $scraper = new Scraper('https://zlk.com/settlement');
    $data = $scraper->scrape();
    foreach ($data as $row) {
        $settlementModel->insert($row);
    }
} else if ($action === 'delete_data') {
    $settlementModel->delete();
} else if ($action === 'drop_table') {
    $settlementModel->dropTable();
}

if ($settlementModel->tableExists()) {
    $data = $settlementModel->read();

    $settlementsInDeadline = $settlementModel->getSettlementsInDeadline();
} else {
    $data = [];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Settlement Data</title>
</head>
<body>
    <form method="POST">
        <?php if (!$settlementModel->tableExists()): ?>
            <button type="submit" name="action" value="create_table">Create Database Table</button>
        <?php else: ?>
            <button type="submit" name="action" value="scrape_data">Scrape Data</button>
            <button type="submit" name="action" value="delete_data">Delete Data</button>
            <button type="submit" name="action" value="drop_table">Drop Table</button>
        <?php endif; ?>
    </form>

    <?php if ($settlementModel->tableExists()): ?>
        <h3>Settlements</h3>
        <table border="1">
            <tr>
                <th>Company Name</th>
                <th>Ticker Symbol</th>
                <th>Deadline</th>
                <th>Class Period</th>
                <th>Settlement Fund</th>
                <th>Hearing Date</th>
                <th>Post URL</th>
            </tr>
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['company_name']) ?></td>
                        <td><?= htmlspecialchars($row['ticker_symbol']) ?></td>
                        <td>
                            <?= htmlspecialchars(date('M d, Y', strtotime($row['deadline']))) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(date('M d, Y', strtotime($row['class_period_start'])) . ' - ' . date('M d, Y' , strtotime($row['class_period_end']))) ?>
                        </td>
                        <td align="right"><?= htmlspecialchars('$' . number_format($row['settlement_fund'])) ?></td>
                        <td><?= htmlspecialchars(date('M d, Y', strtotime($row['settlement_hearing_date']))) ?></td>
                        <td><a href="<?= htmlspecialchars($row['post_url']) ?>">Link</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">
                        No Data
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <h3>Settlements in Deadline</h3>
        <table border="1">
            <tr>
                <th>Company Name</th>
                <th>Ticker Symbol</th>
                <th>Deadline</th>
                <th>Class Period</th>
                <th>Settlement Fund</th>
                <th>Hearing Date</th>
                <th>Post URL</th>
            </tr>
            <?php if (!empty($settlementsInDeadline)): ?>
                <?php foreach ($settlementsInDeadline as $settlement): ?>
                    <tr>
                    <td><?= htmlspecialchars($settlement['company_name']) ?></td>
                        <td><?= htmlspecialchars($settlement['ticker_symbol']) ?></td>
                        <td>
                            <?= htmlspecialchars(date('M d, Y', strtotime($settlement['deadline']))) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(date('M d, Y', strtotime($settlement['class_period_start'])) . ' - ' . date('M d, Y' , strtotime($settlement['class_period_end']))) ?>
                        </td>
                        <td align="right"><?= htmlspecialchars('$' . number_format($settlement['settlement_fund'])) ?></td>
                        <td><?= htmlspecialchars(date('M d, Y', strtotime($settlement['settlement_hearing_date']))) ?></td>
                        <td><a href="<?= htmlspecialchars($settlement['post_url']) ?>">Link</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">
                        No Data
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</body>

<style>
    th, td {
        padding: 5px 10px;
    }
</style>
</html>