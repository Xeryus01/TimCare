<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PiketSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_start_date',
        'week_end_date',
        'technician_1',
        'technician_2',
        'technician_3',
    ];

    protected $casts = [
        'week_start_date' => 'date:Y-m-d',
        'week_end_date' => 'date:Y-m-d',
    ];

    // Get current week schedule
    public static function getCurrentWeek()
    {
        return self::forDate(now());
    }

    public static function forDate($date = null)
    {
        $currentDate = $date ? Carbon::parse($date) : now();
        $weekStart = $currentDate->copy()->startOfWeek(); // Monday

        return self::whereDate('week_start_date', $weekStart->toDateString())
            ->first() ?? self::makeDefault($weekStart);
    }

    public static function findForDate($date = null)
    {
        $currentDate = $date ? Carbon::parse($date) : now();
        $weekStart = $currentDate->copy()->startOfWeek(); // Monday

        return self::whereDate('week_start_date', $weekStart->toDateString())
            ->first();
    }

    public static function scheduledTechniciansForDate($date = null)
    {
        $schedule = self::findForDate($date);

        return $schedule ? $schedule->scheduledUsers() : collect();
    }

    public function scheduledUserNames(): array
    {
        return collect([$this->technician_1, $this->technician_2, $this->technician_3])
            ->map(fn($name) => trim($name))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function scheduledUsers()
    {
        $names = $this->scheduledUserNames();

        if (empty($names)) {
            return User::role('Teknisi')->orderBy('name')->get();
        }

        $lowerNames = array_map(fn($name) => strtolower(trim($name)), $names);

        return User::role('Teknisi')
            ->where(function ($query) use ($lowerNames) {
                foreach ($lowerNames as $name) {
                    $query->orWhereRaw('LOWER(TRIM(name)) = ?', [$name]);
                }
            })
            ->orderBy('name')
            ->get();
    }

    // Build an in-memory default schedule WITHOUT saving it to the database.
    // Used for display/edit so that merely viewing a week never auto-creates a row.
    public static function makeDefault($weekStart)
    {
        $technicians = self::getTechnicians();

        return new self([
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
            'technician_1' => $technicians[0] ?? 'Fadil Rahman',
            'technician_2' => $technicians[1] ?? 'Marko Santoso',
            'technician_3' => $technicians[2] ?? 'Eji Wijaya',
        ]);
    }

    // Create default schedule if not exists
    public static function createDefault($weekStart)
    {
        $technicians = self::getTechnicians();
        
        return self::firstOrCreate(
            ['week_start_date' => $weekStart->toDateString()],
            [
                'week_end_date' => $weekStart->copy()->endOfWeek()->toDateString(),
                'technician_1' => $technicians[0] ?? 'Fadil Rahman',
                'technician_2' => $technicians[1] ?? 'Marko Santoso',
                'technician_3' => $technicians[2] ?? 'Eji Wijaya',
            ]
        );
    }

    // Get list of all technicians from users with Teknisi role
    public static function getTechnicians()
    {
        return User::role('Teknisi')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    // Generate dynamic color palette for technicians
    public static function generateColorPalette()
    {
        $colors = [
            ['dot' => 'bg-blue-400', 'accent' => 'from-blue-400 to-blue-500'],
            ['dot' => 'bg-emerald-400', 'accent' => 'from-emerald-400 to-emerald-500'],
            ['dot' => 'bg-purple-400', 'accent' => 'from-purple-400 to-purple-500'],
            ['dot' => 'bg-rose-400', 'accent' => 'from-rose-400 to-rose-500'],
            ['dot' => 'bg-amber-400', 'accent' => 'from-amber-400 to-amber-500'],
            ['dot' => 'bg-cyan-400', 'accent' => 'from-cyan-400 to-cyan-500'],
            ['dot' => 'bg-pink-400', 'accent' => 'from-pink-400 to-pink-500'],
            ['dot' => 'bg-lime-400', 'accent' => 'from-lime-400 to-lime-500'],
        ];
        
        return $colors;
    }

    // Get color map for technicians with consistent color assignment
    public static function getTechnicianColorMap()
    {
        $technicians = self::getTechnicians();
        $colors = self::generateColorPalette();
        $colorMap = [];

        foreach ($technicians as $index => $technician) {
            // Use modulo to cycle through colors if more technicians than colors
            $colorIndex = $index % count($colors);
            $colorMap[$technician] = $colors[$colorIndex];
        }

        return $colorMap;
    }
}
