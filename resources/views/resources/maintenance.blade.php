@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #0f1419 !important;
            color: #ffffff !important;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: #ffffff !important;
            font-size: 2em;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #a0aec0 !important;
        }

        .btn-create {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-create:hover {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        .filter-section {
            background-color: rgba(20, 30, 50, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(20px);
        }

        .search-bar {
            display: flex;
            gap: 1rem;
        }

        .search-bar input,
        .search-bar select {
            flex: 1;
            padding: 10px 12px;
            background-color: rgba(15, 20, 25, 0.8);
            color: #ffffff;
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 6px;
            font-size: 0.95em;
        }

        .search-bar input::placeholder {
            color: #6b7a90;
        }

        .search-bar input:focus,
        .search-bar select:focus {
            outline: none;
            border-color: #06b6d4;
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2);
        }

        #inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .resource-card {
            background-color: rgba(20, 30, 50, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 8px;
            padding: 1.5rem;
            backdrop-filter: blur(20px);
            transition: all 0.3s ease;
        }

        .resource-card:hover {
            border-color: #06b6d4;
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.2);
            transform: translateY(-4px);
        }

        .resource-name {
            color: #06b6d4;
            font-weight: 600;
            font-size: 1.1em;
            margin-bottom: 0.5rem;
        }

        .resource-info {
            color: #a0aec0;
            font-size: 0.9em;
            margin-bottom: 0.3rem;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 999999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
        }

        .modal-box {
            background-color: rgba(20, 30, 50, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.15);
            padding: 2rem;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .modal-box h3 {
            color: #06b6d4;
            margin-bottom: 1.5rem;
            font-size: 1.3em;
        }

        .mb-2 {
            margin-bottom: 1rem;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px 12px;
            background-color: rgba(15, 20, 25, 0.8);
            color: #ffffff;
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.95em;
            font-family: inherit;
        }

        input::placeholder {
            color: #6b7a90;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #06b6d4;
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2);
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .btn-reserve {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-reserve:hover {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
        }

        .btn-cancel {
            background-color: #6b7a90;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #a0aec0;
        }
    </style>

    <header class="page-header">
        <h1>Ressources en Maintenance</h1>
        <p>Ressources actuellement en maintenance ou en procédure de diagnostic.</p>
    </header>

    <div class="filter-section">
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Rechercher par nom...">
            <select id="category-filter">
                <option value="ALL">Toutes les catégories</option>
                <option value="Serveur">Serveurs</option>
                <option value="Machine Virtuelle">VMs</option>
                <option value="Stockage">Stockage</option>
                <option value="Réseau">Réseau</option>
            </select>
        </div>
    </div>

    <div id="inventory-grid">
        @include('resources.partials.grid', ['resources' => $resources])
    </div>

    @auth
        @if(auth()->user()->canManage())
            <div id="editModal" class="modal-overlay">
                <div class="modal-box">
                    <h3>Modifier la ressource</h3>
                    <form method="POST" id="editForm">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="name" id="editName" placeholder="Nom" required>

                        <div id="field-cpu-ram" style="display:none;">
                            <input type="number" name="cpu_cores" id="editCpu" min="0" placeholder="Cœurs CPU">
                            <input type="number" name="ram_gb" id="editRam" min="0" placeholder="RAM (Go)">
                        </div>

                        <div id="field-storage" style="display:none;">
                            <input type="number" name="storage_gb" id="editStorage" min="0" placeholder="Stockage (Go)">
                        </div>

                        <div id="field-bandwidth" style="display:none;">
                            <input type="number" name="bandwidth_mbps" id="editBandwidth" min="0"
                                placeholder="Bande passante (Mbps)">
                        </div>

                        <div id="field-storagetype" style="display:none;">
                            <input type="text" name="storage_type" id="editStorageType"
                                placeholder="Type de stockage (ex: SSD, HDD)">
                        </div>

                        <select name="state" id="editState" required>
                            <option value="available">Disponible</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <div class="modal-actions">
                            <button type="submit" class="btn-reserve">Enregistrer</button>
                            <button type="button" class="btn-cancel" onclick="closeEditModal()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>

            <form id="deleteForm" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>

            <div id="addPanel" class="modal-overlay">
                <div class="modal-box" style="width:420px;">
                    <h3>Créer une ressource</h3>
                    <form id="createForm" method="POST" action="{{ route('resources.store') }}">
                        @csrf
                        <input type="text" name="name" placeholder="Nom de la ressource" class="mb-2">
                        <select name="category_id" id="createCategory" onchange="switchCreateFields()" class="mb-2">
                            <option value="">Choisir le type</option>
                            <option value="1">Serveur</option>
                            <option value="2">Machine Virtuelle</option>
                            <option value="3">Stockage</option>
                            <option value="4">Réseau</option>
                        </select>

                        <div id="create-compute" style="display:none;">
                            <input type="number" name="cpu_cores" placeholder="Cœurs CPU">
                            <input type="number" name="ram_gb" placeholder="RAM (Go)">
                            <input type="number" name="storage_gb" placeholder="Stockage (Go)">
                        </div>

                        <div id="create-storage" style="display:none;">
                            <input type="number" name="storage_gb" placeholder="Stockage (Go)">
                            <input type="text" name="storage_type" placeholder="Type de stockage (HDD/SSD)">
                        </div>

                        <div id="create-network" style="display:none;">
                            <input type="number" name="bandwidth_mbps" placeholder="Bande passante (Mbps)">
                        </div>

                        <select name="state" required>
                            <option value="maintenance" selected>Maintenance</option>
                            <option value="available">Disponible</option>
                        </select>

                        <div class="modal-actions">
                            <button type="submit" class="btn-reserve">Créer</button>
                            <button type="button" class="btn-cancel" onclick="closeAddPanel()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endauth
@endsection

@push('scripts')
    <script>
        'use strict';
        let currentEditResourceId = null;

        function openAddPanel() {
            const panel = document.getElementById('addPanel');
            if (panel) panel.style.display = 'flex';
        }

        function closeAddPanel() {
            const panel = document.getElementById('addPanel');
            if (panel) panel.style.display = 'none';
        }

        function openEditModal(id, name, cpu, ram, storage, state) {
            currentEditResourceId = id;
            const modal = document.getElementById('editModal');
            if (modal) {
                modal.style.display = 'flex';
                document.getElementById('editName').value = name || '';
                document.getElementById('editState').value = state || 'maintenance';
                // Note: Other fields populated by handleEditClick usually
            }
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            if (modal) modal.style.display = 'none';
        }

        function handleEditClick(button) {
            try {
                console.log('====== EDIT CLICK HANDLER ======');
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const cpu = button.getAttribute('data-cpu');
                const ram = button.getAttribute('data-ram');
                const storage = button.getAttribute('data-storage');
                const state = button.getAttribute('data-state');
                const catId = button.getAttribute('data-category-id');
                const bandwidth = button.getAttribute('data-bandwidth');
                const storageType = button.getAttribute('data-storagetype');

                console.log('Resource ID:', id, 'Name:', name, 'Category:', catId);

                currentEditResourceId = id;
                const modal = document.getElementById('editModal');

                if (!modal) {
                    console.error('❌ ERROR: Modal with id "editModal" not found!');
                    alert('Erreur : Modal de modification introuvable. Veuillez actualiser la page.');
                    return;
                }

                console.log('✓ Modal found, populating fields...');

                // Populate Fields
                const editName = document.getElementById('editName');
                const editState = document.getElementById('editState');

                if (editName) editName.value = name || '';
                if (editState) editState.value = state || 'maintenance';

                // Reset visibility
                const fieldCpuRam = document.getElementById('field-cpu-ram');
                const fieldStorage = document.getElementById('field-storage');
                const fieldBandwidth = document.getElementById('field-bandwidth');
                const fieldStorageType = document.getElementById('field-storagetype');

                if (fieldCpuRam) fieldCpuRam.style.display = 'none';
                if (fieldStorage) fieldStorage.style.display = 'none';
                if (fieldBandwidth) fieldBandwidth.style.display = 'none';
                if (fieldStorageType) fieldStorageType.style.display = 'none';

                // Show appropriate fields
                if (catId == '1' || catId == '2') { // Server/VM
                    console.log('Showing CPU/RAM/Storage fields');
                    if (fieldCpuRam) fieldCpuRam.style.display = 'block';
                    if (fieldStorage) fieldStorage.style.display = 'block';
                    document.getElementById('editCpu').value = cpu || '';
                    document.getElementById('editRam').value = ram || '';
                    document.getElementById('editStorage').value = storage || '';
                } else if (catId == '3') { // Storage
                    console.log('Showing Storage/Type fields');
                    if (fieldStorage) fieldStorage.style.display = 'block';
                    if (fieldStorageType) fieldStorageType.style.display = 'block';
                    document.getElementById('editStorage').value = storage || '';
                    document.getElementById('editStorageType').value = storageType || '';
                } else if (catId == '4') { // Network
                    console.log('Showing Bandwidth fields');
                    if (fieldBandwidth) fieldBandwidth.style.display = 'block';
                    document.getElementById('editBandwidth').value = bandwidth || '';
                }

                // Set Form Action for Native Submit
                const form = document.getElementById('editForm');
                if (form) {
                    form.action = `/resources/${id}`;
                    console.log('Form action set to:', form.action);
                }

                console.log('Setting modal display to flex...');
                modal.style.display = 'flex';
                console.log('✓ Modal should now be visible');
                console.log('====== END EDIT CLICK HANDLER ======');
            } catch (error) {
                console.error('❌ ERROR in handleEditClick:', error);
                alert('Une erreur est survenue : ' + error.message);
            }
        }

        function confirmDelete(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette ressource ?')) {
                const form = document.getElementById('deleteForm');
                form.action = '/resources/' + id;
                form.submit();
            }
        }

        function switchCreateFields() {
            const catId = document.getElementById('createCategory').value;
            document.getElementById('create-compute').style.display = 'none';
            document.getElementById('create-storage').style.display = 'none';
            document.getElementById('create-network').style.display = 'none';

            if (catId == '1' || catId == '2') {
                document.getElementById('create-compute').style.display = 'block';
            } else if (catId == '3') {
                document.getElementById('create-storage').style.display = 'block';
            } else if (catId == '4') {
                document.getElementById('create-network').style.display = 'block';
            }
        }

        document.getElementById('editForm')?.addEventListener('submit', function (e) {
            if (!currentEditResourceId) {
                e.preventDefault();
                return;
            }
            // Let the form submit normally - don't prevent default
            const form = document.getElementById('editForm');
            if (form) {
                form.action = `/resources/${currentEditResourceId}`;
            }
        });

        // Ajax Filter
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('search-input');
            const category = document.getElementById('category-filter');

            function fetchResources() {
                const params = new URLSearchParams({
                    search: search.value,
                    category: category.value,
                });
                fetch('/maintenances/filter?' + params.toString())
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('inventory-grid').innerHTML = html;
                    });
            }

            if (search) search.addEventListener('input', fetchResources);
            if (category) category.addEventListener('change', fetchResources);
        });
    </script>
@endpush