import json
from collections import Counter

# Read the assets JSON file
with open('assets_data.json', 'r') as f:
    assets = json.load(f)

# Count by various attributes
categories = Counter(asset.get('Jenis Barang / Kategori') for asset in assets)
brands = Counter(asset.get('Merek') for asset in assets)
conditions = Counter(asset.get('Kondisi') for asset in assets)
statuses = Counter(asset.get('Status') for asset in assets)
locations = Counter(asset.get('Lokasi Aset') for asset in assets)

print("=" * 60)
print("ASSET DATA STATISTICS")
print("=" * 60)
print(f"\nTotal Assets: {len(assets)}\n")

print("ASSETS BY CATEGORY (Top 15):")
for cat, count in categories.most_common(15):
    percentage = (count / len(assets)) * 100
    print(f"  • {cat}: {count} ({percentage:.1f}%)")

print("\nASSETS BY BRAND (Top 20):")
for brand, count in brands.most_common(20):
    percentage = (count / len(assets)) * 100
    print(f"  • {brand}: {count} ({percentage:.1f}%)")

print("\nASSETS BY CONDITION:")
for cond, count in sorted(conditions.items()):
    percentage = (count / len(assets)) * 100
    print(f"  • {cond}: {count} ({percentage:.1f}%)")

print("\nASSETS BY ALLOCATION STATUS:")
for stat, count in sorted(statuses.items()):
    percentage = (count / len(assets)) * 100
    print(f"  • {stat}: {count} ({percentage:.1f}%)")

print("\nASSETS BY LOCATION:")
for loc, count in sorted(locations.items(), key=lambda x: -x[1]):
    percentage = (count / len(assets)) * 100
    print(f"  • {loc}: {count} ({percentage:.1f}%)")

print("\n" + "=" * 60)
