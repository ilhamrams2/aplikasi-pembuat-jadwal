<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;

class ScheduleGeneratorService
{
    /**
     * Generate schedule automatically for all classes.
     *
     * @return array [success => bool, message => string, placed_count => int, unplaced => array]
     */
    public function generateAll(): array
    {
        return DB::transaction(function () {
            // Clear existing schedules
            Schedule::query()->delete();

            $classrooms = Classroom::with('assignments.teacher', 'assignments.subject')->get();
            $timeSlots = TimeSlot::where('is_break', false)->orderBy('day')->orderBy('period')->get();

            // Structure slots by day
            $slotsByDay = [];
            foreach ($timeSlots as $slot) {
                $slotsByDay[$slot->day][$slot->period] = $slot;
            }

            // Tracking occupancy
            // $teacherBusy[teacher_id][day][period] = true
            // $classBusy[classroom_id][day][period] = true
            // $classSubjectDays[classroom_id][subject_id] = [days used]
            $teacherBusy = [];
            $classBusy = [];
            $classSubjectDays = [];

            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            $allUnplaced = [];
            $totalPlaced = 0;

            // Sort classrooms and assignments
            foreach ($classrooms as $classroom) {
                // Get all assignments for this classroom
                $assignments = $classroom->assignments()->with('teacher', 'subject')->get();

                // Sort assignments: subjects with highest hours first (e.g. 5 JP, then 3 JP, then 2 JP, then 1 JP)
                $sortedAssignments = $assignments->sortByDesc('hours_per_week');

                foreach ($sortedAssignments as $assignment) {
                    $totalHours = $assignment->hours_per_week;
                    $teacherId = $assignment->teacher_id;
                    $subjectId = $assignment->subject_id;
                    $classId = $classroom->id;

                    // Determine block sizes to schedule
                    // e.g. 5 -> [3, 2] or [2, 3] or [5]
                    // 4 -> [2, 2]
                    // 3 -> [3]
                    // 2 -> [2]
                    // 1 -> [1]
                    $blocks = $this->splitIntoBlocks($totalHours);

                    foreach ($blocks as $blockSize) {
                        $placed = false;

                        // Try to find an available day and period range
                        // Shuffle days to distribute evenly across week
                        $dayCandidates = $days;
                        shuffle($dayCandidates);

                        // Prioritize days where this subject hasn't been taught in this class yet
                        usort($dayCandidates, function ($a, $b) use ($classSubjectDays, $classId, $subjectId) {
                            $usedA = in_array($a, $classSubjectDays[$classId][$subjectId] ?? []);
                            $usedB = in_array($b, $classSubjectDays[$classId][$subjectId] ?? []);
                            return $usedA <=> $usedB;
                        });

                        foreach ($dayCandidates as $day) {
                            if (!isset($slotsByDay[$day])) continue;

                            $daySlots = $slotsByDay[$day];
                            $maxPeriod = max(array_keys($daySlots));

                            // Try starting periods from 1 up to maxPeriod - blockSize + 1
                            $periodCandidates = [];
                            for ($p = 1; $p <= ($maxPeriod - $blockSize + 1); $p++) {
                                $periodCandidates[] = $p;
                            }
                            shuffle($periodCandidates);

                            foreach ($periodCandidates as $startPeriod) {
                                // Check if all periods in this block are free for both class and teacher
                                $canFit = true;
                                for ($offset = 0; $offset < $blockSize; $offset++) {
                                    $p = $startPeriod + $offset;
                                    if (!isset($daySlots[$p])) {
                                        $canFit = false;
                                        break;
                                    }
                                    if (isset($classBusy[$classId][$day][$p])) {
                                        $canFit = false;
                                        break;
                                    }
                                    if (isset($teacherBusy[$teacherId][$day][$p])) {
                                        $canFit = false;
                                        break;
                                    }
                                }

                                if ($canFit) {
                                    // Place the block!
                                    for ($offset = 0; $offset < $blockSize; $offset++) {
                                        $p = $startPeriod + $offset;
                                        $slot = $daySlots[$p];

                                        Schedule::create([
                                            'classroom_id' => $classId,
                                            'time_slot_id' => $slot->id,
                                            'teacher_assignment_id' => $assignment->id,
                                            'teacher_id' => $teacherId,
                                            'subject_id' => $subjectId,
                                            'day' => $day,
                                            'period' => $p,
                                            'room' => 'R.' . $classroom->name,
                                        ]);

                                        $classBusy[$classId][$day][$p] = true;
                                        $teacherBusy[$teacherId][$day][$p] = true;
                                        $totalPlaced++;
                                    }

                                    $classSubjectDays[$classId][$subjectId][] = $day;
                                    $placed = true;
                                    break;
                                }
                            }

                            if ($placed) break;
                        }

                        if (!$placed) {
                            // Fallback: try placing individual 1-hour slots
                            for ($rem = 0; $rem < $blockSize; $rem++) {
                                $singlePlaced = false;
                                foreach ($days as $day) {
                                    if (!isset($slotsByDay[$day])) continue;
                                    foreach ($slotsByDay[$day] as $p => $slot) {
                                        if (!isset($classBusy[$classId][$day][$p]) && !isset($teacherBusy[$teacherId][$day][$p])) {
                                            Schedule::create([
                                                'classroom_id' => $classId,
                                                'time_slot_id' => $slot->id,
                                                'teacher_assignment_id' => $assignment->id,
                                                'teacher_id' => $teacherId,
                                                'subject_id' => $subjectId,
                                                'day' => $day,
                                                'period' => $p,
                                                'room' => 'R.' . $classroom->name,
                                            ]);
                                            $classBusy[$classId][$day][$p] = true;
                                            $teacherBusy[$teacherId][$day][$p] = true;
                                            $totalPlaced++;
                                            $singlePlaced = true;
                                            break;
                                        }
                                    }
                                    if ($singlePlaced) break;
                                }

                                if (!$singlePlaced) {
                                    $allUnplaced[] = [
                                        'classroom' => $classroom->name,
                                        'teacher' => $assignment->teacher->name,
                                        'subject' => $assignment->subject->name,
                                        'hours' => 1,
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            return [
                'success' => true,
                'message' => 'Jadwal pelajaran berhasil digenerate otomatis!',
                'total_placed' => $totalPlaced,
                'unplaced' => $allUnplaced,
            ];
        });
    }

    /**
     * Generate schedule automatically for a specific classroom.
     * Existing schedules for this classroom will be cleared, while preserving all other classes.
     * Respects teacher occupancy from other classrooms to prevent any clashes.
     *
     * @param int $classroomId
     * @return array [success => bool, message => string, total_placed => int, unplaced => array, classroom => Classroom]
     */
    public function generateClass(int $classroomId): array
    {
        return DB::transaction(function () use ($classroomId) {
            $classroom = Classroom::with('assignments.teacher', 'assignments.subject')->findOrFail($classroomId);

            // 1. Clear existing schedules ONLY for this classroom
            Schedule::where('classroom_id', $classroomId)->delete();

            $timeSlots = TimeSlot::where('is_break', false)->orderBy('day')->orderBy('period')->get();

            // Structure slots by day
            $slotsByDay = [];
            foreach ($timeSlots as $slot) {
                $slotsByDay[$slot->day][$slot->period] = $slot;
            }

            // 2. Track teacher occupancy from OTHER classrooms
            $teacherBusy = [];
            $otherSchedules = Schedule::where('classroom_id', '!=', $classroomId)->get();
            foreach ($otherSchedules as $s) {
                $teacherBusy[$s->teacher_id][$s->day][$s->period] = true;
            }

            // Track occupancy for this classroom
            $classBusy = [];
            $classSubjectDays = [];

            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            $allUnplaced = [];
            $totalPlaced = 0;

            // 3. Get all assignments for this classroom
            $assignments = $classroom->assignments()->with('teacher', 'subject')->get();
            $sortedAssignments = $assignments->sortByDesc('hours_per_week');

            foreach ($sortedAssignments as $assignment) {
                $totalHours = $assignment->hours_per_week;
                $teacherId = $assignment->teacher_id;
                $subjectId = $assignment->subject_id;
                $classId = $classroom->id;

                $blocks = $this->splitIntoBlocks($totalHours);

                foreach ($blocks as $blockSize) {
                    $placed = false;

                    $dayCandidates = $days;
                    shuffle($dayCandidates);

                    usort($dayCandidates, function ($a, $b) use ($classSubjectDays, $classId, $subjectId) {
                        $usedA = in_array($a, $classSubjectDays[$classId][$subjectId] ?? []);
                        $usedB = in_array($b, $classSubjectDays[$classId][$subjectId] ?? []);
                        return $usedA <=> $usedB;
                    });

                    foreach ($dayCandidates as $day) {
                        if (!isset($slotsByDay[$day])) continue;

                        $daySlots = $slotsByDay[$day];
                        $maxPeriod = max(array_keys($daySlots));

                        $periodCandidates = [];
                        for ($p = 1; $p <= ($maxPeriod - $blockSize + 1); $p++) {
                            $periodCandidates[] = $p;
                        }
                        shuffle($periodCandidates);

                        foreach ($periodCandidates as $startPeriod) {
                            $canFit = true;
                            for ($offset = 0; $offset < $blockSize; $offset++) {
                                $p = $startPeriod + $offset;
                                if (!isset($daySlots[$p])) {
                                    $canFit = false;
                                    break;
                                }
                                if (isset($classBusy[$classId][$day][$p])) {
                                    $canFit = false;
                                    break;
                                }
                                if (isset($teacherBusy[$teacherId][$day][$p])) {
                                    $canFit = false;
                                    break;
                                }
                            }

                            if ($canFit) {
                                for ($offset = 0; $offset < $blockSize; $offset++) {
                                    $p = $startPeriod + $offset;
                                    $slot = $daySlots[$p];

                                    Schedule::create([
                                        'classroom_id' => $classId,
                                        'time_slot_id' => $slot->id,
                                        'teacher_assignment_id' => $assignment->id,
                                        'teacher_id' => $teacherId,
                                        'subject_id' => $subjectId,
                                        'day' => $day,
                                        'period' => $p,
                                        'room' => 'R.' . $classroom->name,
                                    ]);

                                    $classBusy[$classId][$day][$p] = true;
                                    $teacherBusy[$teacherId][$day][$p] = true;
                                    $totalPlaced++;
                                }

                                $classSubjectDays[$classId][$subjectId][] = $day;
                                $placed = true;
                                break;
                            }
                        }

                        if ($placed) break;
                    }

                    if (!$placed) {
                        // Fallback: try placing individual 1-hour slots
                        for ($rem = 0; $rem < $blockSize; $rem++) {
                            $singlePlaced = false;
                            foreach ($days as $day) {
                                if (!isset($slotsByDay[$day])) continue;
                                foreach ($slotsByDay[$day] as $p => $slot) {
                                    if (!isset($classBusy[$classId][$day][$p]) && !isset($teacherBusy[$teacherId][$day][$p])) {
                                        Schedule::create([
                                            'classroom_id' => $classId,
                                            'time_slot_id' => $slot->id,
                                            'teacher_assignment_id' => $assignment->id,
                                            'teacher_id' => $teacherId,
                                            'subject_id' => $subjectId,
                                            'day' => $day,
                                            'period' => $p,
                                            'room' => 'R.' . $classroom->name,
                                        ]);
                                        $classBusy[$classId][$day][$p] = true;
                                        $teacherBusy[$teacherId][$day][$p] = true;
                                        $totalPlaced++;
                                        $singlePlaced = true;
                                        break;
                                    }
                                }
                                if ($singlePlaced) break;
                            }

                            if (!$singlePlaced) {
                                $allUnplaced[] = [
                                    'classroom' => $classroom->name,
                                    'teacher' => $assignment->teacher->name,
                                    'subject' => $assignment->subject->name,
                                    'hours' => 1,
                                ];
                            }
                        }
                    }
                }
            }

            return [
                'success' => true,
                'message' => "Jadwal untuk Kelas {$classroom->name} berhasil digenerate otomatis!",
                'total_placed' => $totalPlaced,
                'unplaced' => $allUnplaced,
                'classroom' => $classroom,
            ];
        });
    }

    /**
     * Split subject weekly hours into realistic pedagogical blocks.
     */
    private function splitIntoBlocks(int $hours): array
    {
        if ($hours <= 0) return [];
        if ($hours === 1) return [1];
        if ($hours === 2) return [2];
        if ($hours === 3) return [3];
        if ($hours === 4) return [2, 2];
        if ($hours === 5) return [3, 2];
        if ($hours === 6) return [3, 3];
        return array_fill(0, $hours, 1);
    }

    /**
     * Detect conflicts in the current schedule.
     */
    public function detectConflicts(): array
    {
        $conflicts = [];

        // Check teacher clash: 1 teacher in multiple classes at same day & period
        $teacherClashes = Schedule::select('teacher_id', 'day', 'period', DB::raw('COUNT(*) as cnt'))
            ->groupBy('teacher_id', 'day', 'period')
            ->having('cnt', '>', 1)
            ->with('teacher')
            ->get();

        foreach ($teacherClashes as $tc) {
            $details = Schedule::where('teacher_id', $tc->teacher_id)
                ->where('day', $tc->day)
                ->where('period', $tc->period)
                ->with('classroom', 'subject')
                ->get();

            $conflicts[] = [
                'type' => 'teacher_clash',
                'title' => "Bentrok Guru: {$tc->teacher->name}",
                'description' => "Guru mengajar di {$tc->cnt} kelas bersamaan pada hari {$tc->day} jam ke-{$tc->period}.",
                'classes' => $details->pluck('classroom.name')->implode(', '),
            ];
        }

        // Check classroom clash: 1 classroom with multiple schedules at same day & period
        $classClashes = Schedule::select('classroom_id', 'day', 'period', DB::raw('COUNT(*) as cnt'))
            ->groupBy('classroom_id', 'day', 'period')
            ->having('cnt', '>', 1)
            ->with('classroom')
            ->get();

        foreach ($classClashes as $cc) {
            $conflicts[] = [
                'type' => 'classroom_clash',
                'title' => "Bentrok Kelas: {$cc->classroom->name}",
                'description' => "Kelas memiliki {$cc->cnt} mata pelajaran pada hari {$cc->day} jam ke-{$cc->period}.",
            ];
        }

        return $conflicts;
    }
}
