<?php
require_once 'vendor/autoload.php';
// Sample data for the report
$data = [
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],
    ['Department' => 'Finance', 'Campus' => 'Dapitan'],
    ['Department' => 'HR', 'Campus' => 'Dipolog'],
    ['Department' => 'IT', 'Campus' => 'Katipunan'],
    ['Department' => 'IT', 'Campus' => 'Tampilisan'],
    ['Department' => 'IT', 'Campus' => 'Siocon'],

];
// Initialize mPDF
$mpdf = new \Mpdf\Mpdf();
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
<div style="display: flex; align-items: center; flex-direction: column; text-align: center;margin-bottom:2rem;">
    <img src="assets/img/logo.png" alt="Logo" style="width: 60px; margin-bottom: 10px;">
    <h5 style="margin: 0;">Republic of the Philippines</h5>
    <h5 style="margin: 0; color: red; font-style: italic;">JOSE RIZAL MEMORIAL STATE UNIVERSITY</h5>
    <h5 style="margin: 0;">The premier University in Zamboanga del Norte</h5>
</div>
<hr>
<h2 style="text-align:center;">VPAA Document Management System Monthly Report</h2>
<div style="margin-bottom:15px;">
<h4 style="margin:0;">Month: <span style="color:green">JANUARY</span></h4>
<h4 style="margin:0;">Year: <span style="color:green">2025</span></h4>
</div>
<table>
    <tr>
        <th>Total No. Of Incoming</th>
        <th class="text-center">90</th>
    </tr>
    <tr>
        <th>Total No. Of Completed</th>
        <th class="text-center">50</th>
    </tr>
    <tr>
        <th>Total No. Of Declined</th>
        <th class="text-center">30</th>
    </tr>
</table>
<br>
<table style="font-size:12px;">
<tr>
    <th style="background-color:gray;color:white">Department</th>
    <th style="width:100px;background-color:gray;color:white">Campus</th>
    <th style="background-color:gray;color:white">No. Incoming</th>
    <th style="background-color:gray;color:white">No. Completed</th>
    <th style="background-color:gray;color:white">No. Declined</th>
</tr>';
foreach ($data as $row) {
    $html .= '<tr>
                <td>' . htmlspecialchars($row['Department']) . '</td>
                <td>' . htmlspecialchars($row['Campus']) . '</td>
                <td class="text-center">12</td>
                <td class="text-center">12</td>
                <td class="text-center">12</td>
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
