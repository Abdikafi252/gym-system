<?php
/**
 * Gym System Localization (Bilingual: Somali & English)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default language is Somali
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'so';
}

// Toggle language if requested
if (isset($_GET['set_lang'])) {
    $allowed_langs = ['en', 'so'];
    if (in_array($_GET['set_lang'], $allowed_langs)) {
        $_SESSION['lang'] = $_GET['set_lang'];
    }
    // Redirect back to same page without the query param
    $clean_url = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: " . $clean_url);
    exit;
}

$translations = [
    'en' => [
        'dashboard' => 'Dashboard',
        'members' => 'Members',
        'members_list' => 'Members List',
        'add_members' => 'Add Members',
        'edit_member' => 'Edit Member',
        'remove_member' => 'Remove Member',
        'member_status' => 'Member Status',
        'attendance' => 'Attendance',
        'manage_attendance' => 'Manage Attendance',
        'attendance_report' => 'Attendance Report',
        'accounting' => 'Accounting',
        'journal' => 'Journal Entries',
        'income_statement' => 'Income Statement',
        'balance_sheet' => 'Balance Sheet',
        'fiscal_periods' => 'Fiscal Periods',
        'yearly_history' => 'Yearly History',
        'payroll' => 'Staff Payroll',
        'announcements' => 'Announcements',
        'staff_management' => 'Staff Management',
        'reports' => 'Reports',
        'logout' => 'Log Out',
        'welcome' => 'Welcome',
        'active_members' => 'Active Members',
        'total_members' => 'Total Members',
        'revenue' => 'Total Revenue',
        'expenses' => 'Total Expenses',
        'net_income' => 'Net Income',
        'pending_balance' => 'Pending Balance',
        'active_members' => 'Active Members',
        'total_announcements' => 'Announcements',
        'available_equipment' => 'Available Equipment',
        'monthly_net_profit' => 'Monthly Net Profit',
        'today_expiry' => 'Today\'s Expiry',
        'trainers' => 'Trainers',
        'today_income' => 'Today\'s Income',
        'weekly_income' => 'Weekly Income',
        'monthly_income' => 'Monthly Income',
        'monthly_expenses' => 'Monthly Expenses',
        'today_renewals' => 'Today\'s Renewals',
        'monthly_renewals' => 'Monthly Renewals',
        'pending_renewals' => 'Pending Renewals',
        'present_members' => 'Present Members',
        'new_registrations' => 'New Registrations',
        'total_staff' => 'Total Staff',
        'search' => 'Search',
        'save' => 'Save Changes',
        'cancel' => 'Cancel',
        'pay_now' => 'Pay Now',
        'language' => 'Language',
        'somali' => 'Somali',
        'english' => 'English'
    ],
    'so' => [
        'dashboard' => 'Dashboard-ka',
        'members' => 'Xubnaha',
        'members_list' => 'Liiska Xubnaha',
        'add_members' => 'Diiwaangeli Member',
        'edit_member' => 'Bedel Member',
        'remove_member' => 'Tir Member',
        'member_status' => 'Xaaladda Xubnaha',
        'attendance' => 'Imaanshaha',
        'manage_attendance' => 'Maamul Imaanshaha',
        'attendance_report' => 'Warbixinta Imaanshaha',
        'accounting' => 'Accounting (Xisaab)',
        'journal' => 'Journal Entries',
        'income_statement' => 'Income Statement',
        'balance_sheet' => 'Balance Sheet',
        'fiscal_periods' => 'Xiritaanka Sanadka',
        'yearly_history' => 'Diiwaanka Xisaabta',
        'payroll' => 'Mushaharka Shaqaalaha',
        'announcements' => 'Ogeysiisyada',
        'staff_management' => 'Maamulka Shaqaalaha',
        'reports' => 'Warbixinnada',
        'logout' => 'Ka Bax Nidaamka',
        'welcome' => 'Soo Dhawoow',
        'active_members' => 'Xubnaha Firfircoon',
        'total_members' => 'Warta Xubnaha',
        'revenue' => 'Dakhliga Guud',
        'expenses' => 'Kharashka Guud',
        'net_income' => 'Faa\'iidada Saafiga ah',
        'pending_balance' => 'Baqiga dhiman',
        'active_members' => 'Xubnaha Firfircoon',
        'total_announcements' => 'Ogeysiisyada',
        'available_equipment' => 'Qalabka Diyaarka ah',
        'monthly_net_profit' => 'Faa\'iidada Bishan',
        'today_expiry' => 'Kuwa Maanta dhacay',
        'trainers' => 'Tababarayaasha',
        'today_income' => 'Dakhliga Maanta',
        'weekly_income' => 'Dakhliga Toddobaadka',
        'monthly_income' => 'Dakhliga Bishan',
        'monthly_expenses' => 'Kharashka Bishan',
        'today_renewals' => 'Cusboonaysiinta Maanta',
        'monthly_renewals' => 'Cusboonaysiinta Bishan',
        'pending_renewals' => 'Cusboonaysiinta dhiman',
        'present_members' => 'Xubnaha Maanta yimid',
        'new_registrations' => 'Diiwaangalinta Cusub',
        'total_staff' => 'Warta Shaqaalaha',
        'search' => 'Raadi',
        'save' => 'Keydi Isbedelka',
        'cancel' => 'Iska Daay',
        'pay_now' => 'Bixi Mushaharka',
        'language' => 'Luuqadda',
        'somali' => 'Af Soomaali',
        'english' => 'English'
    ]
];

/**
 * Helper function to translate a key
 */
function __($key) {
    global $translations;
    $lang = $_SESSION['lang'] ?? 'so';
    return $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;
}
?>
