import openpyxl
import json

wb = openpyxl.load_workbook('asset-upload-template.xlsx')
ws = wb.active

# Get all data
data = []
headers = None

for row_idx, row in enumerate(ws.iter_rows(values_only=True), 1):
    if row_idx == 1:
        headers = [h for h in row if h]
        print(f"Headers: {headers}\n")
    else:
        if row[0] is None:
            break
        data_dict = {}
        for col_idx, header in enumerate(headers):
            if col_idx < len(row):
                data_dict[header] = row[col_idx]
        data.append(data_dict)

print(f"Total Assets: {len(data)}\n")

# Display sample
if data:
    print("First Asset:")
    print(json.dumps(data[0], indent=2, default=str))
    
# Save to JSON for later use
with open('assets_data.json', 'w') as f:
    json.dump(data, f, indent=2, default=str)
    
print(f"\nSaved {len(data)} assets to assets_data.json")
