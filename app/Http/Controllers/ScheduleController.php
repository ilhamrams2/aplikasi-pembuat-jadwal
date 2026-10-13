<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\TimeSlot;
use App\Models\SchoolSetting;
use App\Services\ScheduleGeneratorService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $generator;

    public function __construct(ScheduleGeneratorService $generator)
    {
        $this->generator = $generator;
    }

    public function index(Request $request)
    {
        $viewType = $request->get('type', 'class'); // 'class', 'teacher', 'master'
        $selectedClassId = $request->get('classroom_id');
        $selectedTeacherId = $request->get('teacher_id');

        $classrooms = Classroom::orderBy('grade')->orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);

        if (!$selectedClassId && $classrooms->isNotEmpty()) {
            $selectedClassId = $classrooms->first()->id;
        }
        if (!$selectedTeacherId && $teachers->isNotEmpty()) {
            $selectedTeacherId = $teachers->first()->id;
        }

        // Get schedules depending on viewType
        $schedulesGrid = [];
        $selectedEntity = null;

        if ($viewType === 'class' && $selectedClassId) {
            $selectedEntity = Classroom::with('homeroomTeacher')->find($selectedClassId);
            $schedules = Schedule::where('classroom_id', $selectedClassId)
                ->with('teacher', 'subject', 'timeSlot')
                ->get();

            foreach ($schedules as $s) {
                $schedulesGrid[$s->day][$s->period] = $s;
            }
        } elseif ($viewType === 'teacher' && $selectedTeacherId) {
            $selectedEntity = Teacher::find($selectedTeacherId);
            $schedules = Schedule::where('teacher_id', $selectedTeacherId)
                ->with('classroom', 'subject', 'timeSlot')
                ->get();

            foreach ($schedules as $s) {
                $schedulesGrid[$s->day][$s->period] = $s;
            }
        } elseif ($viewType === 'master') {
            // Master matrix: for a specific day or all
            $selectedDay = $request->get('day', 'Senin');
            $activeTab = $request->get('tab', 'official'); // 'official' (SK Reisvi 4 full layout) or 'interactive' (daily grid)
            $schedules = Schedule::where('day', $selectedDay)
                ->with('classroom', 'teacher', 'subject')
                ->get();

            $masterGrid = [];
            foreach ($schedules as $s) {
                $masterGrid[$s->classroom_id][$s->period] = $s;
            }

            $officialHeader = \App\Services\OfficialScheduleData::getHeaderInfo();
            $officialClassColumns = \App\Services\OfficialScheduleData::getClassColumns();
            $officialDaysSchedule = \App\Services\OfficialScheduleData::getDaysSchedule();
            $officialTeachersList = \App\Services\OfficialScheduleData::getTeachersList();
            $officialHomeroomTeachers = \App\Services\OfficialScheduleData::getHomeroomTeachers();

            return view('schedules.master', compact(
                'viewType', 'classrooms', 'teachers', 'days', 'periods', 'selectedDay', 'masterGrid',
                'activeTab', 'officialHeader', 'officialClassColumns', 'officialDaysSchedule', 'officialTeachersList', 'officialHomeroomTeachers'
            ));
        }

        // Available assignments for manual assignment modal
        $availableAssignments = [];
        $teacherBusyMap = [];
        if ($selectedClassId) {
            $availableAssignments = TeacherAssignment::where('classroom_id', $selectedClassId)
                ->with('teacher', 'subject')
                ->withCount('schedules')
                ->get();

            // Map busy slots: key = "teacherId_day_period", value = classroom name
            $allSchedules = Schedule::with('classroom')->get();
            foreach ($allSchedules as $s) {
                if ($s->classroom_id != $selectedClassId) {
                    $teacherBusyMap[$s->teacher_id . '_' . $s->day . '_' . $s->period] = $s->classroom->name;
                }
            }
        }

        $conflicts = $this->generator->detectConflicts();

        // 5. Guru Piket Harian
        $pikets = [
            'Senin' => SchoolSetting::get('piket_Senin', 'FIRMANSYAH S.Ag., HANNY MILLENIA S.Pd., M. IKHLAS, S.Pd.'),
            'Selasa' => SchoolSetting::get('piket_Selasa', 'MIFTAH EL FALAH, SE., NURHASANAH S.Pd., LEYLIA ARUM AMALUN NISA S.Pd.'),
            'Rabu' => SchoolSetting::get('piket_Rabu', 'NANANG KOSIM S.Pd., SITI HASANAH S.Ag., IIS SURYANI S.Pd.I.'),
            'Kamis' => SchoolSetting::get('piket_Kamis', 'ALI SUBHAN S.Ag., SITI BAROROH S.Pd., MUHAMAD RICO SAPUTRA, S.Pd.'),
            'Jumat' => SchoolSetting::get('piket_Jumat', 'SITI RUWAIDAH, S.Pd., ARIEL BONARDO H. LIMBONG S.Pd., CITRA ARIYANTI'),
        ];

        return view('schedules.index', compact(
            'viewType', 'classrooms', 'teachers', 'days', 'periods',
            'selectedClassId', 'selectedTeacherId', 'selectedEntity',
            'schedulesGrid', 'availableAssignments', 'conflicts', 'teacherBusyMap', 'pikets'
        ));
    }

    public function updatePiket(Request $request)
    {
        $request->validate([
            'piket_Senin' => 'nullable|string|max:255',
            'piket_Selasa' => 'nullable|string|max:255',
            'piket_Rabu' => 'nullable|string|max:255',
            'piket_Kamis' => 'nullable|string|max:255',
            'piket_Jumat' => 'nullable|string|max:255',
        ]);

        foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day) {
            if ($request->has("piket_{$day}")) {
                SchoolSetting::set("piket_{$day}", $request->input("piket_{$day}"));
            }
        }

        return back()->with('success', 'Data Guru Piket berhasil diperbarui.');
    }

    public function generate(Request $request)
    {
        $result = $this->generator->generateAll();

        if ($result['success']) {
            $msg = "Berhasil membuat jadwal otomatis! Total {$result['total_placed']} jam pelajaran berhasil ditempatkan.";
            if (count($result['unplaced']) > 0) {
                $msg .= " Catatan: Terdapat " . count($result['unplaced']) . " jam yang belum termuat karena keterbatasan slot.";
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Gagal membuat jadwal otomatis.');
    }

    public function clear()
    {
        Schedule::query()->delete();
        return back()->with('success', 'Semua data jadwal berhasil direset/dikosongkan.');
    }

    public function generateClass(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        $result = $this->generator->generateClass($classroom->id);

        if ($result['success']) {
            $msg = "Berhasil membuat jadwal otomatis khusus Kelas {$classroom->name}! Total {$result['total_placed']} JP berhasil ditempatkan.";
            if (count($result['unplaced']) > 0) {
                $msg .= " Catatan: Terdapat " . count($result['unplaced']) . " jam yang belum termuat karena bentrok slot guru di kelas lain.";
            }
            return redirect()->route('schedules.index', ['type' => 'class', 'classroom_id' => $classroom->id])
                ->with('success', $msg);
        }

        return redirect()->route('schedules.index', ['type' => 'class', 'classroom_id' => $classroom->id])
            ->with('error', "Gagal membuat jadwal otomatis untuk Kelas {$classroom->name}.");
    }

    public function clearClass(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        Schedule::where('classroom_id', $classroom->id)->delete();

        return redirect()->route('schedules.index', ['type' => 'class', 'classroom_id' => $classroom->id])
            ->with('success', "Jadwal pelajaran khusus Kelas {$classroom->name} berhasil direset/dikosongkan.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_assignment_id' => 'required|exists:teacher_assignments,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'period' => 'required|integer|min:1|max:10',
        ]);

        $assignment = TeacherAssignment::findOrFail($request->teacher_assignment_id);

        // Find or create matching time slot
        $timeSlot = TimeSlot::where('day', $request->day)
            ->where('period', $request->period)
            ->first();

        if (!$timeSlot) {
            return back()->with('error', 'Slot waktu tidak ditemukan.');
        }

        // Check if teacher is already busy at this time
        $clash = Schedule::where('teacher_id', $assignment->teacher_id)
            ->where('day', $request->day)
            ->where('period', $request->period)
            ->where('classroom_id', '!=', $request->classroom_id)
            ->with('classroom')
            ->first();

        if ($clash) {
            return back()->with('error', "Bentrok! Guru {$assignment->teacher->name} sudah mengajar di kelas {$clash->classroom->name} pada hari {$request->day} jam ke-{$request->period}.");
        }

        // Check if JP quota for this assignment is already full
        $existingSchedule = Schedule::where('classroom_id', $request->classroom_id)
            ->where('time_slot_id', $timeSlot->id)
            ->first();

        $currentSchedulesCount = Schedule::where('teacher_assignment_id', $assignment->id)->count();
        $isReplacingSame = $existingSchedule && $existingSchedule->teacher_assignment_id == $assignment->id;

        if (!$isReplacingSame && $currentSchedulesCount >= $assignment->hours_per_week) {
            return back()->with('error', "Alokasi jam pelajaran untuk {$assignment->subject->name} ({$assignment->teacher->name}) sudah habis / terpenuhi ({$assignment->hours_per_week}/{$assignment->hours_per_week} JP). Tidak dapat menambahkan lagi.");
        }

        Schedule::updateOrCreate(
            [
                'classroom_id' => $request->classroom_id,
                'time_slot_id' => $timeSlot->id,
            ],
            [
                'teacher_assignment_id' => $assignment->id,
                'teacher_id' => $assignment->teacher_id,
                'subject_id' => $assignment->subject_id,
                'day' => $request->day,
                'period' => $request->period,
                'room' => 'R.' . $assignment->classroom->name,
            ]
        );

        return back()->with('success', 'Jadwal pelajaran berhasil ditambahkan/diperbarui.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return back()->with('success', 'Jadwal pada slot tersebut telah dihapus.');
    }

    public function printClass($id)
    {
        $classroom = Classroom::with('homeroomTeacher')->findOrFail($id);
        $schedules = Schedule::where('classroom_id', $id)->with('teacher', 'subject')->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);
        $schedulesGrid = [];
        foreach ($schedules as $s) {
            $schedulesGrid[$s->day][$s->period] = $s;
        }

        $timeSlots = TimeSlot::orderBy('period')->get()->unique('period')->keyBy('period');
        $settings = SchoolSetting::pluck('value', 'key');

        return view('schedules.print_class', compact('classroom', 'days', 'periods', 'schedulesGrid', 'timeSlots', 'settings'));
    }

    public function printTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $schedules = Schedule::where('teacher_id', $id)->with('classroom', 'subject')->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $periods = range(1, 10);
        $schedulesGrid = [];
        foreach ($schedules as $s) {
            $schedulesGrid[$s->day][$s->period] = $s;
        }

        $timeSlots = TimeSlot::orderBy('period')->get()->unique('period')->keyBy('period');
        $settings = SchoolSetting::pluck('value', 'key');

        return view('schedules.print_teacher', compact('teacher', 'days', 'periods', 'schedulesGrid', 'timeSlots', 'settings'));
    }

    public function printAll()
    {
        $data = $this->getPrintAllData();
        return view('schedules.print_all', $data);
    }

    public function exportExcel(\App\Services\ScheduleExcelExportService $excelService)
    {
        $data = $this->getPrintAllData();
        return $excelService->export(
            $data['header'],
            $data['classrooms'],
            $data['daysSchedule'],
            $data['teachersList'],
            $data['homeroomTeachers'],
            $data['pikets']
        );
    }

    public function exportZipClasses(\App\Services\ScheduleZipExportService $zipService)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        $zipPath = $zipService->exportClassesZip();
        return response()->download($zipPath, 'Semua_Jadwal_Kelas_MAN20.zip')->deleteFileAfterSend(true);
    }

    public function exportZipTeachers(\App\Services\ScheduleZipExportService $zipService)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        $zipPath = $zipService->exportTeachersZip();
        return response()->download($zipPath, 'Semua_Jadwal_Guru_MAN20.zip')->deleteFileAfterSend(true);
    }

    private function getPrintAllData(): array
    {
        $settings = SchoolSetting::pluck('value', 'key');
        
        $header = [
            'title' => 'JADWAL PELAJARAN SEMESTER ' . strtoupper($settings['semester'] ?? 'Ganjil'),
            'school_name' => strtoupper($settings['school_name'] ?? 'MAN 20 JAKARTA TIMUR'),
            'academic_year' => 'TAHUN PELAJARAN ' . ($settings['academic_year'] ?? '2026/2027'),
            'revision' => $settings['schedule_revision'] ?? 'REVISI4_BERLAKU MULAI : Senin, 12 Oktober 2026',
            'date_signed' => $settings['date_signed'] ?? 'Jakarta, 13 Juli 2026',
            'principal_name' => $settings['principal_name'] ?? 'Drs. SODIKIN, M.Si.',
            'principal_nip' => $settings['principal_nip'] ?? '196805101994031002',
            'principal_title' => 'Kepala MAN 20 Jakarta',
            'academic_head_name' => $settings['waka_kurikulum'] ?? 'TEMY YULIANTI, S.Pd., M.Pd.',
            'academic_head_nip' => '197712102009122001',
            'academic_head_title' => 'Wakil Kepala Bidang Akademik',
            'note' => 'Catatan: Setelah salat Ashar piket kelas wajib membersihkan kelasnya sebelum pulang',
            'principal_signature' => $settings['principal_signature'] ?? '',
            'school_stamp' => $settings['school_stamp'] ?? '',
            'academic_head_signature' => $settings['academic_head_signature'] ?? '',
        ];

        // 1. Dinamis Daftar Kelas
        $classrooms = Classroom::with('homeroomTeacher')->orderBy('grade')->orderBy('name')->get();

        // 2. Dinamis Jadwal dari Database
        $schedules = Schedule::with(['teacher', 'subject', 'classroom'])->get();
        $schedulesMap = [];
        $teacherDaysMap = []; // teacher_id => ['Senin' => count, ...]

        foreach ($schedules as $s) {
            $schedulesMap[$s->day][$s->period][$s->classroom_id] = $s;
            if (!isset($teacherDaysMap[$s->teacher_id])) {
                $teacherDaysMap[$s->teacher_id] = ['Senin' => 0, 'Selasa' => 0, 'Rabu' => 0, 'Kamis' => 0, 'Jumat' => 0];
            }
            if (isset($teacherDaysMap[$s->teacher_id][$s->day])) {
                $teacherDaysMap[$s->teacher_id][$s->day]++;
            }
        }

        // 3. Dinamis Daftar Guru & Beban Mengajar (Muncul Otomatis saat Guru Baru Ditambahkan)
        $allTeachers = Teacher::with(['assignments.subject'])
            ->orderByRaw('COALESCE(order_no, 999)')
            ->orderBy('name')
            ->get();

        $teachersList = [];
        foreach ($allTeachers as $teacher) {
            $code = $teacher->order_no ? sprintf('%02d', $teacher->order_no) : sprintf('G%02d', $teacher->id);
            $subjectStr = \App\Services\ScheduleZipExportService::formatTeacherSubjects($teacher->assignments);
            
            $daysCount = [
                $teacherDaysMap[$teacher->id]['Senin'] ?? 0,
                $teacherDaysMap[$teacher->id]['Selasa'] ?? 0,
                $teacherDaysMap[$teacher->id]['Rabu'] ?? 0,
                $teacherDaysMap[$teacher->id]['Kamis'] ?? 0,
                $teacherDaysMap[$teacher->id]['Jumat'] ?? 0,
            ];
            $totalHours = array_sum($daysCount);
            if ($totalHours == 0 && $teacher->target_hours > 0) {
                $totalHours = $teacher->assignments->sum('hours_per_week');
            }

            $teachersList[$code] = [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'subject' => $subjectStr,
                'days' => $daysCount,
                'total' => $totalHours,
            ];
        }

        // 4. Dinamis Daftar Wali Kelas (Muncul Otomatis dari Relasi Kelas)
        $homeroomTeachers = [];
        $no = 1;
        foreach ($classrooms as $cls) {
            $homeroomTeachers[$no] = [
                'class' => 'Kelas ' . $cls->name,
                'name' => $cls->homeroomTeacher ? $cls->homeroomTeacher->name : 'Belum Ditentukan',
            ];
            $no++;
        }

        // 5. Guru Piket
        $pikets = [
            'Senin' => SchoolSetting::get('piket_Senin', 'FIRMANSYAH S.Ag., HANNY MILLENIA S.Pd., M. IKHLAS, S.Pd.'),
            'Selasa' => SchoolSetting::get('piket_Selasa', 'MIFTAH EL FALAH, SE., NURHASANAH S.Pd., LEYLIA ARUM AMALUN NISA S.Pd.'),
            'Rabu' => SchoolSetting::get('piket_Rabu', 'NANANG KOSIM S.Pd., SITI HASANAH S.Ag., IIS SURYANI S.Pd.I.'),
            'Kamis' => SchoolSetting::get('piket_Kamis', 'ALI SUBHAN S.Ag., SITI BAROROH S.Pd., MUHAMAD RICO SAPUTRA, S.Pd.'),
            'Jumat' => SchoolSetting::get('piket_Jumat', 'SITI RUWAIDAH, S.Pd., ARIEL BONARDO H. LIMBONG S.Pd., CITRA ARIYANTI'),
        ];

        // 6. Build Struktur Jadwal Lengkap
        $daysSchedule = $this->buildDynamicDaysSchedule($schedulesMap, $classrooms);

        return compact(
            'header', 'classrooms', 'daysSchedule', 'teachersList', 'homeroomTeachers', 'pikets'
        );
    }

    private function buildDynamicDaysSchedule($schedulesMap, $classrooms)
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $timesSeninKamis = [
            1 => '06:30 - 07:15',
            2 => '07:15 - 08:00',
            3 => '08:00 - 08:45',
            4 => '08:45 - 09:30',
            'break_1' => ['time' => '09:30 - 09:50', 'label' => 'ISTIRAHAT / SALAT DUHA'],
            5 => '09:50 - 10:35',
            6 => '10:35 - 11:20',
            7 => '11:20 - 12:05',
            'break_2' => ['time' => '12:05 - 12:45', 'label' => 'ISTIRAHAT DAN SALAT ZUHUR BERJAMAAH'],
            8 => '12:45 - 13:30',
            9 => '13:30 - 14:15',
            10 => '14:15 - 15:00',
            'salat_ashar' => ['time' => '15.00 - 15.30', 'label' => 'SALAT ASHAR BERJAMAAH'],
        ];

        $timesJumat = [
            1 => '06:30 - 07:15',
            2 => '07:15 - 08:00',
            3 => '08:00 - 08:45',
            4 => '08:45 - 09:30',
            'break_1' => ['time' => '09:30 - 09:50', 'label' => 'ISTIRAHAT / SALAT DUHA'],
            5 => '09:50 - 10:35',
            6 => '10:35 - 11:20',
            'break_2' => ['time' => '11:20 - 12:30', 'label' => 'ISTIRAHAT DAN SALAT JUMAT/ZUHUR BERJAMAAH'],
            7 => '12:30 - 13:15',
            8 => '13:15 - 14:00',
            9 => '14:00 - 14:45',
            10 => '14:45 - 15:30',
            'salat_ashar' => ['time' => '15.30 - 16.00', 'label' => 'SALAT ASHAR BERJAMAAH'],
        ];

        $pikets = [
            'Senin' => SchoolSetting::get('piket_Senin', 'FIRMANSYAH S.Ag., HANNY MILLENIA S.Pd., M. IKHLAS, S.Pd.'),
            'Selasa' => SchoolSetting::get('piket_Selasa', 'MIFTAH EL FALAH, SE., NURHASANAH S.Pd., LEYLIA ARUM AMALUN NISA S.Pd.'),
            'Rabu' => SchoolSetting::get('piket_Rabu', 'NANANG KOSIM S.Pd., SITI HASANAH S.Ag., IIS SURYANI S.Pd.I.'),
            'Kamis' => SchoolSetting::get('piket_Kamis', 'ALI SUBHAN S.Ag., SITI BAROROH S.Pd., MUHAMAD RICO SAPUTRA, S.Pd.'),
            'Jumat' => SchoolSetting::get('piket_Jumat', 'SITI RUWAIDAH, S.Pd., ARIEL BONARDO H. LIMBONG S.Pd., CITRA ARIYANTI'),
        ];

        $daysSchedule = [];
        foreach ($days as $day) {
            $times = ($day === 'Jumat') ? $timesJumat : $timesSeninKamis;
            $periodsData = [];

            foreach ($times as $key => $timeVal) {
                if ($key === 'break_1' || $key === 'break_2' || $key === 'salat_ashar') {
                    $periodsData[$key] = $timeVal;
                    continue;
                }

                $pNum = (int)$key;
                $slots = [];
                $hasPramukaGrade10 = ($day === 'Rabu' && in_array($pNum, [1, 2]));

                foreach ($classrooms as $cls) {
                    if (isset($schedulesMap[$day][$pNum][$cls->id])) {
                        $s = $schedulesMap[$day][$pNum][$cls->id];
                        $teacherOrder = $s->teacher->order_no ?? null;

                        if ($hasPramukaGrade10 && $cls->grade == 10) {
                            $code = 'PRAMUKA';
                        } elseif ($teacherOrder) {
                            $code = (string)$teacherOrder;
                            $subName = $s->subject->name ?? '';
                            if ($teacherOrder == 15) {
                                $code .= str_contains($subName, 'EKONOMI') ? 'E' : 'P';
                            } elseif ($teacherOrder == 17 && str_contains($subName, 'TINGKAT LANJUT')) {
                                $code .= 'T';
                            } elseif ($teacherOrder == 18) {
                                $code .= str_contains($subName, 'AKIDAH') ? 'A' : 'Q';
                            } elseif ($teacherOrder == 30) {
                                $code .= str_contains($subName, 'GEOGRAFI') ? 'G' : 'P';
                            } elseif ($teacherOrder == 33) {
                                $code .= str_contains($subName, 'SEJARAH') ? 'S' : 'P';
                            }
                        } else {
                            $code = 'G' . $s->teacher_id;
                        }

                        $slots[$cls->id] = $code;
                    } else {
                        $slots[$cls->id] = '';
                    }
                }

                $periodsData[$pNum] = [
                    'time' => $timeVal,
                    'slots' => $slots,
                    'special_merge_x' => $hasPramukaGrade10,
                ];
            }

            $daysSchedule[$day] = [
                'piket' => $pikets[$day] ?? '-',
                'periods' => $periodsData,
            ];
        }

        return $daysSchedule;
    }

    public function conflicts(Request $request)
    {
        $selectedDay = $request->get('day');
        $selectedTeacherId = $request->get('teacher_id');

        // Query for teacher clashes
        $teacherClashesQuery = Schedule::select('teacher_id', 'day', 'period', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
            ->groupBy('teacher_id', 'day', 'period')
            ->having('cnt', '>', 1)
            ->with('teacher');

        if ($selectedDay) {
            $teacherClashesQuery->where('day', $selectedDay);
        }
        if ($selectedTeacherId) {
            $teacherClashesQuery->where('teacher_id', $selectedTeacherId);
        }

        $teacherClashes = $teacherClashesQuery->orderBy('day')->orderBy('period')->get();

        $timesMap = [
            1 => '06:30 - 07:15',
            2 => '07:15 - 08:00',
            3 => '08:00 - 08:45',
            4 => '08:45 - 09:30',
            5 => '09:50 - 10:35',
            6 => '10:35 - 11:20',
            7 => '11:20 - 12:05',
            8 => '12:45 - 13:30',
            9 => '13:30 - 14:15',
            10 => '14:15 - 15:00',
        ];

        $detailedConflicts = [];
        foreach ($teacherClashes as $tc) {
            $schedules = Schedule::where('teacher_id', $tc->teacher_id)
                ->where('day', $tc->day)
                ->where('period', $tc->period)
                ->with('classroom', 'subject')
                ->get();

            $isPramuka = ($tc->day === 'Rabu' && in_array($tc->period, [1, 2]));

            $detailedConflicts[] = [
                'teacher' => $tc->teacher,
                'day' => $tc->day,
                'period' => $tc->period,
                'time' => $timesMap[$tc->period] ?? '07:00 - 07:45',
                'count' => $tc->cnt,
                'is_pramuka' => $isPramuka,
                'schedules' => $schedules,
            ];
        }

        // Summary stats
        $totalClashes = count($detailedConflicts);
        $affectedTeachers = collect($detailedConflicts)->pluck('teacher.id')->unique()->count();
        $affectedDays = collect($detailedConflicts)->pluck('day')->unique();
        $allTeachersWithClash = Teacher::whereIn('id', collect($detailedConflicts)->pluck('teacher.id')->unique())->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return view('schedules.conflicts', compact(
            'detailedConflicts', 'totalClashes', 'affectedTeachers', 'affectedDays',
            'allTeachersWithClash', 'days', 'selectedDay', 'selectedTeacherId'
        ));
    }
}
