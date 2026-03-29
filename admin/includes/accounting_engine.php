<?php
// --- Manual Journal Entry for Unearned Revenue ---
function acc_post_unearned_revenue($db, $amount, $memo = 'Unearned Revenue Liability', $date = null) {
    if (!$date) $date = date('Y-m-d');
    $lines = [
        ['account_code' => '1000', 'debit' => $amount, 'credit' => 0, 'line_memo' => $memo . ' (Cash)'],
        ['account_code' => '2000', 'debit' => 0, 'credit' => $amount, 'line_memo' => $memo . ' (Unearned Revenue)']
    ];
    return acc_create_entry($db, $date, $memo, 'manual', 'unearned-revenue', $lines, 0, 0, 'Admin');
}


function acc_bootstrap_tables($db)
{
    mysqli_query($db, "CREATE TABLE IF NOT EXISTS chart_of_accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) NOT NULL UNIQUE,
        name VARCHAR(120) NOT NULL,
        account_type ENUM('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
        normal_side ENUM('Debit','Credit') NOT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    mysqli_query($db, "CREATE TABLE IF NOT EXISTS journal_entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        entry_date DATE NOT NULL,
        period_year INT NOT NULL,
        memo VARCHAR(255) NULL,
        source_type VARCHAR(50) NULL,
        source_id VARCHAR(50) NULL,
        is_adjustment TINYINT(1) NOT NULL DEFAULT 0,
        is_closing TINYINT(1) NOT NULL DEFAULT 0,
        status ENUM('posted','draft') NOT NULL DEFAULT 'posted',
        posted_by VARCHAR(80) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    mysqli_query($db, "CREATE TABLE IF NOT EXISTS journal_lines (
        id INT AUTO_INCREMENT PRIMARY KEY,
        journal_entry_id INT NOT NULL,
        account_id INT NOT NULL,
        debit DECIMAL(12,2) NOT NULL DEFAULT 0,
        credit DECIMAL(12,2) NOT NULL DEFAULT 0,
        line_memo VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_journal_lines_entry FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
        CONSTRAINT fk_journal_lines_account FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    mysqli_query($db, "CREATE TABLE IF NOT EXISTS fiscal_periods (
        id INT AUTO_INCREMENT PRIMARY KEY,
        period_year INT NOT NULL UNIQUE,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        status ENUM('open','closed') NOT NULL DEFAULT 'open',
        closed_at DATETIME NULL,
        closed_by VARCHAR(80) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    mysqli_query($db, "CREATE TABLE IF NOT EXISTS transactions_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        txn_date DATE NOT NULL,
        source_table VARCHAR(50) NOT NULL,
        source_id VARCHAR(50) NOT NULL,
        reference_no VARCHAR(80) NULL,
        description VARCHAR(255) NOT NULL,
        account_code VARCHAR(20) NOT NULL,
        debit DECIMAL(12,2) NOT NULL DEFAULT 0,
        credit DECIMAL(12,2) NOT NULL DEFAULT 0,
        journal_entry_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY idx_txn_source (source_table, source_id),
        KEY idx_txn_date (txn_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    mysqli_query($db, "CREATE TABLE IF NOT EXISTS owner_capital_contributions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        contribution_date DATE NOT NULL,
        amount DECIMAL(12,2) NOT NULL DEFAULT 0,
        reference_no VARCHAR(80) NULL,
        notes VARCHAR(255) NULL,
        funded_by VARCHAR(120) NOT NULL DEFAULT 'Owner',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $defaults = [
        ['1000', 'Cash', 'Asset', 'Debit'],
        ['1100', 'Accounts Receivable', 'Asset', 'Debit'],
        ['1200', 'Prepaid Expenses', 'Asset', 'Debit'],
        ['1500', 'Gym Equipment', 'Asset', 'Debit'],
        ['2000', 'Accounts Payable', 'Liability', 'Credit'],
        ['2100', 'Salaries Payable', 'Liability', 'Credit'],
        ['2200', 'Unearned Revenue', 'Liability', 'Credit'],
        ['3000', 'Owner Equity', 'Equity', 'Credit'],
        ['3100', 'Retained Earnings', 'Equity', 'Credit'],
        ['4000', 'Membership Revenue', 'Revenue', 'Credit'],
        ['4100', 'Other Revenue', 'Revenue', 'Credit'],
        ['5000', 'Salaries Expense', 'Expense', 'Debit'],
        ['5100', 'Rent Expense', 'Expense', 'Debit'],
        ['5200', 'Utilities Expense', 'Expense', 'Debit'],
        ['5300', 'Equipment Expense', 'Expense', 'Debit'],
        ['5400', 'Marketing Expense', 'Expense', 'Debit'],
        ['5500', 'Administrative Expense', 'Expense', 'Debit'],
        ['5600', 'Depreciation Expense', 'Expense', 'Debit']
    ];

    foreach ($defaults as $acc) {
        $code = mysqli_real_escape_string($db, $acc[0]);
        $name = mysqli_real_escape_string($db, $acc[1]);
        $type = mysqli_real_escape_string($db, $acc[2]);
        $side = mysqli_real_escape_string($db, $acc[3]);
        mysqli_query($db, "INSERT IGNORE INTO chart_of_accounts(code, name, account_type, normal_side) VALUES('$code', '$name', '$type', '$side')");
    }
}

function acc_source_already_posted($db, $sourceType, $sourceId)
{
    $sourceTypeEsc = mysqli_real_escape_string($db, $sourceType);
    $sourceIdEsc = mysqli_real_escape_string($db, $sourceId);
    $res = mysqli_query($db, "SELECT id FROM journal_entries WHERE source_type='$sourceTypeEsc' AND source_id='$sourceIdEsc' LIMIT 1");
    return $res && mysqli_num_rows($res) > 0;
}

function acc_log_transaction_lines($db, $entryDate, $sourceType, $sourceId, $referenceNo, $memo, $lines, $journalEntryId, $paymentHistoryId = null, $branch_id = 0)
{
    $entryDateEsc = mysqli_real_escape_string($db, $entryDate);
    $sourceTypeEsc = mysqli_real_escape_string($db, $sourceType);
    $sourceIdEsc = mysqli_real_escape_string($db, $sourceId);
    $referenceNoEsc = mysqli_real_escape_string($db, $referenceNo);
    $memoEsc = mysqli_real_escape_string($db, $memo);
    $journalEntryId = (int)$journalEntryId;
    $paymentHistoryIdEsc = $paymentHistoryId !== null ? (int)$paymentHistoryId : 'NULL';
    $branch_id = (int)$branch_id;

    foreach ($lines as $line) {
        $accountCode = mysqli_real_escape_string($db, (string)($line['account_code'] ?? ''));
        $debit = isset($line['debit']) ? (float)$line['debit'] : 0;
        $credit = isset($line['credit']) ? (float)$line['credit'] : 0;
        if ($accountCode === '' || ($debit == 0 && $credit == 0)) {
            continue;
        }

        mysqli_query(
            $db,
            "INSERT INTO transactions_log(txn_date, source_table, source_id, reference_no, description, account_code, debit, credit, journal_entry_id, payment_history_id, branch_id)
             VALUES('$entryDateEsc', '$sourceTypeEsc', '$sourceIdEsc', '$referenceNoEsc', '$memoEsc', '$accountCode', '$debit', '$credit', '$journalEntryId', $paymentHistoryIdEsc, $branch_id)"
        );
    }
}

function acc_delete_source_postings($db, $sourceType, $sourceId)
{
    $sourceTypeEsc = mysqli_real_escape_string($db, $sourceType);
    $sourceIdEsc = mysqli_real_escape_string($db, $sourceId);

    mysqli_begin_transaction($db);
    try {
        $entryIds = [];
        $res = mysqli_query($db, "SELECT id FROM journal_entries WHERE source_type='$sourceTypeEsc' AND source_id='$sourceIdEsc'");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $entryIds[] = (int)$row['id'];
            }
        }

        if (!empty($entryIds)) {
            $idList = implode(',', $entryIds);
            mysqli_query($db, "DELETE FROM transactions_log WHERE journal_entry_id IN ($idList)");
            mysqli_query($db, "DELETE FROM journal_entries WHERE id IN ($idList)");
        } else {
            mysqli_query($db, "DELETE FROM transactions_log WHERE source_table='$sourceTypeEsc' AND source_id='$sourceIdEsc'");
        }

        mysqli_commit($db);
        return ['ok' => true, 'deleted' => count($entryIds)];
    } catch (Throwable $ex) {
        mysqli_rollback($db);
        return ['ok' => false, 'message' => $ex->getMessage()];
    }
}

function acc_account_id_by_code($db, $code)
{
    $code = mysqli_real_escape_string($db, $code);
    $res = mysqli_query($db, "SELECT id FROM chart_of_accounts WHERE code='$code' LIMIT 1");
    if (!$res || mysqli_num_rows($res) === 0) {
        return 0;
    }
    $row = mysqli_fetch_assoc($res);
    return (int)$row['id'];
}

function acc_create_entry($db, $entryDate, $memo, $sourceType, $sourceId, $lines, $isAdjustment = 0, $branchId = 0, $isClosing = 0, $postedBy = 'System')
{
    if (empty($lines) || !is_array($lines)) {
        return ['ok' => false, 'message' => 'Journal lines are required'];
    }

    // Audit log: attempt to create journal entry
    if (function_exists('audit_log')) {
        audit_log($db, 'user', $postedBy, 'attempt_create_journal_entry', 'journal_entries', $sourceId, json_encode(['memo'=>$memo,'lines'=>$lines,'sourceType'=>$sourceType]));
    }

    $totalDebit = 0;
    $totalCredit = 0;
    foreach ($lines as $line) {
        $debit = isset($line['debit']) ? (float)$line['debit'] : 0;
        $credit = isset($line['credit']) ? (float)$line['credit'] : 0;
        $totalDebit += $debit;
        $totalCredit += $credit;
    }

    if (round($totalDebit, 2) !== round($totalCredit, 2)) {
        return ['ok' => false, 'message' => 'Debits and credits must be equal'];
    }

    $entryDateEsc = mysqli_real_escape_string($db, $entryDate);
    // If this is a payment_history entry, try to fetch package name for memo
    if ($sourceType === 'payment_history' && is_numeric($sourceId)) {
        $phQ = mysqli_query($db, "SELECT amount, plan, fullname FROM payment_history WHERE id='" . intval($sourceId) . "'");
        if ($phQ && ($phRow = mysqli_fetch_assoc($phQ))) {
            $amount = $phRow['amount'];
            $plan = $phRow['plan'];
            $fullname = $phRow['fullname'];
            $packageQ = mysqli_query($db, "SELECT packagename FROM packages WHERE amount='" . mysqli_real_escape_string($db, $amount) . "' AND duration='" . mysqli_real_escape_string($db, $plan) . "' LIMIT 1");
            if ($packageQ && ($prow = mysqli_fetch_assoc($packageQ))) {
                $packageName = $prow['packagename'];
                $memo = 'Payment for ' . $packageName . ' - ' . $fullname;
            }
        }
    }
    $memoEsc = mysqli_real_escape_string($db, $memo);
    $sourceTypeEsc = mysqli_real_escape_string($db, $sourceType);
    $sourceIdEsc = mysqli_real_escape_string($db, $sourceId);
    $postedByEsc = mysqli_real_escape_string($db, $postedBy);
    $periodYear = (int)date('Y', strtotime($entryDate));


    mysqli_begin_transaction($db);
    try {
        $paymentHistoryId = null;
        if ($sourceType === 'payment_history') {
            $paymentHistoryId = $sourceId;
        }
        $branchIdVal = $branchId > 0 ? (int)$branchId : (isset($_SESSION['branch_id']) && $_SESSION['branch_id'] > 0 ? (int)$_SESSION['branch_id'] : 0);
        $q = "INSERT INTO journal_entries(entry_date, period_year, memo, source_type, source_id, is_adjustment, is_closing, status, posted_by, payment_history_id, branch_id)"
              . " VALUES('$entryDateEsc', '$periodYear', '$memoEsc', '$sourceTypeEsc', '$sourceIdEsc', " . (int)$isAdjustment . ", " . (int)$isClosing . ", 'posted', '$postedByEsc', " . ($paymentHistoryId !== null ? (int)$paymentHistoryId : 'NULL') . ", $branchIdVal)";
        if (!mysqli_query($db, $q)) {
            throw new Exception(mysqli_error($db));
        }

        $entryId = (int)mysqli_insert_id($db);

        foreach ($lines as $line) {
            $accCode = isset($line['account_code']) ? $line['account_code'] : '';
            $accId = acc_account_id_by_code($db, $accCode);
            if ($accId <= 0) {
                throw new Exception('Account code not found: ' . $accCode);
            }

            $debit = isset($line['debit']) ? (float)$line['debit'] : 0;
            $credit = isset($line['credit']) ? (float)$line['credit'] : 0;
            $lineMemo = isset($line['line_memo']) ? mysqli_real_escape_string($db, $line['line_memo']) : '';

            $lineQ = "INSERT INTO journal_lines(journal_entry_id, account_id, debit, credit, line_memo)
                      VALUES('$entryId', '$accId', '$debit', '$credit', '$lineMemo')";
            if (!mysqli_query($db, $lineQ)) {
                throw new Exception(mysqli_error($db));
            }
        }

        mysqli_commit($db);
        acc_log_transaction_lines($db, $entryDate, $sourceType, $sourceId, $sourceId, $memo, $lines, $entryId, $paymentHistoryId, $branchIdVal);

        // Audit log: successful journal entry creation
        if (function_exists('audit_log')) {
            audit_log($db, 'user', $postedBy, 'create_journal_entry', 'journal_entries', $entryId, json_encode(['memo'=>$memo,'lines'=>$lines,'sourceType'=>$sourceType]));
        }

        return ['ok' => true, 'entry_id' => $entryId, 'message' => 'Journal entry posted'];
    } catch (Exception $ex) {
        mysqli_rollback($db);
        // Audit log: failed journal entry creation
        if (function_exists('audit_log')) {
            audit_log($db, 'user', $postedBy, 'fail_create_journal_entry', 'journal_entries', $sourceId, json_encode(['memo'=>$memo,'lines'=>$lines,'sourceType'=>$sourceType,'error'=>$ex->getMessage()]));
        }
        return ['ok' => false, 'message' => $ex->getMessage()];
    }
}

function acc_create_entry_once($db, $entryDate, $memo, $sourceType, $sourceId, $lines, $isAdjustment = 0, $branchId = 0, $isClosing = 0, $postedBy = 'System')
{
    if (!$isAdjustment && !$isClosing && acc_source_already_posted($db, $sourceType, $sourceId)) {
        return ['ok' => true, 'skipped' => true, 'message' => 'Source already posted'];
    }

    return acc_create_entry($db, $entryDate, $memo, $sourceType, $sourceId, $lines, $isAdjustment, $branchId, $isClosing, $postedBy);
}

function acc_trial_balance_rows($db, $includeAdjustments = 1, $uptoDate = null, $year = null)
{
    $whereSegments = ["je.status='posted'"];
    if (!$includeAdjustments) {
        $whereSegments[] = "je.is_adjustment=0";
    }
    
    // Base filter for permanent accounts (Asset, Liability, Equity)
    $permanentWhere = "je2.status='posted'";
    if (!$includeAdjustments) {
        $permanentWhere .= " AND je2.is_adjustment=0";
    }
    if (!empty($uptoDate)) {
        $safeDate = mysqli_real_escape_string($db, $uptoDate);
        $permanentWhere .= " AND je2.entry_date <= '$safeDate'";
    } elseif (!empty($year)) {
        $permanentWhere .= " AND je2.period_year <= " . (int)$year;
    }

    // Filter for temporary accounts (Revenue, Expense) - only current period and NO CLOSING ENTRIES
    $temporaryWhere = "je2.status='posted' AND je2.is_closing=0";
    if (!$includeAdjustments) {
        $temporaryWhere .= " AND je2.is_adjustment=0";
    }
    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
    if ($branch_id > 0) {
        $whereSegments[] = "je.branch_id = $branch_id";
        $permanentWhere .= " AND je2.branch_id = $branch_id";
        $temporaryWhere .= " AND je2.branch_id = $branch_id";
    }

    if (!empty($year)) {
        $temporaryWhere .= " AND je2.period_year = " . (int)$year;
    } elseif (!empty($uptoDate)) {
        $yearFromDate = (int)date('Y', strtotime($uptoDate));
        $temporaryWhere .= " AND je2.period_year = $yearFromDate AND je2.entry_date <= '" . mysqli_real_escape_string($db, $uptoDate) . "'";
    }

    $sql = "SELECT coa.code, coa.name, coa.account_type, coa.normal_side,
                   CASE 
                     WHEN coa.account_type IN ('Revenue', 'Expense') THEN 
                       (SELECT COALESCE(SUM(jl2.debit),0) FROM journal_lines jl2 JOIN journal_entries je2 ON je2.id=jl2.journal_entry_id WHERE jl2.account_id=coa.id AND $temporaryWhere)
                     ELSE 
                       (SELECT COALESCE(SUM(jl2.debit),0) FROM journal_lines jl2 JOIN journal_entries je2 ON je2.id=jl2.journal_entry_id WHERE jl2.account_id=coa.id AND $permanentWhere)
                   END as total_debit,
                   CASE 
                     WHEN coa.account_type IN ('Revenue', 'Expense') THEN 
                       (SELECT COALESCE(SUM(jl2.credit),0) FROM journal_lines jl2 JOIN journal_entries je2 ON je2.id=jl2.journal_entry_id WHERE jl2.account_id=coa.id AND $temporaryWhere)
                     ELSE 
                       (SELECT COALESCE(SUM(jl2.credit),0) FROM journal_lines jl2 JOIN journal_entries je2 ON je2.id=jl2.journal_entry_id WHERE jl2.account_id=coa.id AND $permanentWhere)
                   END as total_credit
            FROM chart_of_accounts coa
            ORDER BY coa.code ASC";

    $res = mysqli_query($db, $sql);
    $rows = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $debit = (float)$row['total_debit'];
            $credit = (float)$row['total_credit'];
            $net = $debit - $credit;
            
            $row['trial_debit'] = ($net > 0) ? $net : 0;
            $row['trial_credit'] = ($net < 0) ? abs($net) : 0;
            $rows[] = $row;
        }
    }
    return $rows;
}

function acc_income_statement($db, $fromDate, $toDate)
{
    $from = mysqli_real_escape_string($db, $fromDate);
    $to = mysqli_real_escape_string($db, $toDate);

    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
    $branch_where = $branch_id > 0 ? " AND je.branch_id = $branch_id " : "";

    $revSql = "SELECT coa.code, coa.name, COALESCE(SUM(jl.credit - jl.debit),0) amount
               FROM chart_of_accounts coa
               JOIN journal_lines jl ON jl.account_id = coa.id
               JOIN journal_entries je ON je.id = jl.journal_entry_id
               WHERE coa.account_type='Revenue' AND je.status='posted' 
                 AND je.is_closing=0 
                 AND je.entry_date BETWEEN '$from' AND '$to' $branch_where
               GROUP BY coa.id
               HAVING amount <> 0
               ORDER BY coa.code";

    $expSql = "SELECT coa.code, coa.name, COALESCE(SUM(jl.debit - jl.credit),0) amount
               FROM chart_of_accounts coa
               JOIN journal_lines jl ON jl.account_id = coa.id
               JOIN journal_entries je ON je.id = jl.journal_entry_id
               WHERE coa.account_type='Expense' AND je.status='posted' 
                 AND je.is_closing=0 
                 AND je.entry_date BETWEEN '$from' AND '$to' $branch_where
               GROUP BY coa.id
               HAVING amount <> 0
               ORDER BY coa.code";

    $revenues = [];
    $expenses = [];
    $totalRevenue = 0;
    $totalExpense = 0;

    $r1 = mysqli_query($db, $revSql);
    if ($r1) {
        while ($row = mysqli_fetch_assoc($r1)) {
            $row['amount'] = (float)$row['amount'];
            $totalRevenue += $row['amount'];
            $revenues[] = $row;
        }
    }

    $r2 = mysqli_query($db, $expSql);
    if ($r2) {
        while ($row = mysqli_fetch_assoc($r2)) {
            $row['amount'] = (float)$row['amount'];
            $totalExpense += $row['amount'];
            $expenses[] = $row;
        }
    }

    return [
        'revenues' => $revenues,
        'expenses' => $expenses,
        'total_revenue' => $totalRevenue,
        'total_expense' => $totalExpense,
        'net_income' => $totalRevenue - $totalExpense
    ];
}

function acc_balance_sheet($db, $asOfDate)
{
    $date = mysqli_real_escape_string($db, $asOfDate);
    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
    $branch_where = $branch_id > 0 ? " AND je.branch_id = $branch_id " : "";
    $sql = "SELECT coa.code, coa.name, coa.account_type,
                   COALESCE(SUM(jl.debit),0) total_debit,
                   COALESCE(SUM(jl.credit),0) total_credit
            FROM chart_of_accounts coa
            LEFT JOIN journal_lines jl ON jl.account_id = coa.id
            LEFT JOIN journal_entries je ON je.id = jl.journal_entry_id
            WHERE je.status='posted' AND je.entry_date <= '$date' $branch_where
            GROUP BY coa.id
            ORDER BY coa.code";

    $res = mysqli_query($db, $sql);
    $assets = [];
    $liabilities = [];
    $equity = [];
    $totalAssets = 0;
    $totalLiabilities = 0;
    $totalEquity = 0;

    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $type = $row['account_type'];
            $debit = (float)$row['total_debit'];
            $credit = (float)$row['total_credit'];
            $amount = 0;

            if ($type === 'Asset') {
                $amount = $debit - $credit;
                if (abs($amount) > 0.009) {
                    $row['amount'] = $amount;
                    $assets[] = $row;
                    $totalAssets += $amount;
                }
            } elseif ($type === 'Liability') {
                $amount = $credit - $debit;
                if (abs($amount) > 0.009) {
                    $row['amount'] = $amount;
                    $liabilities[] = $row;
                    $totalLiabilities += $amount;
                }
            } elseif ($type === 'Equity') {
                $amount = $credit - $debit;
                if (abs($amount) > 0.009) {
                    $row['amount'] = $amount;
                    $equity[] = $row;
                    $totalEquity += $amount;
                }
            }
        }
    }

    return [
        'assets' => $assets,
        'liabilities' => $liabilities,
        'equity' => $equity,
        'total_assets' => $totalAssets,
        'total_liabilities' => $totalLiabilities,
        'total_equity' => $totalEquity
    ];
}

function acc_close_year($db, $year, $postedBy = 'Admin', $branch_id = 0)
{
    $year = (int)$year;
    $start = $year . '-01-01';
    $end = $year . '-12-31';

    $branch_where = $branch_id > 0 ? " AND branch_id = $branch_id " : "";
    $exists = mysqli_query($db, "SELECT id FROM journal_entries WHERE period_year='$year' AND is_closing=1 $branch_where LIMIT 1");
    if ($exists && mysqli_num_rows($exists) > 0) {
        return ['ok' => false, 'message' => 'Year already closed for this branch'];
    }

    $revRes = mysqli_query($db, "SELECT coa.code, COALESCE(SUM(jl.credit - jl.debit),0) bal
                                FROM chart_of_accounts coa
                                JOIN journal_lines jl ON jl.account_id = coa.id
                                JOIN journal_entries je ON je.id = jl.journal_entry_id
                                WHERE coa.account_type='Revenue' AND je.status='posted' AND je.entry_date BETWEEN '$start' AND '$end' $branch_where
                                GROUP BY coa.id HAVING bal <> 0");

    $expRes = mysqli_query($db, "SELECT coa.code, COALESCE(SUM(jl.debit - jl.credit),0) bal
                                FROM chart_of_accounts coa
                                JOIN journal_lines jl ON jl.account_id = coa.id
                                JOIN journal_entries je ON je.id = jl.journal_entry_id
                                WHERE coa.account_type='Expense' AND je.status='posted' AND je.entry_date BETWEEN '$start' AND '$end' $branch_where
                                GROUP BY coa.id HAVING bal <> 0");

    $lines = [];
    $totalRevenue = 0;
    $totalExpense = 0;

    if ($revRes) {
        while ($r = mysqli_fetch_assoc($revRes)) {
            $bal = (float)$r['bal'];
            $totalRevenue += $bal;
            $lines[] = [
                'account_code' => $r['code'],
                'debit' => $bal,
                'credit' => 0,
                'line_memo' => 'Close revenue ' . $r['code']
            ];
        }
    }

    if ($expRes) {
        while ($r = mysqli_fetch_assoc($expRes)) {
            $bal = (float)$r['bal'];
            $totalExpense += $bal;
            $lines[] = [
                'account_code' => $r['code'],
                'debit' => 0,
                'credit' => $bal,
                'line_memo' => 'Close expense ' . $r['code']
            ];
        }
    }

    $netIncome = $totalRevenue - $totalExpense;
    if ($netIncome > 0) {
        $lines[] = [
            'account_code' => '3100',
            'debit' => 0,
            'credit' => $netIncome,
            'line_memo' => 'Transfer net income to retained earnings'
        ];
    } elseif ($netIncome < 0) {
        $lines[] = [
            'account_code' => '3100',
            'debit' => abs($netIncome),
            'credit' => 0,
            'line_memo' => 'Transfer net loss to retained earnings'
        ];
    }

    if (count($lines) === 0) {
        return ['ok' => false, 'message' => 'No revenue or expense balances to close'];
    }

    $memo = 'Year-end closing entries for ' . $year;
    $res = acc_create_entry($db, $end, $memo, 'closing', (string)$year, $lines, 0, $branch_id, 1, $postedBy);
    if (!$res['ok']) {
        return $res;
    }

    $postedByEsc = mysqli_real_escape_string($db, $postedBy);
    mysqli_query($db, "INSERT INTO fiscal_periods(period_year, start_date, end_date, status, closed_at, closed_by)
                      VALUES('$year', '$start', '$end', 'closed', NOW(), '$postedByEsc')
                      ON DUPLICATE KEY UPDATE status='closed', closed_at=NOW(), closed_by='$postedByEsc'");

    return ['ok' => true, 'message' => 'Year closed successfully', 'entry_id' => $res['entry_id'], 'net_income' => $netIncome];
}

function acc_expense_account_code_from_category($category)
{
    $cat = strtolower(trim((string)$category));
    if ($cat === 'salaries') {
        return '5000';
    }
    if ($cat === 'bills') {
        return '5200';
    }
    if ($cat === 'maintenance' || $cat === 'equipment') {
        return '5300';
    }
    if ($cat === 'marketing') {
        return '5400';
    }
    return '5500';
}

function acc_sync_payment_history($db, $targetDate = null, $postedBy = 'Admin', $branch_id = 0)
{
    if (!$targetDate) $targetDate = date('Y-m-d');
    $summary = ['created' => 0, 'skipped' => 0, 'failed' => 0, 'messages' => []];
    $branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
    $res = mysqli_query($db, "SELECT id, invoice_no, fullname, plan, amount, paid_amount, discount_amount, paid_date, branch_id FROM payment_history $branch_where ORDER BY id ASC");
    if (!$res) {
        $summary['failed']++;
        $summary['messages'][] = mysqli_error($db);
        return $summary;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $sourceId = (string)$row['id'];
        $entryDate = !empty($row['paid_date']) ? $row['paid_date'] : date('Y-m-d');
        $invoiceNo = (string)($row['invoice_no'] ?? '');
        $amount = (float)($row['paid_amount'] ?? 0);
        if ($amount <= 0) {
            $summary['skipped']++;
            continue;
        }
        $discount = (float)($row['discount_amount'] ?? 0);
        $gross_amount = (!empty($row['amount']) && (float)$row['amount'] > 0) ? (float)$row['amount'] : $amount;
        $receivable = $gross_amount - $discount - $amount; // gross - discount - paid
        if ($receivable < 0) $receivable = 0;

        // Ensure gross_amount is at least the amount actually paid to avoid negative unearned revenue anomalies
        if ($gross_amount < ($amount + $discount)) {
            $gross_amount = $amount + $discount;
        }

        // --- ADVANCE PAYMENT LOGIC (ACCRUAL) ---
        $plan_months = (isset($row['plan']) && (int)$row['plan'] > 0) ? (int)$row['plan'] : 1;
        $monthly_fee = $gross_amount / $plan_months;
        
        // Calculate how many months have passed since paid_date until targetDate
        $d1 = new DateTime($entryDate);
        $d2 = new DateTime($targetDate);
        $interval = $d1->diff($d2);
        $monthsSince = ($interval->invert) ? 0 : ($interval->y * 12) + $interval->m + 1;
        if ($entryDate > $targetDate) $monthsSince = 0;

        $earnedMonths = min((int)$plan_months, (int)$monthsSince);
        $revenue = $monthly_fee * $earnedMonths;
        $liability = $gross_amount - $revenue; 
        if ($liability < 0) $liability = 0;

        $memo = 'Synced member payment ' . ($invoiceNo !== '' ? $invoiceNo : ('PH-' . $sourceId));
        $lines = [
            ['account_code' => '1000', 'debit' => $amount, 'credit' => 0, 'line_memo' => $memo]
        ];
        
        if ($receivable > 0) {
            $lines[] = ['account_code' => '1100', 'debit' => $receivable, 'credit' => 0, 'line_memo' => $memo . ' (Accounts Receivable)'];
        }
        if ($discount > 0) {
            $lines[] = ['account_code' => '4100', 'debit' => $discount, 'credit' => 0, 'line_memo' => $memo . ' (Discount/Contra-Revenue)'];
        }
        
        if ($revenue > 0) {
            $lines[] = ['account_code' => '4000', 'debit' => 0, 'credit' => $revenue, 'line_memo' => $memo . ' (Earned Revenue)'];
        }
        if ($liability > 0) {
            $lines[] = ['account_code' => '2200', 'debit' => 0, 'credit' => $liability, 'line_memo' => $memo . ' (Unearned/Advance Liability)'];
        }

        $result = acc_create_entry_once(
            $db,
            $entryDate,
            $memo,
            'payment_history',
            $sourceId,
            $lines,
            0,
            (int)($row['branch_id'] ?? 0),
            0,
            $postedBy
        );

        if (!empty($result['ok']) && empty($result['skipped'])) {
            $summary['created']++;
        } elseif (!empty($result['skipped'])) {
            $summary['skipped']++;
        } else {
            $summary['failed']++;
            $summary['messages'][] = 'Payment #' . $sourceId . ': ' . ($result['message'] ?? 'Unknown error');
        }
    }

    return $summary;
}

function acc_sync_expenses($db, $postedBy = 'Admin', $branch_id = 0)
{
    $summary = ['created' => 0, 'skipped' => 0, 'failed' => 0, 'messages' => []];
    $branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
    $res = mysqli_query($db, "SELECT id, name, category, amount, date, branch_id FROM expenses $branch_where ORDER BY id ASC");
    if (!$res) {
        $summary['failed']++;
        $summary['messages'][] = mysqli_error($db);
        return $summary;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $sourceId = (string)$row['id'];
        $entryDate = !empty($row['date']) ? $row['date'] : date('Y-m-d');
        $amount = (float)($row['amount'] ?? 0);
        if ($amount <= 0) {
            $summary['skipped']++;
            continue;
        }
        $expenseCode = acc_expense_account_code_from_category($row['category'] ?? '');
        $memo = 'Synced expense ' . (string)($row['name'] ?? ('EXP-' . $sourceId));

        // Audit log: expense sync
        if (function_exists('audit_log')) {
            audit_log($db, 'user', $postedBy, 'sync_expense', 'expenses', $sourceId, json_encode(['amount'=>$amount,'date'=>$entryDate]));
        }

        $result = acc_create_entry_once(
            $db,
            $entryDate,
            $memo,
            'expense',
            $sourceId,
            [
                ['account_code' => $expenseCode, 'debit' => $amount, 'credit' => 0, 'line_memo' => $memo],
                ['account_code' => '1000', 'debit' => 0, 'credit' => $amount, 'line_memo' => $memo]
            ],
            0,
            (int)($row['branch_id'] ?? 0),
            0,
            $postedBy
        );

        if (!empty($result['ok']) && empty($result['skipped'])) {
            $summary['created']++;
        } elseif (!empty($result['skipped'])) {
            $summary['skipped']++;
        } else {
            $summary['failed']++;
            $summary['messages'][] = 'Expense #' . $sourceId . ': ' . ($result['message'] ?? 'Unknown error');
        }
    }

    return $summary;
}

function acc_sync_equipment($db, $postedBy = 'Admin', $branch_id = 0)
{
    $summary = ['created' => 0, 'skipped' => 0, 'failed' => 0, 'messages' => []];
    $branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
    $res = mysqli_query($db, "SELECT id, name, amount, date, branch_id FROM equipment $branch_where ORDER BY id ASC");
    if (!$res) {
        $summary['failed']++;
        $summary['messages'][] = mysqli_error($db);
        return $summary;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $sourceId = (string)$row['id'];
        $entryDate = !empty($row['date']) ? $row['date'] : date('Y-m-d');
        $amount = (float)($row['amount'] ?? 0);
        if ($amount <= 0) {
            $summary['skipped']++;
            continue;
        }
        $memo = 'Synced owner-funded equipment purchase ' . (string)($row['name'] ?? ('EQ-' . $sourceId));

        // Audit log: equipment sync
        if (function_exists('audit_log')) {
            audit_log($db, 'user', $postedBy, 'sync_equipment', 'equipment', $sourceId, json_encode(['amount'=>$amount,'date'=>$entryDate]));
        }

        $result = acc_create_entry_once(
            $db,
            $entryDate,
            $memo,
            'equipment',
            $sourceId,
            [
                ['account_code' => '1500', 'debit' => $amount, 'credit' => 0, 'line_memo' => $memo],
                ['account_code' => '3000', 'debit' => 0, 'credit' => $amount, 'line_memo' => $memo]
            ],
            0,
            (int)($row['branch_id'] ?? 0),
            0,
            $postedBy
        );

        if (!empty($result['ok']) && empty($result['skipped'])) {
            $summary['created']++;
        } elseif (!empty($result['skipped'])) {
            $summary['skipped']++;
        } else {
            $summary['failed']++;
            $summary['messages'][] = 'Equipment #' . $sourceId . ': ' . ($result['message'] ?? 'Unknown error');
        }
    }

    return $summary;
}

function acc_rebuild_payment_history($db, $postedBy = 'Admin', $branch_id = 0)
{
    $summary = ['created' => 0, 'skipped' => 0, 'failed' => 0, 'messages' => []];
    $branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
    $res = mysqli_query($db, "SELECT id FROM payment_history $branch_where ORDER BY id ASC");
    if (!$res) {
        $summary['failed']++;
        $summary['messages'][] = mysqli_error($db);
        return $summary;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $sourceId = (string)$row['id'];
        $deleteRes = acc_delete_source_postings($db, 'payment_history', $sourceId);
        if (empty($deleteRes['ok'])) {
            $summary['failed']++;
            $summary['messages'][] = 'Payment #' . $sourceId . ': ' . ($deleteRes['message'] ?? 'Delete failed');
            continue;
        }
    }

    return acc_sync_payment_history($db, date('Y-m-d'), $postedBy, $branch_id);
}

function acc_rebuild_expenses($db, $postedBy = 'Admin', $branch_id = 0)
{
    $summary = ['created' => 0, 'skipped' => 0, 'failed' => 0, 'messages' => []];
    $branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
    $res = mysqli_query($db, "SELECT id FROM expenses $branch_where ORDER BY id ASC");
    if (!$res) {
        $summary['failed']++;
        $summary['messages'][] = mysqli_error($db);
        return $summary;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $sourceId = (string)$row['id'];
        $deleteRes = acc_delete_source_postings($db, 'expense', $sourceId);
        if (empty($deleteRes['ok'])) {
            $summary['failed']++;
            $summary['messages'][] = 'Expense #' . $sourceId . ': ' . ($deleteRes['message'] ?? 'Delete failed');
            continue;
        }
    }

    return acc_sync_expenses($db, $postedBy, $branch_id);
}

function acc_rebuild_equipment($db, $postedBy = 'Admin', $branch_id = 0)
{
    $summary = ['created' => 0, 'skipped' => 0, 'failed' => 0, 'messages' => []];
    $branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
    $res = mysqli_query($db, "SELECT id FROM equipment $branch_where ORDER BY id ASC");
    if (!$res) {
        $summary['failed']++;
        $summary['messages'][] = mysqli_error($db);
        return $summary;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $sourceId = (string)$row['id'];
        $deleteRes = acc_delete_source_postings($db, 'equipment', $sourceId);
        if (empty($deleteRes['ok'])) {
            $summary['failed']++;
            $summary['messages'][] = 'Equipment #' . $sourceId . ': ' . ($deleteRes['message'] ?? 'Delete failed');
            continue;
        }
    }

    return acc_sync_equipment($db, $postedBy, $branch_id);
}

function acc_sync_owner_capital($db, $postedBy = 'Admin', $branch_id = 0)
{
    $summary = ['created' => 0, 'skipped' => 0, 'failed' => 0, 'messages' => []];
    $branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
    $res = mysqli_query($db, "SELECT id, contribution_date, amount, reference_no, notes, branch_id FROM owner_capital_contributions $branch_where ORDER BY id ASC");
    if (!$res) {
        $summary['failed']++;
        $summary['messages'][] = mysqli_error($db);
        return $summary;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $sourceId = (string)$row['id'];
        $amount = (float)($row['amount'] ?? 0);
        if ($amount <= 0) {
            $summary['skipped']++;
            continue;
        }
        $memo = 'Synced owner capital contribution' . ($row['reference_no'] !== '' ? ' ' . $row['reference_no'] : ' #' . $sourceId);
        if (!empty($row['notes'])) $memo .= ' - ' . $row['notes'];

        $result = acc_create_entry_once(
            $db,
            $row['contribution_date'],
            $memo,
            'owner_capital',
            $sourceId,
            [
                ['account_code' => '1000', 'debit' => $amount, 'credit' => 0, 'line_memo' => $memo],
                ['account_code' => '3000', 'debit' => 0, 'credit' => $amount, 'line_memo' => $memo]
            ],
            0,
            (int)($row['branch_id'] ?? 0),
            0,
            $postedBy
        );

        if (!empty($result['ok']) && empty($result['skipped'])) {
            $summary['created']++;
        } elseif (!empty($result['skipped'])) {
            $summary['skipped']++;
        } else {
            $summary['failed']++;
            $summary['messages'][] = 'Capital #' . $sourceId . ': ' . ($result['message'] ?? 'Unknown error');
        }
    }
    return $summary;
}

function acc_rebuild_owner_capital($db, $postedBy = 'Admin', $branch_id = 0)
{
    $branch_where = $branch_id > 0 ? " WHERE branch_id = $branch_id " : "";
    $res = mysqli_query($db, "SELECT id FROM owner_capital_contributions $branch_where ORDER BY id ASC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            acc_delete_source_postings($db, 'owner_capital', (string)$row['id']);
        }
    }
    return acc_sync_owner_capital($db, $postedBy, $branch_id);
}

function acc_record_owner_capital($db, $contributionDate, $amount, $referenceNo = '', $notes = '', $fundedBy = 'Owner', $postedBy = 'Admin', $branch_id = 0)
{
    $amount = (float)$amount;
    if ($amount <= 0) {
        return ['ok' => false, 'message' => 'Contribution amount must be greater than zero'];
    }

    $dateEsc = mysqli_real_escape_string($db, $contributionDate);
    $refEsc = mysqli_real_escape_string($db, $referenceNo);
    $notesEsc = mysqli_real_escape_string($db, $notes);
    $fundedByEsc = mysqli_real_escape_string($db, $fundedBy);

    $branch_id = $branch_id > 0 ? (int)$branch_id : (isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0);
    $insert = mysqli_query(
        $db,
        "INSERT INTO owner_capital_contributions(contribution_date, amount, reference_no, notes, funded_by, branch_id)
         VALUES('$dateEsc', '$amount', '$refEsc', '$notesEsc', '$fundedByEsc', $branch_id)"
    );
    if (!$insert) {
        return ['ok' => false, 'message' => mysqli_error($db)];
    }

    $contributionId = (string)mysqli_insert_id($db);
    $memo = 'Owner capital contribution' . ($referenceNo !== '' ? ' ' . $referenceNo : ' #' . $contributionId);
    if ($notes !== '') {
        $memo .= ' - ' . $notes;
    }

    // Audit log: owner capital contribution
    if (function_exists('audit_log')) {
        audit_log($db, 'user', $postedBy, 'owner_capital_contribution', 'owner_capital_contributions', $contributionId, json_encode(['amount'=>$amount,'referenceNo'=>$referenceNo,'notes'=>$notes]));
    }

    $entry = acc_create_entry_once(
        $db,
        $contributionDate,
        $memo,
        'owner_capital',
        $contributionId,
        [
            ['account_code' => '1000', 'debit' => $amount, 'credit' => 0, 'line_memo' => $memo],
            ['account_code' => '3000', 'debit' => 0, 'credit' => $amount, 'line_memo' => $memo]
        ],
        0,
        $branch_id,
        0,
        $postedBy
    );

    if (empty($entry['ok'])) {
        acc_delete_source_postings($db, 'owner_capital', $contributionId);
        mysqli_query($db, "DELETE FROM owner_capital_contributions WHERE id='" . (int)$contributionId . "'");
        return $entry;
    }

    return ['ok' => true, 'contribution_id' => (int)$contributionId, 'entry_id' => $entry['entry_id'] ?? null];
}

/**
 * Synchronize Staff Payroll Accruals (Monthly Liability)
 * Now records BOTH a Debt record and a Journal Entry (Dr 5000, Cr 2100).
 */
function acc_sync_payroll_accruals($db, $postedBy = 'System') {
    // Audit log: payroll accrual (moved inside loop)
    $summary = ['success' => 0, 'failed' => 0, 'messages' => []];
    
    // Get all staff with salary > 0
    $res = mysqli_query($db, "SELECT * FROM staffs WHERE salary > 0");
    if (!$res) return $summary;

    $current_year = (int)date('Y');
    $current_month = (int)date('n');

    while ($staff = mysqli_fetch_assoc($res)) {
        $staff_id = $staff['user_id'];
        $salary = (float)$staff['salary'];
        $created_at = isset($staff['created_at']) ? $staff['created_at'] : null;
        $reg_time = $created_at ? strtotime($created_at) : null;
        $reg_year = $reg_time ? (int)date('Y', $reg_time) : null;
        $reg_month = $reg_time ? (int)date('n', $reg_time) : null;

        for ($m = 0; $m < 3; $m++) {
            $time = strtotime("-$m months");
            $y = (int)date('Y', $time);
            $mon = (int)date('n', $time);

            if ($y > $current_year || ($y == $current_year && $mon > $current_month)) continue;

            // Prevent accrual for months before or the SAME AS or BEFORE registration month
            // Only accrue for months strictly AFTER registration month
            if ($reg_time) {
                if ($y < $reg_year || ($y == $reg_year && $mon <= $reg_month)) {
                    continue;
                }
            }

            $check = mysqli_query($db, "SELECT id, journal_entry_id FROM payroll_accruals WHERE staff_id=$staff_id AND period_year=$y AND period_month=$mon");
            if (mysqli_num_rows($check) > 0) {
                // Check if it has a journal entry, if not, we might need to add one if the user wants it everywhere
                $row = mysqli_fetch_assoc($check);
                if (!empty($row['journal_entry_id'])) continue;
            }

            // Post Accrual
            $memo = "Deynta Mushaharka Shaqalaha Bishii laso dhaafe - " . $staff['fullname'] . " (" . date('F Y', $time) . ")";
            $date = date('Y-m-t', $time);

            $lines = [
                ['account_code' => '5000', 'debit' => $salary, 'credit' => 0, 'line_memo' => $memo],
                ['account_code' => '2100', 'debit' => 0, 'credit' => $salary, 'line_memo' => $memo]
            ];

            // Audit log: payroll accrual (now inside loop, after variables are set)
            if (function_exists('audit_log')) {
                audit_log($db, 'user', $postedBy, 'payroll_accrual', 'payroll_accruals', "{$staff_id}-{$y}-{$mon}", json_encode(['salary'=>$salary,'staff_id'=>$staff_id,'period_year'=>$y,'period_month'=>$mon]));
            }

            $entry = acc_create_entry($db, $date, $memo, 'payroll_accrual', "{$staff_id}-{$y}-{$mon}", $lines, 0, (int)($staff['branch_id'] ?? 0), $postedBy);

            if (!empty($entry['ok'])) {
                $je_id = $entry['entry_id'];
                if (mysqli_num_rows($check) > 0) {
                    mysqli_query($db, "UPDATE payroll_accruals SET journal_entry_id = $je_id, branch_id = ". (int)($staff['branch_id'] ?? 0) ." WHERE staff_id=$staff_id AND period_year=$y AND period_month=$mon");
                } else {
                    mysqli_query($db, "INSERT INTO payroll_accruals (staff_id, period_year, period_month, amount, journal_entry_id, branch_id) 
                                     VALUES ($staff_id, $y, $mon, $salary, $je_id, ". (int)($staff['branch_id'] ?? 0) .")");
                }
                $summary['success']++;
            } else {
                $summary['failed']++;
            }
        }
    }
    return $summary;
}

