<?php

namespace App\Http\Controllers;

use App\Imports\AssetsImport;
use App\Imports\AssetsTemplateExport;
use App\Imports\AssetsExport;
use App\Models\Asset;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Requests\ChangeAssetHolderRequest;
use App\Http\Requests\StoreAssetMaintenanceRequest;
use App\Models\AssetHolderHistory;
use App\Models\AssetMaintenance;
use App\Models\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AssetViewController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $sort = $request->input('sort');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $search = $request->input('search');
        $status = $request->input('status');
        $condition = $request->input('condition');
        $type = $request->input('type');

        $sortableColumns = [
            'asset_code' => 'asset_code',
            'name' => 'name',
            'type' => 'type',
            'brand' => 'brand',
            'model' => 'model',
            'serial_number' => 'serial_number',
            'purchased_at' => 'purchased_at',
            'nilai_perolehan' => 'nilai_perolehan',
            'location' => 'location',
            'kode_satker' => 'kode_satker',
            'holder' => 'holder',
            'status' => 'status',
            'condition' => 'condition',
        ];

        $q = Asset::query();

        if ($search) {
            $q->where(function ($query) use ($search) {
                $query->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('holder', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $q->where('status', $status);
        }

        if ($condition) {
            $q->where('condition', $condition);
        }

        if ($type) {
            $q->where('type', 'like', "%{$type}%");
        }

        if (array_key_exists($sort, $sortableColumns)) {
            $q->orderBy($sortableColumns[$sort], $direction);
        } else {
            $q->latest();
        }

        $assetTypes = Asset::query()
            ->select('type')
            ->whereNotNull('type')
            ->where('type', '<>', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50]) ? (int)$perPage : 10;
        
        $assets = $q->paginate($perPage)->appends(request()->query());
        return view('assets.index', compact('assets', 'sort', 'direction', 'assetTypes'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();

        // Handle serial photo (file or URL)
        if ($photoSerial = $this->resolvePhotoField($request, 'photo_serial', 'photo_serial_url')) {
            $data['photo_serial'] = $photoSerial;
        }

        // Handle asset photo (file or URL)
        if ($photoAsset = $this->resolvePhotoField($request, 'photo_asset', 'photo_asset_url')) {
            $data['photo_asset'] = $photoAsset;
        }

        $asset = Asset::create($data);
        
        // Send notification
        $this->notificationService->notifyAssetCreated($request->user(), $asset);

        return redirect()->route('assets.show', $asset)->with('success', 'Asset created');
    }

    private function resolvePhotoField(Request $request, string $fileKey, string $urlKey): ?string
    {
        if ($request->hasFile($fileKey)) {
            return $request->file($fileKey)->store('assets', 'public');
        }

        if ($request->filled($urlKey)) {
            $url = $request->input($urlKey);
            if ($id = Asset::extractGoogleDriveId($url)) {
                return 'drive:' . $id;
            }

            return $url;
        }

        return null;
    }

    public function show(Asset $asset)
    {
        $asset->load(['holderHistory.changedByUser', 'maintenances.performedByUser']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', compact('asset'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $changes = $request->validated();

        // Handle uploads or URLs for serial photo
        if ($photoSerial = $this->resolvePhotoField($request, 'photo_serial', 'photo_serial_url')) {
            $changes['photo_serial'] = $photoSerial;
        }

        // Handle uploads or URLs for asset photo
        if ($photoAsset = $this->resolvePhotoField($request, 'photo_asset', 'photo_asset_url')) {
            $changes['photo_asset'] = $photoAsset;
        }
        $before = $asset->only(array_keys($changes));

        $asset->update($changes);

        Log::create([
            'actor_id' => auth()->id(),
            'entity_type' => 'Asset',
            'entity_id' => $asset->id,
            'action' => 'UPDATED',
            'meta' => [
                'before' => $before,
                'after' => $asset->only(array_keys($changes)),
            ],
        ]);

        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted');
    }

    public function downloadTemplate()
    {
        abort_unless(auth()->user()->can('manage assets'), 403);

        $filename = 'template-aset.xlsx';

        // Delivery langsung, tanpa ketergantungan file system lokal
        return Excel::download(new AssetsTemplateExport(), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function import(Request $request)
    {
        abort_unless(auth()->user()->can('manage assets'), 403);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');

        try {
            Excel::import(new AssetsImport(), $file);
        } catch (\Exception $ex) {
            return redirect()->route('assets.index')->with('error', 'Import gagal: ' . $ex->getMessage());
        }

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil diimpor dari Excel.');
    }

    public function export(Request $request)
    {
        abort_unless(auth()->user()->can('manage assets'), 403);

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'period' => 'nullable|string',
        ]);

        $startDate = null;
        $endDate = null;
        $year = now()->year;

        // Jika ada periode preset yang dipilih
        if ($request->period) {
            switch ($request->period) {
                case 'q1':
                    $startDate = "{$year}-01-01";
                    $endDate = "{$year}-03-31";
                    break;
                case 'q2':
                    $startDate = "{$year}-04-01";
                    $endDate = "{$year}-06-30";
                    break;
                case 'q3':
                    $startDate = "{$year}-07-01";
                    $endDate = "{$year}-09-30";
                    break;
                case 'q4':
                    $startDate = "{$year}-10-01";
                    $endDate = "{$year}-12-31";
                    break;
                case 'year':
                    $startDate = "{$year}-01-01";
                    $endDate = "{$year}-12-31";
                    break;
            }
        } else {
            // Gunakan tanggal yang dipilih user
            $startDate = $request->start_date;
            $endDate = $request->end_date;
        }

        $filename = 'aset-' . ($startDate ? \Carbon\Carbon::parse($startDate)->format('Y-m-d') : 'all') .
                   '-to-' . ($endDate ? \Carbon\Carbon::parse($endDate)->format('Y-m-d') : 'all') .
                   '-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new AssetsExport($startDate, $endDate), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function changeHolder(Asset $asset)
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);
        
        return view('assets.change-holder', compact('asset'));
    }

    public function storeChangeHolder(ChangeAssetHolderRequest $request, Asset $asset)
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);

        $previousHolder = $asset->holder;
        
        // Create history record
        AssetHolderHistory::create([
            'asset_id' => $asset->id,
            'previous_holder' => $previousHolder,
            'new_holder' => $request->new_holder,
            'changed_at' => $request->changed_at,
            'notes' => $request->notes,
            'changed_by_user_id' => auth()->id(),
        ]);

        // Update asset holder
        $asset->update(['holder' => $request->new_holder]);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Pemegang aset berhasil diubah dari ' . ($previousHolder ?? '-') . ' menjadi ' . $request->new_holder);
    }

    public function addMaintenance(Asset $asset)
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);
        
        $maintenanceTypes = AssetMaintenance::types();
        
        return view('assets.add-maintenance', compact('asset', 'maintenanceTypes'));
    }

    public function storeMaintenance(StoreAssetMaintenanceRequest $request, Asset $asset)
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);

        $maintenance = AssetMaintenance::create([
            'asset_id' => $asset->id,
            'type' => $request->type,
            'maintenance_date' => $request->maintenance_date,
            'description' => $request->description,
            'findings' => $request->findings,
            'actions_taken' => $request->actions_taken,
            'condition_before' => $request->condition_before ?? $asset->condition,
            'condition_after' => $request->condition_after,
            'performed_by_user_id' => auth()->id(),
            'next_maintenance_date' => $request->next_maintenance_date,
        ]);

        // Update asset condition if condition_after is provided
        if ($request->condition_after) {
            $asset->update(['condition' => $request->condition_after]);
        }

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Perawatan aset berhasil dicatat');
    }
}

