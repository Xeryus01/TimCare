# Asset Seeder Usage Guide

## What it does

`AssetSeeder` replaces the asset inventory with the 312 rows in
`database/data/aset-berwujud-2026-11-05-142737.xlsx`. It runs in a database
transaction and verifies the imported row count before committing.

Before deleting the old assets, the seeder saves linked ticket asset tags and
temporarily detaches the tickets. It relinks tickets when the same Asset Tag is
present in the new workbook; otherwise the ticket remains with no asset. Asset
holder-history and maintenance rows are tied to the replaced records and are
deleted by their foreign-key cascades. Back up the database before running the
seeder.

The default `DatabaseSeeder` does not run `AssetSeeder`. Run it explicitly so
ordinary seed commands cannot replace the production inventory by accident.

## Deployment

Deploy the workbook and code, then run:

```bash
php artisan migrate --force
php artisan db:seed --class=AssetSeeder --force
```

The migration changes the `tickets.asset_id` foreign key to `ON DELETE SET
NULL`, so deleting an asset in the application no longer deletes its ticket.
The seeder also detaches/relinks tickets explicitly during replacement.

## Verify the import

```bash
php artisan tinker
>>> App\Models\Asset::count()
=> 312
>>> App\Models\Asset::where('status', 'Teralokasi')->count()
=> 241
>>> App\Models\Asset::where('status', 'Pengajuan Alokasi')->count()
=> 29
>>> App\Models\Asset::where('condition', 'Rusak Ringan')->count()
=> 144
```

Status and condition values are kept in the same Indonesian wording as the
workbook:

| Workbook status | Count |
|---|---:|
| Teralokasi | 241 |
| Tidak Dapat Dialokasikan | 20 |
| Pengajuan Alokasi | 29 |
| Penetapan Pengembalian | 1 |
| Pengajuan Pengembalian | 2 |
| Siap Dialokasikan | 19 |

Condition counts are Baik (119), Rusak Ringan (144), and Rusak Berat (49).
Placeholder/non-numeric NIP values are stored as `NULL`. Asset Tags are
unique; NO BMN values are not required to be unique.
For all 68 employee-held assets, the workbook's `Nama Pegawai` is reconciled
to the canonical employee name in the supplied `pegawai.json` using NIP. The
JSON is not required at runtime; only the reconciled workbook is bundled.
When a matching account exists, the asset is linked to that existing user; the
seeder does not create user accounts.

Run the focused tests with:

```bash
php artisan test --filter=AssetSeederTest
```

## Related files

- Seeder: `database/seeders/AssetSeeder.php`
- Workbook: `database/data/aset-berwujud-2026-11-05-142737.xlsx`
- Ticket foreign-key migration:
  `database/migrations/2026_11_05_000001_preserve_tickets_when_assets_are_deleted.php`
- Import mapping: `app/Imports/AssetsImport.php`
