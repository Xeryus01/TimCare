<?php

namespace App\Http\Controllers;

use App\Models\PiketSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PiketScheduleController extends Controller
{
    // Show all piket schedules
    public function index(Request $request)
    {
        // Semua jadwal untuk kalender + statistik (tetap lengkap, tidak difilter)
        $schedules = PiketSchedule::orderBy('week_start_date', 'asc')->get();

        // Filter daftar tabel: per bulan (1), per 2 bulan (2), per 3 bulan (3), atau semua (pagination)
        $range = (string) $request->input('range', '1');
        if (! in_array($range, ['1', '2', '3', 'all'], true)) {
            $range = '1';
        }

        $listQuery = PiketSchedule::orderBy('week_start_date', 'asc');

        if ($range === 'all') {
            $scheduleList = $listQuery->paginate(12)->appends($request->query());
        } else {
            $months = (int) $range;
            $startWindow = now()->startOfMonth()->toDateString();
            $endWindow = now()->addMonths($months - 1)->endOfMonth()->toDateString();
            $scheduleList = $listQuery
                ->overlapping($startWindow, $endWindow)
                ->get();
        }

        $technicians = PiketSchedule::getTechnicians();

        return view('admin.piket.index', compact('schedules', 'scheduleList', 'range', 'technicians'));
    }

    // Show edit form for a specific saved schedule.
    public function edit(PiketSchedule $schedule)
    {
<<<<<<< HEAD
        if ($schedule->hasPassed()) {
            return redirect()->route('piket.index')
                ->with('error', 'Jadwal yang sudah terlewat tidak dapat diedit.');
        }
=======
        $weekStartDate = \Carbon\Carbon::parse($weekStart)->startOfWeek();
        $schedule = PiketSchedule::whereDate('week_start_date', $weekStartDate->toDateString())
            ->first() ?? PiketSchedule::makeDefault($weekStartDate);
>>>>>>> 86283643a095c48bf1433b50ced58effc4036f8f

        $technicians = PiketSchedule::getTechnicians();

        return view('admin.piket.edit', compact('schedule', 'technicians'));
    }

    // Update piket schedule
    public function update(Request $request, PiketSchedule $schedule)
    {
<<<<<<< HEAD
        abort_if($schedule->hasPassed(), 403, 'Jadwal yang sudah terlewat tidak dapat diedit.');
=======
        $weekStartDate = \Carbon\Carbon::parse($weekStart)->startOfWeek();
        $schedule = PiketSchedule::whereDate('week_start_date', $weekStartDate->toDateString())
            ->first() ?? PiketSchedule::makeDefault($weekStartDate);
>>>>>>> 86283643a095c48bf1433b50ced58effc4036f8f

        $request->validate([
            'week_start_date' => ['required', 'date', Rule::unique('piket_schedules', 'week_start_date')->ignore($schedule->getKey())],
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'technician_1' => 'required|string',
            'technician_2' => 'required|string|different:technician_1',
            'technician_3' => 'required|string|different:technician_1,technician_2',
        ], [
            'week_start_date.unique' => 'Tanggal mulai sudah digunakan oleh jadwal piket lain.',
            'week_end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'technician_2.different' => 'Petugas 2 tidak boleh sama dengan Petugas 1',
            'technician_3.different' => 'Petugas 3 tidak boleh sama dengan Petugas 1 atau Petugas 2',
        ]);

<<<<<<< HEAD
        $newStart = \Carbon\Carbon::parse($request->input('week_start_date'))->toDateString();
        $newEnd = \Carbon\Carbon::parse($request->input('week_end_date'))->toDateString();

        DB::transaction(function () use ($request, $schedule, $newStart, $newEnd) {
            // Kunci data jadwal selama pengecekan agar dua request tidak lolos bersamaan.
            PiketSchedule::query()->lockForUpdate()->get();
            $lockedSchedule = PiketSchedule::query()
                ->lockForUpdate()
                ->findOrFail($schedule->getKey());

            abort_if($lockedSchedule->hasPassed(), 403, 'Jadwal yang sudah terlewat tidak dapat diedit.');

            $overlapExists = PiketSchedule::overlapping($newStart, $newEnd)
                ->where($lockedSchedule->getKeyName(), '!=', $lockedSchedule->getKey())
                ->exists();

            if ($overlapExists) {
                throw ValidationException::withMessages([
                    'week_start_date' => 'Rentang tanggal ini tumpang tindih dengan jadwal piket lain. Silakan pilih rentang tanggal lain.',
                ]);
            }

            $lockedSchedule->fill($request->only([
                'week_start_date',
                'week_end_date',
                'technician_1',
                'technician_2',
                'technician_3',
            ]));
            $lockedSchedule->save();
        });
=======
        $schedule->fill($request->only(['week_start_date', 'week_end_date', 'technician_1', 'technician_2', 'technician_3']));
        $schedule->save();
>>>>>>> 86283643a095c48bf1433b50ced58effc4036f8f

        return redirect()->route('piket.index')->with('success', 'Jadwal piket berhasil diperbarui');
    }

    public function destroy(PiketSchedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('piket.index')->with('success', 'Jadwal piket berhasil dihapus');
    }

    // Show create form for new week schedule
    public function create()
    {
        $technicians = PiketSchedule::getTechnicians();

        return view('admin.piket.create', compact('technicians'));
    }

    // Store new piket schedule
    public function store(Request $request)
    {
        $request->validate([
            'week_start_date' => ['required', 'date', 'after_or_equal:' . today()->toDateString(), Rule::unique('piket_schedules', 'week_start_date')],
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'technician_1' => 'required|string',
            'technician_2' => 'required|string|different:technician_1',
            'technician_3' => 'required|string|different:technician_1,technician_2',
        ], [
            'week_start_date.unique' => 'Tanggal mulai sudah memiliki jadwal piket.',
            'week_start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'week_end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'technician_2.different' => 'Petugas 2 tidak boleh sama dengan Petugas 1',
            'technician_3.different' => 'Petugas 3 tidak boleh sama dengan Petugas 1 atau Petugas 2',
        ]);

        $weekStartDate = \Carbon\Carbon::parse($request->input('week_start_date'));
        $weekEndDate = \Carbon\Carbon::parse($request->input('week_end_date'));

        DB::transaction(function () use ($request, $weekStartDate, $weekEndDate) {
            // Kunci data jadwal selama pengecekan agar dua request tidak lolos bersamaan.
            PiketSchedule::query()->lockForUpdate()->get();

            $overlapExists = PiketSchedule::overlapping($weekStartDate, $weekEndDate)->exists();

            if ($overlapExists) {
                throw ValidationException::withMessages([
                    'week_start_date' => 'Rentang tanggal ini tumpang tindih dengan jadwal piket yang sudah ada. Silakan pilih rentang tanggal lain.',
                ]);
            }

            // Selalu buat baris baru. Jangan pernah menimpa jadwal lama dari form tambah.
            PiketSchedule::create([
                'week_start_date' => $weekStartDate->toDateString(),
                'week_end_date' => $weekEndDate->toDateString(),
                'technician_1' => $request->input('technician_1'),
                'technician_2' => $request->input('technician_2'),
                'technician_3' => $request->input('technician_3'),
            ]);
        });

        return redirect()->route('piket.index')->with('success', 'Jadwal piket berhasil ditambahkan');
    }

    // View piket schedule for technicians (read-only)
    public function view()
    {
        // Get all schedules from database, ordered by week_start_date
        $schedules = PiketSchedule::orderBy('week_start_date', 'asc')->get();
        
        $technicians = PiketSchedule::getTechnicians();

        return view('piket.view', compact('schedules', 'technicians'));
    }

    // Show current piket schedule (for display on welcome page)
    public function show()
    {
        $schedule = PiketSchedule::getCurrentWeek();

        return response()->json([
            'technician_1' => $schedule->technician_1,
            'technician_2' => $schedule->technician_2,
            'technician_3' => $schedule->technician_3,
            'week_start' => $schedule->week_start_date,
            'week_end' => $schedule->week_end_date,
        ]);
    }
}
