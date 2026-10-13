<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\SchoolSetting;
use App\Models\Teacher;
use App\Models\TimeSlot;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use ZipArchive;

class ScheduleZipExportService
{
    /**
     * Format list of teacher subject assignments to match master matrix (concise Wajib & TL).
     */
    public static function formatTeacherSubjects($assignments): string
    {
        if ($assignments->isEmpty()) {
            return '-';
        }

        $groups = [];
        foreach ($assignments as $asg) {
            $name = trim($asg->subject->name ?? '');
            if (!$name) continue;

            $upper = strtoupper($name);
            $isTL = str_contains($upper, 'TINGKAT LANJUT') || str_ends_with($upper, ' TL');
            $isWajib = str_contains($upper, 'WAJIB');

            // Strip suffixes to get base name
            $base = preg_replace('/\b(TINGKAT LANJUT|TL|WAJIB)\b/i', '', $name);
            $base = trim(preg_replace('/\s+/', ' ', $base));

            if (!$base) {
                $base = $name;
            }

            // Convert to nice Title Case if all uppercase, but preserve common acronyms
            $acronyms = ['SKI' => 'SKI', 'BK' => 'BK', 'PJOK' => 'PJOK', 'SBK' => 'SBK', 'FIKIH' => 'Fikih', 'FIQIH' => 'Fikih', 'SI' => 'SI', 'IPA' => 'IPA', 'IPS' => 'IPS'];
            if (isset($acronyms[strtoupper($base)])) {
                $base = $acronyms[strtoupper($base)];
            } else {
                $base = ucwords(strtolower($base));
            }

            if (!isset($groups[$base])) {
                $groups[$base] = ['has_wajib' => false, 'has_tl' => false, 'plain' => false];
            }

            if ($isTL) {
                $groups[$base]['has_tl'] = true;
            } elseif ($isWajib) {
                $groups[$base]['has_wajib'] = true;
            } else {
                $groups[$base]['plain'] = true;
            }
        }

        $formatted = [];
        foreach ($groups as $base => $info) {
            if ($info['has_tl'] && ($info['has_wajib'] || $info['plain'])) {
                $formatted[] = $base . ' (Wajib & TL)';
            } elseif ($info['has_tl']) {
                $formatted[] = $base . ' TL';
            } elseif ($info['has_wajib']) {
                $formatted[] = $base . ' Wajib';
            } else {
                $formatted[] = $base;
            }
        }

        return implode(', ', $formatted);
    }

    /**
     * Locate installed Chromium / Edge executable for 100% browser-grade PDF rendering.
     */
    public static function getChromiumBinary(): ?string
    {
        $candidates = [
            'C:\Program Files\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
            'C:\Program Files\Microsoft\Edge\Application\msedge.exe',
        ];

        foreach ($candidates as $bin) {
            if (file_exists($bin)) {
                return $bin;
            }
        }

        return null;
    }

    /**
     * Embed critical public images as Base64 data URIs so they load instantly with zero broken links.
     */
    private static function embedBase64Images(string $html, $settings): string
    {
        $logos = [
            'images/logo_kemenag.png',
            'images/logo_man20.png',
        ];

        foreach ($logos as $rel) {
            $full = public_path($rel);
            if (file_exists($full)) {
                $ext = pathinfo($full, PATHINFO_EXTENSION);
                $b64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($full));
                $html = str_replace(asset($rel), $b64, $html);
            }
        }

