<?php
include('../connection/conn.php');
require_once '../vendor/autoload.php';
$db = new DatabaseConnector();
$month = isset($_GET['mt']) ? htmlspecialchars($_GET['mt'], ENT_QUOTES, 'UTF-8') : date('n');
$currentYear = isset($_GET['yr']) ? htmlspecialchars($_GET['yr'], ENT_QUOTES, 'UTF-8') : date('Y');

$monthList = [
    "1" => "January",
    "2" => "February",
    "3" => "March",
    "4" => "April",
    "5" => "May",
    "6" => "June",
    "7" => "July",
    "8" => "August",
    "9" => "September",
    "10" => "October",
    "11" => "November",
    "12" => "December",
];

$monthlyReportSql = "SELECT UPPER(d.Department_Description) AS Department, UPPER(c.Campus_Description) AS Campus,
        (
            SELECT COUNT(f_sub.File_No) 
            FROM files f_sub
            LEFT JOIN account_user a_sub ON f_sub.Sender = a_sub.User_No
            WHERE a_sub.Department = d.Department_No
            AND MONTH(f_sub.Date_Created) = ?
            AND YEAR(f_sub.Date_Created) = ?
        ) AS Total_Incoming,
        (
            SELECT COUNT(f_sub.File_No) 
            FROM files f_sub
            LEFT JOIN account_user a_sub ON f_sub.Sender = a_sub.User_No
            WHERE f_sub.Status = 'APPROVED'
            AND a_sub.Department = d.Department_No
            AND MONTH(f_sub.Date_Created) = ?
            AND YEAR(f_sub.Date_Created) = ?
        ) AS Total_Completed,
        (
            SELECT COUNT(f_sub.File_No) 
            FROM files f_sub
            LEFT JOIN account_user a_sub ON f_sub.Sender = a_sub.User_No
            WHERE f_sub.Status = 'DISSAPPROVED'
            AND a_sub.Department = d.Department_No
            AND MONTH(f_sub.Date_Created) = ?
            AND YEAR(f_sub.Date_Created) = ?
        ) AS Total_Declined
        FROM department d
        LEFT JOIN account_user a ON d.Department_No = a.Department
        LEFT JOIN campus c ON d.Campus = c.Campus_No
        LEFT JOIN files f ON f.Sender = a.User_No
        GROUP BY d.Department_Description,c.Campus_Description, Total_Incoming, Total_Completed, Total_Declined
        ORDER BY d.Department_Description;";
$monthlyReportResult = $db->fetchAll($monthlyReportSql, [$month,$currentYear,$month,$currentYear,$month,$currentYear]);

$reportTotal = "SELECT 
                (
                    SELECT COUNT(File_No) 
                    FROM files
                    WHERE MONTH(Date_Created) = ?
                    AND YEAR(Date_Created) = ?
                ) AS Total_Incoming,
                (
                    SELECT COUNT(File_No) 
                    FROM files
                    WHERE `Status` = 'APPROVED' 
                    AND MONTH(Date_Created) = ?
                    AND YEAR(Date_Created) = ?
                ) AS Total_Completed,
                (
                    SELECT COUNT(File_No) 
                    FROM files
                    WHERE `Status` = 'DISSAPPROVED' 
                    AND MONTH(Date_Created) = ?
                    AND YEAR(Date_Created) = ?
                ) AS Total_Declined";
$reportTotalResult = $db->fetchAll($reportTotal, [$month, $currentYear, $month, $currentYear, $month, $currentYear]);

// Initialize mPDF
$mpdf = new \Mpdf\Mpdf();

// Set header with image
$header = '<div style="text-align: center; padding-bottom: 10px;">
    <img src="../assets/img/vpaa_header.png" style="max-width: 100%;" alt="Header Image">
</div>';
$mpdf->SetHTMLHeader($header);

// Set the footer that will appear on every page
$footer = '<div style="text-align: left; font-size: 10px; font-style: italic; border-top: 1px solid #ccc; padding-top: 5px;">
    This report is system-generated and does not require a signature. For any discrepancies, please contact VPAA Unit - System Administrator.
</div>';
$mpdf->SetHTMLFooter($footer);

// HTML structure for the report
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monthly Report</title>
<style>
body {
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #333;
    margin: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
}
table, th, td {
    border: 1px solid black;
}
th, td {
    padding: 8px;
    text-align: left;
}
tr .text-center {
    text-align: center !important;
    width: 80px;
}
h1 {
    text-align: center;
}
</style>
</head>
<body>
<hr style="margin-top:100px;">
<h2 style="text-align:center;">VPAA Document Management System Monthly Report</h2>
<div style="margin-bottom:15px;">
<h4 style="margin:0;">Month: <span style="color:green">'.strtoupper($monthList[$month]).'</span></h4>
<h4 style="margin:0;">Year: <span style="color:green">'.$currentYear.'</span></h4>
</div>
<table>
    <tr>
        <th>Total No. Of Incoming Files</th>
        <th class="text-center">'.htmlspecialchars($reportTotalResult[0]['Total_Incoming']).'</th>
    </tr>
    <tr>
        <th>Total No. Of Completed Files</th>
        <th class="text-center">'.htmlspecialchars($reportTotalResult[0]['Total_Completed']).'</th>
    </tr>
    <tr>
        <th>Total No. Of Declined Files</th>
        <th class="text-center">'.htmlspecialchars($reportTotalResult[0]['Total_Declined']).'</th>
    </tr>
</table>
<br>
<table style="font-size:12px;">
<tr>
    <th style="background-color:gray;color:white">Department</th>
    <th style="width:100px;background-color:gray;color:white">Campus</th>
    <th style="background-color:gray;color:white">Sent Files</th>
    <th style="background-color:gray;color:white">Completed</th>
    <th style="background-color:gray;color:white">Declined</th>
</tr>';
foreach ($monthlyReportResult as $row) {
    $html .= '<tr>
                <td>' . htmlspecialchars($row['Department']) . '</td>
                <td>' . htmlspecialchars($row['Campus']) . '</td>
                <td class="text-center">' . htmlspecialchars($row['Total_Incoming']) . '</td>
                <td class="text-center">' . htmlspecialchars($row['Total_Completed']) . '</td>
                <td class="text-center">' . htmlspecialchars($row['Total_Declined']) . '</td>
              </tr>';
}
$html .= '</table></body></html>';
// Write the HTML to the PDF
$mpdf->WriteHTML($html);
// Output the PDF to the browser
$mpdf->Output('report.pdf', 'I'); // 'I' means inline display (open in the browser)
?>
<script>
// This script will trigger the print dialog after the PDF is opened in the browser
window.onload = function() {
    window.print();
};
</script>