# Asset Management System - Data Documentation

## Overview
This document describes the asset data loaded into the system from the Excel template (`asset-upload-template.xlsx`). The system now contains **771 assets** across **27 categories**, **29 brands**, **3 condition levels**, **3 allocation statuses**, and **8 office locations**.

## Data Source
- **File**: `asset-upload-template.xlsx`
- **Total Assets**: 771
- **Import Date**: 2026-06-05
- **Seeder Files**: 
  - `database/seeders/AssetSeeder.php` - Main asset data (771 entries)
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

Assets are classified into three condition categories:

| Code | Indonesian | English | Excel Value |
|------|-----------|---------|-------------|
| `GOOD` | Baik | Good | Baik |
| `LIGHT` | Rusak Ringan | Light Damage | Rusak Ringan |
| `HEAVY` | Rusak Berat | Heavy Damage | Rusak Berat |

### Condition Distribution:
- **GOOD (Baik)**: Assets in good working condition
- **LIGHT (Rusak Ringan)**: Assets with minor damage, still usable
- **HEAVY (Rusak Berat)**: Assets with major damage, not usable

## Allocation Status

Assets have the following allocation statuses mapped to the system:

| System Status | Indonesian | Original Excel Values |
|---------------|-----------|---------------------|
| `ACTIVE` | Aktif | Teralokasi, Siap Dialokasikan |
| `INACTIVE` | Tidak Aktif | Tidak Dapat Dialokasikan |

### Mapping:
- **Teralokasi** (Allocated) → `ACTIVE`
- **Siap Dialokasikan** (Ready to Allocate) → `ACTIVE`
- **Tidak Dapat Dialokasikan** (Cannot Allocate) → `INACTIVE`

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
| NO BMN | asset_code | Primary identifier, unique |
| Nama | name | Asset name/description |
| Asset Tag | serial_number | Tag/serial number |
| Jenis Barang / Kategori | type | Asset category |
| Merek | brand | Manufacturer |
| Tanggal Perolehan | purchased_at | Acquisition date |
| Lokasi Aset | location | Current location |
| Nama Pegawai | holder | Assigned employee (if applicable) |
| Kondisi | condition | Normalized to GOOD/LIGHT/HEAVY |
| Status | status | Mapped to ACTIVE/INACTIVE |

## Additional Fields

The following fields are set during seeding but not in the Excel:

- **model**: Empty (can be filled manually)
- **specs**: Null (can be populated later)
- **status**: Derived from Excel "Status" column
- **condition**: Normalized from Excel "Kondisi" column

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
$goodAssets = Asset::where('condition', 'GOOD')->get();

// Get by status
$activeAssets = Asset::where('status', 'ACTIVE')->get();

// Get by location
$assetsByLocation = Asset::where('location', 'BPS Provinsi Kep. Bangka Belitung')->get();

// Get by type
$laptops = Asset::where('type', 'Lap Top')->get();
```

## Statistics

Based on the extracted data:

- **Total Assets**: 771
- **Asset Categories**: 27 types
- **Manufacturers**: 29 brands
- **Locations**: 8 offices
- **Condition Statuses**: 3 levels
- **Allocation Statuses**: 2 states

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

2. **Condition Normalization**: The condition values from Excel are normalized to the system's standard conditions (GOOD, LIGHT, HEAVY) to ensure data consistency.

3. **Status Mapping**: The detailed Excel statuses are mapped to the system's two-state model (ACTIVE/INACTIVE) for operational efficiency.

4. **Unique Constraint**: The `asset_code` field is unique and cannot be duplicated. The seeder uses `firstOrCreate` to prevent duplicate entries.

5. **Audit Trail**: All asset operations are logged through the system's audit log for compliance and tracking purposes.

## Related Files

- **Seeder**: [database/seeders/AssetSeeder.php](database/seeders/AssetSeeder.php)
- **Attributes Seeder**: [database/seeders/AssetAttributesSeeder.php](database/seeders/AssetAttributesSeeder.php)
- **Asset Model**: [app/Models/Asset.php](app/Models/Asset.php)
- **Migration**: [database/migrations/2026_03_03_000100_create_assets_table.php](database/migrations/2026_03_03_000100_create_assets_table.php)

---

Generated: 2026-06-05
