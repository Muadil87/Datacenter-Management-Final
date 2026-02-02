<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;
use App\Models\ResourceCategory;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        // Créer d'abord les catégories
        $serverCat = ResourceCategory::firstOrCreate(['name' => 'Serveur'], ['description' => 'Serveurs physiques']);
        $vmCat = ResourceCategory::firstOrCreate(['name' => 'Machine Virtuelle'], ['description' => 'Machines virtuelles']);
        $storageCat = ResourceCategory::firstOrCreate(['name' => 'Stockage'], ['description' => 'Systèmes de stockage']);
        $networkCat = ResourceCategory::firstOrCreate(['name' => 'Réseau'], ['description' => 'Équipements réseau']);

        // Ajouter les ressources
        Resource::create([
            'name' => 'Serveur Dell R740',
            'category_id' => $serverCat->id,
            'location' => 'Baie A',
            'state' => 'available',
            'description' => 'CPU: 2x Intel Xeon, RAM: 128GB, Stockage: 2TB SSD',
            'cpu_cores' => 32,
            'ram_gb' => 128,
            'storage_gb' => 2048
        ]);

        Resource::create([
            'name' => 'VM-Ubuntu-01',
            'category_id' => $vmCat->id,
            'location' => 'Cluster 1',
            'state' => 'available',
            'description' => 'OS: Ubuntu 22.04, vCPU: 4, RAM: 16GB',
            'cpu_cores' => 4,
            'ram_gb' => 16,
            'storage_gb' => 500
        ]);

        Resource::create([
            'name' => 'Baie de Stockage NetApp',
            'category_id' => $storageCat->id,
            'location' => 'Salle Stockage',
            'state' => 'available',
            'description' => 'Capacité: 50TB, Type: NAS',
            'cpu_cores' => 16,
            'ram_gb' => 64,
            'storage_gb' => 50000
        ]);

        Resource::create([
            'name' => 'Switch Cisco Catalyst',
            'category_id' => $networkCat->id,
            'location' => 'Rack Principal',
            'state' => 'available',
            'description' => 'Ports: 48x 10G + 4x 40G, VLAN support',
            'cpu_cores' => 8,
            'ram_gb' => 32,
            'storage_gb' => 256
        ]);

        Resource::create([
            'name' => 'Serveur HP ProLiant',
            'category_id' => $serverCat->id,
            'location' => 'Baie B',
            'state' => 'available',
            'description' => 'CPU: 2x Xeon Platinum, RAM: 256GB',
            'cpu_cores' => 48,
            'ram_gb' => 256,
            'storage_gb' => 4096
        ]);

        Resource::create([
            'name' => 'VM-Windows-02',
            'category_id' => $vmCat->id,
            'location' => 'Cluster 2',
            'state' => 'maintenance',
            'description' => 'OS: Windows Server 2022, vCPU: 8, RAM: 32GB',
            'cpu_cores' => 8,
            'ram_gb' => 32,
            'storage_gb' => 1000
        ]);
    }
}