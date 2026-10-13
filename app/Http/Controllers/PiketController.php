<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class PiketController extends Controller
{
    protected array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    protected array $defaultPikets = [
        'Senin' => 'FIRMANSYAH S.Ag., HANNY MILLENIA S.Pd., M. IKHLAS, S.Pd.',
        'Selasa' => 'MIFTAH EL FALAH, SE., NURHASANAH S.Pd., LEYLIA ARUM AMALUN NISA S.Pd.',
        'Rabu' => 'NANANG KOSIM S.Pd., SITI HASANAH S.Ag., IIS SURYANI S.Pd.I.',
        'Kamis' => 'ALI SUBHAN S.Ag., SITI BAROROH S.Pd., MUHAMAD RICO SAPUTRA, S.Pd.',
        'Jumat' => 'SITI RUWAIDAH, S.Pd., ARIEL BONARDO H. LIMBONG S.Pd., CITRA ARIYANTI',
    ];

    public function index()
    {
        $allTeachers = Teacher::orderBy('name')->get();
        $days = $this->days;

        // Prepare piket assignments for each day
        $piketData = [];

        foreach ($days as $day) {
            $savedIdsJson = SchoolSetting::get("piket_ids_{$day}");
            $teacherIds = $savedIdsJson ? json_decode($savedIdsJson, true) : null;

            if (is_array($teacherIds) && count($teacherIds) > 0) {
                // Fetch teachers in order of selected IDs
                $selectedTeachers = Teacher::whereIn('id', $teacherIds)->get()->sortBy(function ($t) use ($teacherIds) {
                    return array_search($t->id, $teacherIds);
                })->values();
            } else {
                // Fallback: match by saved or default string names
                $nameString = SchoolSetting::get("piket_{$day}", $this->defaultPikets[$day] ?? '');
                $selectedTeachers = $this->resolveTeachersFromString($nameString, $allTeachers);
            }

            $piketData[$day] = [
                'day' => $day,
                'teachers' => $selectedTeachers,
                'count' => $selectedTeachers->count(),
                'raw_string' => SchoolSetting::get("piket_{$day}", $this->defaultPikets[$day] ?? ''),
            ];
        }

        return view('pikets.index', compact('days', 'allTeachers', 'piketData'));
    }

    public function update(Request $request)
    {
        $teacherInputs = $request->input('teachers', []);

        foreach ($this->days as $day) {
            $selectedIds = $teacherInputs[$day] ?? [];
            if (!is_array($selectedIds)) {
                $selectedIds = [];
            }

            // Filter valid IDs and remove duplicates
            $selectedIds = array_values(array_unique(array_filter(array_map('intval', $selectedIds))));

            // Get teachers in selected order
            if (count($selectedIds) > 0) {
                $teachers = Teacher::whereIn('id', $selectedIds)->get()->sortBy(function ($t) use ($selectedIds) {
                    return array_search($t->id, $selectedIds);
                });
                $namesString = $teachers->pluck('name')->implode(', ');
            } else {
                $namesString = '';
            }

            // Save both JSON array and display string to SchoolSetting
            SchoolSetting::set("piket_ids_{$day}", json_encode($selectedIds));
            SchoolSetting::set("piket_{$day}", $namesString);
        }

        return redirect()->route('pikets.index')->with('success', 'Jadwal Guru Piket berhasil diperbarui!');
    }

    private function resolveTeachersFromString(string $namesString, $allTeachers)
    {
        if (empty(trim($namesString))) {
            return collect([]);
        }

        $tokens = array_map('trim', explode(',', $namesString));
        $matched = collect([]);

        foreach ($tokens as $token) {
            if (empty($token)) continue;
            // Clean common academic titles for matching
            $cleanToken = preg_replace('/(\bS\.Pd\b|\bM\.Pd\b|\bM\.Si\b|\bS\.Ag\b|\bM\.Hum\b|\bMM\.Pd\b|\bSE\b|\bS\.Kom\b|\bS\.TP\b|\bS\.Sos\b|\.|\,)/i', '', $token);
            $cleanToken = trim($cleanToken);

            $found = $allTeachers->first(function ($t) use ($cleanToken, $token) {
                return stripos($t->name, $cleanToken) !== false || stripos($t->name, $token) !== false;
            });

            if ($found && !$matched->contains('id', $found->id)) {
                $matched->push($found);
            }
        }

        return $matched;
    }
}
