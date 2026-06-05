# Asset Seeder - Installation & Usage Guide

## Quick Start

### 1. Run the Asset Seeder

```bash
cd /path/to/timcare

# Seed assets into database
php artisan db:seed --class=AssetSeeder
```

**Expected Output:**
```
Seeding: Database\Seeders\AssetSeeder
Seeded:  Database\Seeders\AssetSeeder (XXXs)
```

### 2. Verify Assets Were Loaded

```bash
# Check asset count
php artisan tinker
>>> App\Models\Asset::count()
=> 771

# Check by condition
>>> App\Models\Asset::where('condition', 'GOOD')->count()
=> 447

# Check by status
>>> App\Models\Asset::where('status', 'ACTIVE')->count()
=> 606

# Check by location
>>> App\Models\Asset::where('location', 'BPS Provinsi Kep. Bangka Belitung')->count()
=> 326
```

## Available Seeders

### AssetSeeder
**File**: `database/seeders/AssetSeeder.php`

Contains 771 complete asset records extracted from Excel template.

**Usage:**
```bash
php artisan db:seed --class=AssetSeeder
```

**Features:**
- All 771 assets from template
- Normalized conditions (GOOD/LIGHT/HEAVY)
- Mapped statuses (ACTIVE/INACTIVE)
- Unique asset codes
- Safe re-run (firstOrCreate)

### AssetAttributesSeeder
**File**: `database/seeders/AssetAttributesSeeder.php`

Reference data and documentation for asset attributes.

**Usage:**
```bash
php artisan db:seed --class=AssetAttributesSeeder
```

**Displays:**
- 27 Asset categories
- 29 Manufacturers/brands
- 3 Condition levels
- 2 Status values
- 8 Locations

## Common Queries

### Get Assets by Condition

```php
use App\Models\Asset;

// Good condition (Baik)
$goodAssets = Asset::where('condition', 'GOOD')->get();

// Light damage (Rusak Ringan)
$lightAssets = Asset::where('condition', 'LIGHT')->get();

// Heavy damage (Rusak Berat)
$heavyAssets = Asset::where('condition', 'HEAVY')->get();
```

### Get Assets by Status

```php
// Active assets (allocated or ready)
$activeAssets = Asset::where('status', 'ACTIVE')->get();

// Inactive assets (cannot allocate)
$inactiveAssets = Asset::where('status', 'INACTIVE')->get();
```

### Get Assets by Location

```php
// Provincial office
$provincial = Asset::where('location', 'BPS Provinsi Kep. Bangka Belitung')->get();

// By city/district
$city = Asset::where('location', 'BPS Kota Pangkal Pinang')->get();
$district = Asset::where('location', 'BPS Kabupaten Bangka')->get();
```

### Get Assets by Type

```php
// Laptops
$laptops = Asset::where('type', 'Lap Top')->get();

// Desktops
$desktops = Asset::where('type', 'P.C Unit')->get();

// Printers
$printers = Asset::where('type', 'Printer (Peralatan Personal Komputer)')->get();

// Network equipment
$network = Asset::where('type', 'like', '%Switch%')->orWhere('type', 'Router')->get();
```

### Get Assets by Brand

```php
// Dell assets
$dell = Asset::where('brand', 'Dell')->get();

// HP assets
$hp = Asset::where('brand', 'Hp')->get();

// Lenovo assets
$lenovo = Asset::where('brand', 'Lenovo')->get();
```

### Get Assets by Holder/Employee

```php
use App\Models\Asset;

// Assets assigned to an employee
$held = Asset::whereNotNull('holder')->get();

// Unassigned assets
$unassigned = Asset::whereNull('holder')->get();

// Search by employee name
$held = Asset::where('holder', 'like', '%Name%')->get();
```

## Statistics Queries

### Get Statistics

```php
use App\Models\Asset;

// Total assets
$total = Asset::count(); // 771

// By condition
$byCondition = Asset::groupBy('condition')
    ->selectRaw('condition, count(*) as count')
    ->get();

// By status
$byStatus = Asset::groupBy('status')
    ->selectRaw('status, count(*) as count')
    ->get();

// By location
$byLocation = Asset::groupBy('location')
    ->selectRaw('location, count(*) as count')
    ->orderByDesc('count')
    ->get();

// By type
$byType = Asset::groupBy('type')
    ->selectRaw('type, count(*) as count')
    ->orderByDesc('count')
    ->get();

// By brand
$byBrand = Asset::groupBy('brand')
    ->selectRaw('brand, count(*) as count')
    ->orderByDesc('count')
    ->get();
```

