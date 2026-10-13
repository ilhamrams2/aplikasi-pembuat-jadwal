<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduleExcelExportService
{
    public function export(array $header, $classrooms, array $daysSchedule, array $teachersList, array $homeroomTeachers, array $pikets): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jadwal Pelajaran Resmi');

        // Page setup for printing
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Define column counts
        $classCount = $classrooms->count();
        $totalLeftCols = 3 + $classCount; // Col A (HARI), Col B (JAM), Col C (PUKUL) + Class columns
        $lastClassCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalLeftCols);

        // 1. TOP HEADER (Center Aligned across all schedule columns)
        $sheet->mergeCells("A1:{$lastClassCol}1");
        $sheet->setCellValue("A1", $header['title'] ?? 'JADWAL PELAJARAN SEMESTER GANJIL');
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:{$lastClassCol}2");
        $sheet->setCellValue("A2", $header['school_name'] ?? 'MAN 20 JAKARTA TIMUR');
        $sheet->getStyle("A2")->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle("A2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A3:{$lastClassCol}3");
        $sheet->setCellValue("A3", $header['academic_year'] ?? 'TAHUN PELAJARAN 2026/2027');
        $sheet->getStyle("A3")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A4:{$lastClassCol}4");
        $sheet->setCellValue("A4", $header['revision'] ?? 'REVISI_RESMI BERLAKU MULAI : Senin, 12 Oktober 2026');
        $sheet->getStyle("A4")->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle("A4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 2. SCHEDULE TABLE HEADER (Row 6)
        $currentRow = 6;
        $sheet->setCellValue("A{$currentRow}", "HARI");
        $sheet->setCellValue("B{$currentRow}", "JAM KE");
        $sheet->setCellValue("C{$currentRow}", "PUKUL");

        $colIdx = 4;
        foreach ($classrooms as $cls) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue("{$colLetter}{$currentRow}", $cls->name);
            $colIdx++;
        }

        // Style Table Header Row
        $headerRange = "A{$currentRow}:{$lastClassCol}{$currentRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($currentRow)->setRowHeight(24);

        $tableStartRow = $currentRow;
        $currentRow++;

        // 3. POPULATE DAYS AND PERIODS
        foreach ($daysSchedule as $dayName => $dayData) {
            $periods = $dayData['periods'] ?? [];
            $dayStartRow = $currentRow;
            
            // We'll track row count for this day
            foreach ($periods as $periodKey => $pData) {
                if ($periodKey === 'break_1' || $periodKey === 'break_2' || $periodKey === 'salat_ashar') {
                    // Break Row
                    $sheet->setCellValue("B{$currentRow}", "");
                    $sheet->setCellValue("C{$currentRow}", $pData['time'] ?? '');
                    
                    // Merge across all class columns
                    $firstClassCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4);
                    $sheet->mergeCells("{$firstClassCol}{$currentRow}:{$lastClassCol}{$currentRow}");
                    $sheet->setCellValue("{$firstClassCol}{$currentRow}", $pData['label'] ?? '');
                    
                    $sheet->getStyle("C{$currentRow}:{$lastClassCol}{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
                    $sheet->getStyle("{$firstClassCol}{$currentRow}")->getFont()->setBold(true)->setSize(8);
                    $sheet->getStyle("{$firstClassCol}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getRowDimension($currentRow)->setRowHeight(18);
                } else {
                    // Regular Period Row
                    $sheet->setCellValue("B{$currentRow}", $periodKey);
                    $sheet->setCellValue("C{$currentRow}", $pData['time'] ?? '');
                    
                    $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$currentRow}:C{$currentRow}")->getFont()->setSize(8);

                    $cIdx = 4;
                    foreach ($classrooms as $cls) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx);
                        $slotVal = $pData['slots'][$cls->id] ?? '';
                        $sheet->setCellValue("{$colLetter}{$currentRow}", $slotVal);
                        $sheet->getStyle("{$colLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("{$colLetter}{$currentRow}")->getFont()->setBold(true)->setSize(8);
                        $cIdx++;
                    }
                    $sheet->getRowDimension($currentRow)->setRowHeight(18);
                }
                $currentRow++;
            }

            // PIKET ROW FOR THIS DAY
            $piketRow = $currentRow;
            $sheet->mergeCells("B{$piketRow}:C{$piketRow}");
            $sheet->setCellValue("B{$piketRow}", "PIKET");
            $sheet->getStyle("B{$piketRow}")->getFont()->setBold(true)->setSize(8);
            $sheet->getStyle("B{$piketRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $firstClassCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4);
            $sheet->mergeCells("{$firstClassCol}{$piketRow}:{$lastClassCol}{$piketRow}");
            $piketText = $dayData['piket'] ?? ($pikets[$dayName] ?? '-');
            $sheet->setCellValue("{$firstClassCol}{$piketRow}", $piketText);
            $sheet->getStyle("{$firstClassCol}{$piketRow}")->getFont()->setBold(true)->setSize(8);
            $sheet->getStyle("{$firstClassCol}{$piketRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B{$piketRow}:{$lastClassCol}{$piketRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            $sheet->getRowDimension($piketRow)->setRowHeight(20);

            // Merge Day Column across all rows of this day
            $sheet->mergeCells("A{$dayStartRow}:A{$piketRow}");
            $sheet->setCellValue("A{$dayStartRow}", strtoupper($dayName));
            $sheet->getStyle("A{$dayStartRow}")->getFont()->setBold(true)->setSize(9);
            $sheet->getStyle("A{$dayStartRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

            $currentRow++;
        }

        // Apply Borders to Entire Schedule Table
        $scheduleTableRange = "A{$tableStartRow}:{$lastClassCol}" . ($currentRow - 1);
        $sheet->getStyle($scheduleTableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Note below schedule
        $sheet->setCellValue("A{$currentRow}", $header['note'] ?? 'Catatan: Setelah salat Ashar piket kelas wajib membersihkan kelasnya sebelum pulang');
        $sheet->getStyle("A{$currentRow}")->getFont()->setItalic(true)->setSize(8);
        $currentRow += 2;

        // 4. RIGHT TABLES: TEACHER CODES & HOMEROOM TEACHERS
        // We place them side-by-side or below for crystal-clear readability
        $sideColStartIdx = $totalLeftCols + 2; // Leave 1 empty column separator
        $sideColStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx);
        
        $teacherHeaderRow = 6;
        $sheet->setCellValue("{$sideColStart}{$teacherHeaderRow}", "KODE");
        $col2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx + 1);
        $sheet->setCellValue("{$col2}{$teacherHeaderRow}", "NAMA GURU");
        $col3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx + 2);
        $sheet->setCellValue("{$col3}{$teacherHeaderRow}", "MATA PELAJARAN");
        $col4 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx + 3);
        $sheet->setCellValue("{$col4}{$teacherHeaderRow}", "SEN");
        $col5 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx + 4);
        $sheet->setCellValue("{$col5}{$teacherHeaderRow}", "SEL");
        $col6 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx + 5);
        $sheet->setCellValue("{$col6}{$teacherHeaderRow}", "RAB");
        $col7 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx + 6);
        $sheet->setCellValue("{$col7}{$teacherHeaderRow}", "KAM");
        $col8 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx + 7);
        $sheet->setCellValue("{$col8}{$teacherHeaderRow}", "JUM");
        $col9 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sideColStartIdx + 8);
        $sheet->setCellValue("{$col9}{$teacherHeaderRow}", "JML");

        $tHeaderEnd = $col9;
        $sheet->getStyle("{$sideColStart}{$teacherHeaderRow}:{$tHeaderEnd}{$teacherHeaderRow}")->getFont()->setBold(true)->setSize(8);
        $sheet->getStyle("{$sideColStart}{$teacherHeaderRow}:{$tHeaderEnd}{$teacherHeaderRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle("{$sideColStart}{$teacherHeaderRow}:{$tHeaderEnd}{$teacherHeaderRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $tRow = $teacherHeaderRow + 1;
        foreach ($teachersList as $code => $tInfo) {
            $sheet->setCellValue("{$sideColStart}{$tRow}", $code);
            $sheet->setCellValue("{$col2}{$tRow}", $tInfo['name']);
            $sheet->setCellValue("{$col3}{$tRow}", $tInfo['subject']);
            $sheet->setCellValue("{$col4}{$tRow}", $tInfo['days'][0] ?: '-');
            $sheet->setCellValue("{$col5}{$tRow}", $tInfo['days'][1] ?: '-');
            $sheet->setCellValue("{$col6}{$tRow}", $tInfo['days'][2] ?: '-');
            $sheet->setCellValue("{$col7}{$tRow}", $tInfo['days'][3] ?: '-');
            $sheet->setCellValue("{$col8}{$tRow}", $tInfo['days'][4] ?: '-');
            $sheet->setCellValue("{$col9}{$tRow}", $tInfo['total'] ?: '-');

            $sheet->getStyle("{$sideColStart}{$tRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$col4}{$tRow}:{$col9}{$tRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$sideColStart}{$tRow}:{$col9}{$tRow}")->getFont()->setSize(8);
            $sheet->getRowDimension($tRow)->setRowHeight(16);
            $tRow++;
        }

        $sheet->getStyle("{$sideColStart}{$teacherHeaderRow}:{$tHeaderEnd}" . ($tRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // HOMEROOM TEACHER TABLE (Below Teacher Table)
        $hrStartRow = $tRow + 1;
        $sheet->setCellValue("{$sideColStart}{$hrStartRow}", "NO");
        $sheet->setCellValue("{$col2}{$hrStartRow}", "KELAS");
        $sheet->mergeCells("{$col3}{$hrStartRow}:{$tHeaderEnd}{$hrStartRow}");
        $sheet->setCellValue("{$col3}{$hrStartRow}", "NAMA WALI KELAS");

        $sheet->getStyle("{$sideColStart}{$hrStartRow}:{$tHeaderEnd}{$hrStartRow}")->getFont()->setBold(true)->setSize(8);
        $sheet->getStyle("{$sideColStart}{$hrStartRow}:{$tHeaderEnd}{$hrStartRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');

        $hrRow = $hrStartRow + 1;
        foreach ($homeroomTeachers as $idx => $hr) {
            $sheet->setCellValue("{$sideColStart}{$hrRow}", $idx);
            $sheet->setCellValue("{$col2}{$hrRow}", $hr['class']);
            $sheet->mergeCells("{$col3}{$hrRow}:{$tHeaderEnd}{$hrRow}");
            $sheet->setCellValue("{$col3}{$hrRow}", $hr['name']);

            $sheet->getStyle("{$sideColStart}{$hrRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$sideColStart}{$hrRow}:{$tHeaderEnd}{$hrRow}")->getFont()->setSize(8);
            $sheet->getRowDimension($hrRow)->setRowHeight(16);
            $hrRow++;
        }

        $sheet->getStyle("{$sideColStart}{$hrStartRow}:{$tHeaderEnd}" . ($hrRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 5. SIGNATURES (Tanda Tangan)
        $sigRow = $hrRow + 2;
        $sheet->setCellValue("{$sideColStart}{$sigRow}", "Mengetahui,\n" . ($header['principal_title'] ?? 'Kepala MAN 20 Jakarta') . "\n\n\n\n" . ($header['principal_name'] ?? 'Drs. SODIKIN, M.Si.') . "\nNIP. " . ($header['principal_nip'] ?? '196805101994031002'));
        $sheet->mergeCells("{$sideColStart}{$sigRow}:{$col3}" . ($sigRow + 4));
        $sheet->getStyle("{$sideColStart}{$sigRow}")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("{$sideColStart}{$sigRow}")->getFont()->setSize(9);

        $sheet->setCellValue("{$col4}{$sigRow}", ($header['date_signed'] ?? 'Jakarta, 13 Juli 2026') . "\n" . ($header['academic_head_title'] ?? 'Wakil Kepala Bidang Akademik') . "\n\n\n\n" . ($header['academic_head_name'] ?? 'TEMY YULIANTI, S.Pd., M.Pd.') . "\nNIP. " . ($header['academic_head_nip'] ?? '197712102009122001'));
        $sheet->mergeCells("{$col4}{$sigRow}:{$tHeaderEnd}" . ($sigRow + 4));
        $sheet->getStyle("{$col4}{$sigRow}")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("{$col4}{$sigRow}")->getFont()->setSize(9);

        // Auto-fit column widths
        foreach (range(1, $sideColStartIdx + 8) as $c) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Column A (Day) specific minimum width
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(14);

        // Stream output as .xlsx file
        $fileName = 'Jadwal_Pelajaran_' . str_replace(' ', '_', $header['school_name'] ?? 'MAN20') . '_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
