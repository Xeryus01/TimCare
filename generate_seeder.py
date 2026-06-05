import json
import os

# Read the assets JSON file
with open('assets_data.json', 'r', encoding='utf-8') as f:
    assets = json.load(f)

# Map conditions
def map_condition(condition):
    if condition is None:
        return "'GOOD'"
    
    cond = condition.lower().strip()
    if 'baik' in cond or cond == 'good':
        return "'GOOD'"
    elif 'ringan' in cond or 'light' in cond:
        return "'LIGHT'"
    elif 'berat' in cond or 'heavy' in cond or 'rusak' in cond:
        return "'HEAVY'"
    else:
        return "'GOOD'"

# Map status
def map_status(status):
    if status is None:
        return "'ACTIVE'"
    
    stat = status.lower().strip()
    if 'teralokas' in stat or 'siap' in stat or 'dialokasikan' in stat and 'tidak' not in stat:
        return "'ACTIVE'"
    elif 'tidak dapat' in stat or 'inactive' in stat:
        return "'INACTIVE'"
    else:
        return "'ACTIVE'"

# Generate PHP array entries
php_entries = []

for asset in assets:
    bmn = asset.get('NO BMN', '')
    name = asset.get('Nama', '').replace("'", "\\'")
    asset_tag = asset.get('Asset Tag', '')
    purchase_date = asset.get('Tanggal Perolehan', '')
    nilai_perolehan = asset.get('Nilai Perolehan', None)
    location = asset.get('Lokasi Aset', '')
    kode_satker = asset.get('Kode Satker', '')
    nip_pegawai = asset.get('NIP Pegawai', '')
    category = asset.get('Jenis Barang / Kategori', '')
    brand = asset.get('Merek', '')
    condition = asset.get('Kondisi', '')
    status = asset.get('Status', '')
    holder = asset.get('Nama Pegawai', '')
    
    # Skip if employee name is "Belum dialokasikan"
    if holder and 'belum dialokasikan' not in holder.lower():
        holder = holder.replace("'", "\\'")
    else:
        holder = None

    if isinstance(nilai_perolehan, (int, float)):
        nilai_perolehan = str(nilai_perolehan)
    else:
        nilai_perolehan = str(nilai_perolehan).replace("'", "\\'") if nilai_perolehan not in (None, '') else None
    
    asset_tag = asset_tag.replace("'", "\\'")
    purchase_date = str(purchase_date).replace("'", "\\'")
    location = location.replace("'", "\\'")
    kode_satker = str(kode_satker).replace("'", "\\'")
    nip_pegawai = str(nip_pegawai).replace("'", "\\'")
    category = category.replace("'", "\\'")
    brand = brand.replace("'", "\\'")
    
    # Map the values
    condition_mapped = map_condition(condition)
    status_mapped = map_status(status)
    
    # Create PHP array entry
    php_entry = f"""            [
                'asset_code' => '{bmn}',
                'name' => '{name}',
                'type' => '{category}',
                'brand' => '{brand}',
                'model' => '',
                'serial_number' => '{asset_tag}',
                'nilai_perolehan' => {f"'{nilai_perolehan}'" if nilai_perolehan else 'null'},
                'kode_satker' => {f"'{kode_satker}'" if kode_satker else 'null'},
                'nip_pegawai' => {f"'{nip_pegawai}'" if nip_pegawai else 'null'},
                'specs' => null,
                'location' => '{location}',
                'holder' => {f"'{holder}'" if holder else 'null'},
                'status' => {status_mapped},
                'condition' => {condition_mapped},
                'purchased_at' => '{purchase_date}',
            ],"""
    
    php_entries.append(php_entry)

# Generate the complete PHP file
php_content = f"""<?php

namespace Database\\Seeders;

use App\\Models\\Asset;
use Illuminate\\Database\\Seeder;

class AssetSeeder extends Seeder
{{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {{
        $assets = [
{chr(10).join(php_entries)},
        ];

        foreach ($assets as $asset) {{
            Asset::firstOrCreate(
                ['asset_code' => $asset['asset_code']],
                $asset
            );
        }}
    }}
}}
"""

# Save to file
output_file = 'database/seeders/AssetSeeder.php'
os.makedirs(os.path.dirname(output_file), exist_ok=True)

with open(output_file, 'w', encoding='utf-8') as f:
    f.write(php_content)

print(f"✓ Generated AssetSeeder.php with {len(assets)} assets")
