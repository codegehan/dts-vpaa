<?php 
require '../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

function addLogoToPDF($inputPath, $outputPath, $logoPath) {
    $pdf = new Fpdi();
    $pdf->setSourceFile($inputPath);
    $pageCount = $pdf->setSourceFile($inputPath);

    for ($i = 1; $i <= $pageCount; $i++) {
        $tplIdx = $pdf->importPage($i);
        $size = $pdf->getTemplateSize($tplIdx);

        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($tplIdx);

        // Add Logo (Adjust Position and Size)
        $pdf->Image($logoPath, 150, 300, 50); // (x, y, width)
    }

    $pdf->Output($outputPath, 'F');
}
?>