        if (!empty($settings['school_stamp']) && file_exists(public_path($settings['school_stamp']))) {
            $full = public_path($settings['school_stamp']);
            $ext = pathinfo($full, PATHINFO_EXTENSION);
            $b64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($full));
            $html = str_replace(asset($settings['school_stamp']), $b64, $html);
        }

        if (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) {
            $full = public_path($settings['principal_signature']);
            $ext = pathinfo($full, PATHINFO_EXTENSION);
            $b64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($full));
            $html = str_replace(asset($settings['principal_signature']), $b64, $html);
        }

        return $html;
    }

    /**
     * Render HTML to PDF using Chromium headless (Pixel-perfect identical to browser print).
     * Falls back to DomPDF if Chromium binary is unavailable.
     */
    private function renderViewToPdf(string $html, string $outputPdfPath, \Closure $domPdfFallback): void
    {
        $chromium = self::getChromiumBinary();

        if ($chromium) {
            $tempHtml = storage_path('app/temp_render_' . uniqid('', true) . '.html');
            file_put_contents($tempHtml, $html);
            $fileUrl = 'file:///' . str_replace('\\', '/', $tempHtml);
            $userDataDir = storage_path('app/chrome_profile');

            $cmd = '"' . $chromium . '" --headless=new --disable-gpu --no-first-run --no-default-browser-check --disable-extensions --disable-sync --disable-background-networking --disable-default-apps --user-data-dir="' . $userDataDir . '" --allow-file-access-from-files --no-pdf-header-footer --print-to-pdf="' . $outputPdfPath . '" "' . $fileUrl . '"';
            exec($cmd, $output, $code);
            @unlink($tempHtml);

            if (file_exists($outputPdfPath) && filesize($outputPdfPath) > 1000) {
                return;
            }
        }

        // Fallback to DomPDF if Chromium is not present or failed
        $pdfOutput = $domPdfFallback();
        file_put_contents($outputPdfPath, $pdfOutput);
    }

    /**
     * Generate ZIP archive containing all classroom schedules as A4 Landscape PDFs.
     */
    public function exportClassesZip(): string
    {
        $classrooms = Classroom::with('homeroomTeacher')->orderBy('grade')->orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);
        $timeSlots = TimeSlot::orderBy('period')->get()->unique('period')->keyBy('period');
        $settings = SchoolSetting::pluck('value', 'key');

        $zipFileName = 'Semua_Jadwal_Kelas_MAN20_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat file ZIP di server.');
        }

        $tempFiles = [];

        foreach ($classrooms as $classroom) {
            $schedules = Schedule::where('classroom_id', $classroom->id)
                ->with('teacher', 'subject')
                ->get();

            $schedulesGrid = [];
            foreach ($schedules as $s) {
                $schedulesGrid[$s->day][$s->period] = $s;
            }

            $viewData = [
                'classroom' => $classroom,
                'days' => $days,
                'periods' => $periods,
                'schedulesGrid' => $schedulesGrid,
                'timeSlots' => $timeSlots,
                'settings' => $settings,
                'isPdf' => true,
            ];

            $html = view('schedules.print_class', $viewData)->render();
            $html = self::embedBase64Images($html, $settings);

            $tempPdfPath = storage_path('app/temp_cls_' . $classroom->id . '_' . uniqid() . '.pdf');
            $tempFiles[] = $tempPdfPath;

            $this->renderViewToPdf($html, $tempPdfPath, function () use ($viewData) {
                return Pdf::loadView('schedules.print_class', $viewData)
                    ->setPaper('a4', 'landscape')
                    ->output();
            });

            $safeClassName = Str::slug($classroom->name, '-');
            $pdfFileName = 'Jadwal_Kelas_' . strtoupper($safeClassName) . '.pdf';

            if (file_exists($tempPdfPath)) {
                $zip->addFile($tempPdfPath, $pdfFileName);
            }
        }

        $zip->close();

        foreach ($tempFiles as $f) {
            @unlink($f);
        }

        return $zipPath;
    }

    /**
     * Generate ZIP archive containing all teacher schedules as A4 Landscape PDFs.
     */
    public function exportTeachersZip(): string
    {
        $teachers = Teacher::orderByRaw('COALESCE(order_no, 999)')->orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);
        $timeSlots = TimeSlot::orderBy('period')->get()->unique('period')->keyBy('period');
        $settings = SchoolSetting::pluck('value', 'key');

        $zipFileName = 'Semua_Jadwal_Guru_MAN20_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat file ZIP di server.');
        }

        $tempFiles = [];

        foreach ($teachers as $teacher) {
            $schedules = Schedule::where('teacher_id', $teacher->id)
                ->with('classroom', 'subject')
                ->get();

            $schedulesGrid = [];
            foreach ($schedules as $s) {
                $schedulesGrid[$s->day][$s->period] = $s;
            }

            $viewData = [
                'teacher' => $teacher,
                'days' => $days,
                'periods' => $periods,
                'schedulesGrid' => $schedulesGrid,
                'timeSlots' => $timeSlots,
                'settings' => $settings,
                'isPdf' => true,
            ];

            $html = view('schedules.print_teacher', $viewData)->render();
            $html = self::embedBase64Images($html, $settings);

            $tempPdfPath = storage_path('app/temp_tch_' . $teacher->id . '_' . uniqid() . '.pdf');
            $tempFiles[] = $tempPdfPath;

            $this->renderViewToPdf($html, $tempPdfPath, function () use ($viewData) {
                return Pdf::loadView('schedules.print_teacher', $viewData)
                    ->setPaper('a4', 'landscape')
                    ->output();
            });

            $prefix = $teacher->order_no ? sprintf('%02d_', $teacher->order_no) : '';
            $safeTeacherName = Str::slug($teacher->name, '_');
            $pdfFileName = 'Jadwal_Guru_' . $prefix . $safeTeacherName . '.pdf';

            if (file_exists($tempPdfPath)) {
                $zip->addFile($tempPdfPath, $pdfFileName);
            }
        }

        $zip->close();

        foreach ($tempFiles as $f) {
            @unlink($f);
        }

        return $zipPath;
    }
}
