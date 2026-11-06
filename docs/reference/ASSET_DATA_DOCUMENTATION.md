# Asset Management System - Data Documentation

## Overview
The current inventory import is the 312-row workbook
`database/data/aset-berwujud-2026-11-05-142737.xlsx`. The seeder replaces the
existing asset inventory; see [Asset Seeder Usage Guide](ASSET_SEEDER_USAGE_GUIDE.md)
for the required backup, migration, and seeding steps. Category, brand, and
location lists below document the previous 771-asset snapshot and may not match
the current workbook.

## Data Source
- **File**: `database/data/aset-berwujud-2026-11-05-142737.xlsx`
- **Total Assets**: 312
- **Import Date**: 2026-11-05
- **Seeder Files**: 
  - `database/seeders/AssetSeeder.php` - Replaces inventory from the workbook
  - `database/seeders/AssetAttributesSeeder.php` - Reference attributes

## Asset Categories (27 types)

Organized by type, the system contains assets in the following categories:

### Computing Equipment
- CPU (Peralatan Personal Komputer)
- Lap Top
- Note Book
- P.C Unit
- PC Workstation
- Server
- Ultra Mobile P.C.
- Tablet PC

### Input/Output Devices
- Monitor
- Printer (Peralatan Personal Komputer)
- Scanner (Peralatan Personal Komputer)
- Viewer (Peralatan Personal Komputer)

### Storage Devices
- External
- External/ Portable Hardisk
- Hard Disk
- Rak Server
- Storage Modul Disk (Peralatan Mainframe)
- Switch Rak

### Network Equipment
- Auto Switch/Data Switch
- Firewall
- Modem
- Mobile Modem GSM/ CDMA
- Network Cable Tester
- Peralatan Jaringan Lainnya
- Router
- Switch
- Wireless Access Point

## Manufacturers/Brands (29)

The assets come from the following manufacturers:

Acer, Aruba, Asus, Axway, Brother, Buffalo, Canon, Cisco, D-Link, Dell, Epson, Fuji Xerox, Fujitsu, Hp, Huawei, Ibm, Infocus, Lenovo, Mikrotik, Panasonic, Plustek, Samsung, Seagate, Toshiba, Trendnet, Undefined, Viewsonic, Western, Xerox

## Asset Condition Levels

The import preserves the three condition values used in the current workbook:

| Stored value | English |
|------|---------|
| `Baik` | Good |
| `Rusak Ringan` | Light Damage |
| `Rusak Berat` | Heavy Damage |

### Condition Distribution:
- **Baik**: Assets in good working condition
- **Rusak Ringan**: Assets with minor damage, still usable
- **Rusak Berat**: Assets with major damage, not usable

## Allocation Status

The six status values are stored and shown using the same wording as the
workbook: `Teralokasi`, `Tidak Dapat Dialokasikan`, `Pengajuan Alokasi`,
`Penetapan Pengembalian`, `Pengajuan Pengembalian`, and `Siap Dialokasikan`.

## Office Locations (8)

Assets are distributed across the following BPS (Badan Pusat Statistik) offices:

1. BPS Provinsi Kep. Bangka Belitung (Provincial Level)
2. BPS Kota Pangkal Pinang
3. BPS Kabupaten Bangka
4. BPS Kabupaten Bangka Barat
5. BPS Kabupaten Bangka Selatan
6. BPS Kabupaten Bangka Tengah
7. BPS Kabupaten Belitung
8. BPS Kabupaten Belitung Timur

## Asset Model Mapping

The Excel columns map to the Asset model as follows:

| Excel Column | Asset Field | Notes |
|------|-----------|-------|
| NO BMN | asset_code | BMN inventory number; may be shared |
| Nama | name | Asset name/description |
| Asset Tag | serial_number | Tag/serial number |
| Jenis Barang / Kategori | type | Asset category |
| Merek | brand | Manufacturer |
| Tanggal Perolehan | purchased_at | Acquisition date |
| Lokasi Aset | location | Current location |
| Nama Pegawai | holder | Assigned employee (if applicable) |
| Kondisi | condition | Preserved as Baik/Rusak Ringan/Rusak Berat |
| Status | status | Preserved as the workbook's Indonesian status |

## Additional Fields

The following fields are set during seeding but not in the Excel:

- **model**: Empty (can be filled manually)
- **specs**: Null (can be populated later)
- **status**: Preserved from the Excel "Status" column
- **condition**: Preserved from the Excel "Kondisi" column

## Usage

### Running the Seeders

To seed the database with all assets:

```bash
# Seed only assets
php artisan db:seed --class=AssetSeeder

# View asset attributes reference
php artisan db:seed --class=AssetAttributesSeeder

# Seed everything (if configured in DatabaseSeeder)
php artisan db:seed
```

### Accessing Assets

Once seeded, assets can be accessed via:

```php
use App\Models\Asset;

// Get all assets
$assets = Asset::all();

// Get by condition
$goodAssets = Asset::where('condition', 'Baik')->get();

// Get by status
$allocatedAssets = Asset::where('status', 'Teralokasi')->get();

// Get by location
$assetsByLocation = Asset::where('location', 'BPS Provinsi Kep. Bangka Belitung')->get();

// Get by type
$laptops = Asset::where('type', 'Lap Top')->get();
```

## Previous Snapshot Statistics

The following summaries describe the former 771-asset snapshot, not the current
312-row import. Use database queries to obtain current category, brand, and
location totals.

### Top Asset Categories:
The system contains predominantly computing equipment and network infrastructure, reflecting the operational needs of a statistical agency.

### Top Locations:
- BPS Kota Pangkal Pinang
- BPS Provinsi Kep. Bangka Belitung

### Top Brands:
- Dell
- HP
- Lenovo
- Cisco
- Canon
- And others...

## Notes

1. **Employee Assignment**: When the Excel column "Nama Pegawai" (Employee Name) contains "Belum dialokasikan" (Not yet allocated), the holder field is set to NULL.

2. **Condition and status values**: Current workbook values are retained so the UI and exports match the source labels.

4. **Unique Constraint**: Asset Tags (`serial_number`) are unique. NO BMN (`asset_code`) may be shared.

5. **Audit Trail**: All asset operations are logged through the system's audit log for compliance and tracking purposes.

## Related Files

- **Seeder**: [database/seeders/AssetSeeder.php](database/seeders/AssetSeeder.php)
- **Attributes Seeder**: [database/seeders/AssetAttributesSeeder.php](database/seeders/AssetAttributesSeeder.php)
- **Asset Model**: [app/Models/Asset.php](app/Models/Asset.php)
- **Migration**: [database/migrations/2026_03_03_000100_create_assets_table.php](database/migrations/2026_03_03_000100_create_assets_table.php)

---

Generated: 2026-06-05
