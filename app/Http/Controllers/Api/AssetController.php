<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function __construct(private AuditTrailService $auditTrailService) {}
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Asset::query();

        if (! $user->hasAnyRole(['Admin', 'Teknisi'])) {
            $q->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                $name = trim((string) $user->name);
                if ($name !== '') {
                    $query->orWhereRaw('LOWER(holder) = ?', [mb_strtolower($name)]);
                }
            });
        }

        return AssetResource::collection($q->paginate(20));
    }

    public function show(Request $request, Asset $asset)
    {
        $this->authorizeAssetView($request, $asset);

        return new AssetResource($asset->load('user'));
    }

    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();
        unset(
            $data['photo_serial'],
            $data['photo_serial_url'],
            $data['photo_asset'],
            $data['photo_asset_url'],
            $data['photo_bmn'],
            $data['photo_bmn_url'],
        );

        foreach ([
            ['photo_serial', 'photo_serial_url'],
            ['photo_asset', 'photo_asset_url'],
            ['photo_bmn', 'photo_bmn_url'],
        ] as [$fileKey, $urlKey]) {
            if ($value = $this->resolvePhotoField($request, $fileKey, $urlKey)) {
                $data[$fileKey] = $value;
            }
        }

        $asset = Asset::create($data);

        $this->auditTrailService->record($request, $request->user()->id, 'Asset', $asset->id, 'CREATED', $asset->toArray());

        return (new AssetResource($asset))->response()->setStatusCode(201);
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        if (! $request->user()->hasAnyRole(['Admin', 'Teknisi'])) {
            abort(403, 'Perubahan aset melalui API hanya dapat dilakukan oleh Admin atau Teknisi.');
        }

        $changes = $request->validated();
        unset(
            $changes['photo_serial'],
            $changes['photo_serial_url'],
            $changes['photo_asset'],
            $changes['photo_asset_url'],
            $changes['photo_bmn'],
            $changes['photo_bmn_url'],
        );

        $oldPhotoPaths = [];
        $newPhotoPaths = [];

        foreach ([
            ['photo_serial', 'photo_serial_url'],
            ['photo_asset', 'photo_asset_url'],
            ['photo_bmn', 'photo_bmn_url'],
        ] as [$fileKey, $urlKey]) {
            $newValue = $this->resolvePhotoField($request, $fileKey, $urlKey);
            if ($newValue === null) {
                continue;
            }

            $oldValue = $asset->{$fileKey};
            if ($oldValue && ! Str::startsWith($oldValue, ['http://', 'https://', 'drive:'])
                && Storage::disk('public')->exists($oldValue)) {
                $oldPhotoPaths[] = $oldValue;
            }
            if (! Str::startsWith($newValue, ['http://', 'https://', 'drive:'])) {
                $newPhotoPaths[] = $newValue;
            }

            $changes[$fileKey] = $newValue;
        }

        $before = $asset->only(array_keys($changes));

        try {
            $asset->update($changes);
        } catch (\Throwable $exception) {
            foreach ($newPhotoPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $exception;
        }

        foreach ($oldPhotoPaths as $path) {
            if (! in_array($path, $newPhotoPaths, true)) {
                Storage::disk('public')->delete($path);
            }
        }

        $this->auditTrailService->record($request, $request->user()->id, 'Asset', $asset->id, 'UPDATED', [
            'before' => $before,
            'after' => $asset->only(array_keys($changes)),
        ]);

        return new AssetResource($asset);
    }

    public function destroy(Request $request, Asset $asset)
    {
        if (! $request->user()->hasAnyRole(['Admin', 'Teknisi'])) {
            abort(403, 'Hanya Admin atau Teknisi yang dapat menghapus aset.');
        }

        foreach (['photo_serial', 'photo_asset', 'photo_bmn'] as $field) {
            $path = $asset->{$field};
            if ($path && ! Str::startsWith($path, ['http://', 'https://', 'drive:'])
                && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $asset->delete();
        $this->auditTrailService->record($request, $request->user()->id, 'Asset', $asset->id, 'DELETED');
        return response()->noContent();
    }

    private function authorizeAssetView(Request $request, Asset $asset): void
    {
        $user = $request->user();

        if ($user->hasAnyRole(['Admin', 'Teknisi'])) {
            return;
        }

        $isOwner = $asset->user_id === $user->id
            || mb_strtolower(trim((string) $asset->holder)) === mb_strtolower(trim((string) $user->name));

        abort_unless($isOwner, 403);
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
}