### Advanced Queries

```php
// Assets that need maintenance (not in good condition)
$needsMaintenance = Asset::whereIn('condition', ['LIGHT', 'HEAVY'])->get();

// High-value assets (expensive items - P.C, Server, etc)
$expensive = Asset::whereIn('type', ['Server', 'PC Workstation', 'Lap Top'])
    ->where('condition', 'GOOD')
    ->get();

// Ready for allocation
$readyToAllocate = Asset::where('status', 'ACTIVE')
    ->where('condition', 'GOOD')
    ->whereNull('holder')
    ->get();

// Damaged beyond repair (cannot allocate)
$scrap = Asset::where('status', 'INACTIVE')
    ->where('condition', 'HEAVY')
    ->get();
```

## Troubleshooting

### Seeder Not Found
```bash
# Ensure seeders are discoverable
php artisan package:discover

# Check if file exists
ls database/seeders/AssetSeeder.php
```

### Duplicate Key Error
If you get "Duplicate entry for asset_code":
- The seeder uses `firstOrCreate()` - safe to re-run
- Or manually delete assets first: `php artisan tinker` → `App\Models\Asset::truncate()`

### Date Format Issues
All dates are stored as YYYY-MM-DD:
```php
$asset = Asset::first();
$asset->purchased_at; // Returns Carbon date instance
```

### Missing Employee Names
If holder is NULL, it means the original Excel had "Belum dialokasikan" (Not yet allocated)

## Testing the Seeder

### Via Artisan Tinker

```bash
php artisan tinker

# Count total
>>> App\Models\Asset::count()
=> 771

# Check first asset
>>> App\Models\Asset::first()
=> App\Models\Asset {#4273
     id: 1,
     asset_code: "3100102002-7256",
     name: "Dell Latitude 7320",
     type: "Lap Top",
     brand: "Dell",
     location: "BPS Provinsi Kep. Bangka Belitung",
     holder: "Eka Riezalita Pattinama, S.IP",
     status: "ACTIVE",
     condition: "GOOD",
     purchased_at: "2021-11-18",
   }

# Check statistics
>>> App\Models\Asset::where('condition', 'GOOD')->count()
=> 447

>>> App\Models\Asset::where('condition', 'HEAVY')->count()
=> 165

>>> App\Models\Asset::where('status', 'ACTIVE')->count()
=> 606
```

### Via PHP Unit Tests

Create `tests/Feature/AssetSeederTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Asset;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AssetSeederTest extends TestCase
{
    use DatabaseMigrations;

    public function test_all_assets_are_seeded()
    {
        $this->seed(AssetSeeder::class);
        
        $this->assertEquals(771, Asset::count());
    }

    public function test_condition_values_are_valid()
    {
        $this->seed(AssetSeeder::class);
        
        $conditions = Asset::distinct()->pluck('condition')->toArray();
        
        $this->assertTrue(in_array('GOOD', $conditions));
        $this->assertTrue(in_array('LIGHT', $conditions));
        $this->assertTrue(in_array('HEAVY', $conditions));
    }

    public function test_asset_codes_are_unique()
    {
        $this->seed(AssetSeeder::class);
        
        $total = Asset::count();
        $unique = Asset::distinct()->pluck('asset_code')->count();
        
        $this->assertEquals($total, $unique);
    }
}
```

Run tests:
```bash
php artisan test
```

## Performance Notes

- **Seeding Time**: ~5-10 seconds for 771 assets
- **Database Size**: ~2-3 MB for all asset records
- **Query Performance**: Indexed on asset_code for fast lookups

## Maintenance

### Cleaning Up

```bash
# Delete all assets
php artisan tinker
>>> App\Models\Asset::truncate()

# Re-seed
>>> exit
php artisan db:seed --class=AssetSeeder
```

### Updating Assets

```php
use App\Models\Asset;

// Update an asset
$asset = Asset::where('asset_code', '3100102002-7256')->first();
$asset->update([
    'holder' => 'New Employee Name',
    'condition' => 'LIGHT',
]);

// Bulk update
Asset::where('type', 'Lap Top')
    ->where('condition', 'GOOD')
    ->update(['status' => 'ACTIVE']);
```

## Related Documentation

- [Asset Data Documentation](ASSET_DATA_DOCUMENTATION.md)
- [Implementation Report](ASSET_SEEDER_IMPLEMENTATION_REPORT.md)
- [Asset Model](app/Models/Asset.php)

---

**Last Updated**: 2026-06-05
**Total Assets**: 771
**Categories**: 27
**Brands**: 29
**Locations**: 8
