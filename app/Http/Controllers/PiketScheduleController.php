<?php

namespace App\Http\Controllers;

use App\Models\PiketSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PiketScheduleController extends Controller
{
    // Show all piket schedules
    public function index()
    {
        // Get all schedules from database, ordered by week_start_date
        $schedules = PiketSchedule::orderBy('week_start_date', 'asc')->get();
        
        $technicians = PiketSchedule::getTechnicians();

        return view('admin.piket.index', compact('schedules', 'technicians'));
    }

    // Show edit form for specific week
    public function edit($weekStart)
    {
        $weekStartDate = \Carbon\Carbon::parse($weekStart)->startOfWeek();
        $schedule = PiketSchedule::whereDate('week_start_date', $weekStartDate->toDateString())
            ->first() ?? PiketSchedule::makeDefault($weekStartDate);

        $technicians = PiketSchedule::getTechnicians();

        return view('admin.piket.edit', compact('schedule', 'technicians'));
    }

    // Update piket schedule
    public function update(Request $request, $weekStart)
    {
        $weekStartDate = \Carbon\Carbon::parse($weekStart)->startOfWeek();
        $schedule = PiketSchedule::whereDate('week_start_date', $weekStartDate->toDateString())
            ->first() ?? PiketSchedule::makeDefault($weekStartDate);

        $request->validate([
            'week_start_date' => ['required', 'date', Rule::unique('piket_schedules', 'week_start_date')->ignore($schedule->id)],
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'technician_1' => 'required|string',
            'technician_2' => 'required|string|different:technician_1',
            'technician_3' => 'required|string|different:technician_1,technician_2',
        ], [
            'week_end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'technician_2.different' => 'Petugas 2 tidak boleh sama dengan Petugas 1',
            'technician_3.different' => 'Petugas 3 tidak boleh sama dengan Petugas 1 atau Petugas 2',
        ]);

        $schedule->fill($request->only(['week_start_date', 'week_end_date', 'technician_1', 'technician_2', 'technician_3']));
        $schedule->save();

        return redirect()->route('piket.index')->with('success', 'Jadwal piket berhasil diperbarui');
    }

    public function destroy($weekStart)
    {
        $weekStartDate = \Carbon\Carbon::parse($weekStart)->startOfWeek();
        $schedule = PiketSchedule::whereDate('week_start_date', $weekStartDate->toDateString())->first();

        if ($schedule) {
            $schedule->delete();
            return redirect()->route('piket.index')->with('success', 'Jadwal piket berhasil dihapus');
        }

        return redirect()->route('piket.index')->with('error', 'Jadwal piket tidak ditemukan');
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
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'technician_1' => 'required|string',
            'technician_2' => 'required|string|different:technician_1',
            'technician_3' => 'required|string|different:technician_1,technician_2',
        ], [
            'week_end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'technician_2.different' => 'Petugas 2 tidak boleh sama dengan Petugas 1',
            'technician_3.different' => 'Petugas 3 tidak boleh sama dengan Petugas 1 atau Petugas 2',
        ]);

        $weekStartDate = \Carbon\Carbon::parse($request->input('week_start_date'));
        $weekEndDate = \Carbon\Carbon::parse($request->input('week_end_date'));

        $schedule = PiketSchedule::firstOrCreate([
            'week_start_date' => $weekStartDate->toDateString(),
        ], [
            'week_end_date' => $weekEndDate->toDateString(),
            'technician_1' => $request->input('technician_1'),
            'technician_2' => $request->input('technician_2'),
            'technician_3' => $request->input('technician_3'),
        ]);

        if (! $schedule->wasRecentlyCreated) {
            $schedule->update($request->only(['week_start_date', 'week_end_date', 'technician_1', 'technician_2', 'technician_3']));
        }

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
