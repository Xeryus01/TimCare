<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssetAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder provides reference data for asset attributes extracted from the
     * Excel template. While these are not stored in a database table (as they are
     * used directly in the Asset model as strings), this serves as documentation
     * and reference for all valid attribute values.
     */
    public function run(): void
    {
        // Asset is already seeded with AssetSeeder
        // This file serves as reference documentation
        
        echo "\n=== ASSET ATTRIBUTES REFERENCE ===\n\n";
        
        echo "ASSET CATEGORIES (27 types):\n";
        $categories = $this->getAssetCategories();
        foreach ($categories as $cat) {
            echo "  • $cat\n";
        }
        
        echo "\n\nBRAND MANUFACTURERS (29 brands):\n";
        $brands = $this->getBrands();
        foreach ($brands as $brand) {
            echo "  • $brand\n";
        }
        
        echo "\n\nCONDITION STATUS:\n";
        $conditions = [
            'GOOD' => 'Baik (Good)',
            'LIGHT' => 'Rusak Ringan (Light Damage)',
            'HEAVY' => 'Rusak Berat (Heavy Damage)',
        ];
        foreach ($conditions as $code => $label) {
            echo "  • $code: $label\n";
        }
        
        echo "\n\nALLOCATION STATUS:\n";
        $statuses = [
            'ACTIVE' => 'Teralokasi / Siap Dialokasikan (Active)',
            'INACTIVE' => 'Tidak Dapat Dialokasikan (Inactive)',
        ];
        foreach ($statuses as $code => $label) {
            echo "  • $code: $label\n";
        }
        
        echo "\n\nLOCATIONS (8 offices):\n";
        $locations = $this->getLocations();
        foreach ($locations as $loc) {
            echo "  • $loc\n";
        }
        
        echo "\n=== Total: 771 Assets Loaded ===\n\n";
    }
    
    private function getAssetCategories(): array
    {
        return [
            'Auto Switch/Data Switch',
            'CPU (Peralatan Personal Komputer)',
            'External',
            'External/ Portable Hardisk',
            'Firewall',
            'Hard Disk',
            'Lap Top',
            'Mobile Modem GSM/ CDMA',
            'Modem',
            'Monitor',
            'Network Cable Tester',
            'Note Book',
            'P.C Unit',
            'PC Workstation',
            'Peralatan Jaringan Lainnya',
            'Printer (Peralatan Personal Komputer)',
            'Rak Server',
            'Router',
            'Scanner (Peralatan Personal Komputer)',
            'Server',
            'Storage Modul Disk (Peralatan Mainframe)',
            'Switch',
            'Switch Rak',
            'Tablet PC',
            'Ultra Mobile P.C.',
            'Viewer (Peralatan Personal Komputer)',
            'Wireless Access Point',
        ];
    }
    
    private function getBrands(): array
    {
        return [
            'Acer',
            'Aruba',
            'Asus',
            'Axway',
            'Brother',
            'Buffalo',
            'Canon',
            'Cisco',
            'D-Link',
            'Dell',
            'Epson',
            'Fuji Xerox',
            'Fujitsu',
            'Hp',
            'Huawei',
            'Ibm',
            'Infocus',
            'Lenovo',
            'Mikrotik',
            'Panasonic',
            'Plustek',
            'Samsung',
            'Seagate',
            'Toshiba',
            'Trendnet',
            'Undefined',
            'Viewsonic',
            'Western',
            'Xerox',
        ];
    }
    
    private function getLocations(): array
    {
        return [
            'BPS Kabupaten Bangka',
            'BPS Kabupaten Bangka Barat',
            'BPS Kabupaten Bangka Selatan',
            'BPS Kabupaten Bangka Tengah',
            'BPS Kabupaten Belitung',
            'BPS Kabupaten Belitung Timur',
            'BPS Kota Pangkal Pinang',
            'BPS Provinsi Kep. Bangka Belitung',
        ];
    }
}
