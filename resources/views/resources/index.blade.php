@extends('layouts.app')

@section('content')
    <style>
        /* Force Dark Theme for this page */
        /* Force Light Theme for this page text */
        body {
            /* background-color: #fff !important; Inherit from layout */
            color: #1a2332 !important;
            /* Black text */
        }

        header h1 {
            color: #ffffff !important;
        }

        header p {
            color: #64748b !important;
        }
    </style>

    <header>
        <h1>Inventaire des Ressources</h1>
        <p>Accès direct au matériel et aux actifs virtuels du DataCenter.</p>
    </header>

    @guest
        <div class="guest-message">
            <h3>Vous n'avez pas de compte ?</h3>
            <p>Pour réserver des ressources, vous devez créer un compte et être approuvé par un administrateur.</p>
            <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Créer un compte</a>
            <a href="{{ route('login') }}" class="login"><i class="fas fa-lock"></i> Se connecter</a>
        </div>
    @endguest

    @auth
        @if(auth()->user()->canManage())
            <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
                <button class="btn-create" onclick="openAddPanel()">
                    + Créer une ressource
                </button>
            </div>
        @endif
    @endauth

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
        <div class="range-group">
            <div class="range-item">
                <label>Cœurs CPU Min <span id="cpu-val">0</span></label>
                <input type="range" id="cpu-filter" min="0" max="100" value="0">
            </div>
            <div class="range-item">
                <label>RAM Min (Go) <span id="ram-val">0</span></label>
                <input type="range" id="ram-filter" min="0" max="212" value="0">
            </div>
            <div class="range-item">
                <label>Stockage Min (To) <span id="storage-val">0</span></label>
                <input type="range" id="storage-filter" min="0" max="1000" value="0">
            </div>
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
                            <input type="number" name="bandwidth_mbps" id="editBandwidth" min="0" placeholder="Bande passante (Mbps)">
                        </div>

                        <div id="field-storagetype" style="display:none;">
                            <input type="text" name="storage_type" id="editStorageType" placeholder="Type de stockage (ex: SSD, HDD)">
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
                            <option value="available">Disponible</option>
                            <option value="maintenance">Maintenance</option>
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

        // ====== GLOBAL VARIABLES ======
        let currentEditResourceId = null;

        // ====== MODAL & PANEL FUNCTIONS ======
        function openAddPanel() {
            const panel = document.getElementById('addPanel');
            if (panel) {
                panel.style.display = 'flex';
                console.log('Add panel opened');
            }
        }

        function closeAddPanel() {
            const panel = document.getElementById('addPanel');
            if (panel) {
                panel.style.display = 'none';
            }
        }

        function openEditModal(id, name, cpu, ram, storage, state) {
            console.log('Opening edit modal for resource:', id);
            currentEditResourceId = id;
            const modal = document.getElementById('editModal');
            if (!modal) {
                console.error('Edit modal not found');
                alert('Erreur : Modal introuvable');
                return;
            }
            modal.style.display = 'flex';
            document.getElementById('editName').value = name || '';
            document.getElementById('editCpu').value = cpu || '';
            document.getElementById('editRam').value = ram || '';
            document.getElementById('editStorage').value = storage || '';
            document.getElementById('editState').value = state || 'available';
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            if (modal) {
                modal.style.display = 'none';
            }
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
                if (editState) editState.value = state || 'available';

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

        function closeAllModals() {
            closeEditModal();
            closeAddPanel();
        }

        // ====== SUBMIT FORMS ======
        // ====== NATIVE FORM SUBMISSIONS ======
        // We use standard HTML forms for robustness.

        function switchCreateFields() {
            const catId = document.getElementById('createCategory').value;

            const computeDiv = document.getElementById('create-compute');
            const storageDiv = document.getElementById('create-storage');
            const networkDiv = document.getElementById('create-network');

            // Reset: Hide all and disable all inputs inside them
            [computeDiv, storageDiv, networkDiv].forEach(div => {
                div.style.display = 'none';
                div.querySelectorAll('input').forEach(input => input.disabled = true);
            });

            if (catId == '1' || catId == '2') { // Server/VM
                computeDiv.style.display = 'block';
                computeDiv.querySelectorAll('input').forEach(input => input.disabled = false);
            } else if (catId == '3') { // Storage
                storageDiv.style.display = 'block';
                storageDiv.querySelectorAll('input').forEach(input => input.disabled = false);
            } else if (catId == '4') { // Network
                networkDiv.style.display = 'block';
                networkDiv.querySelectorAll('input').forEach(input => input.disabled = false);
            }
        }

        // ====== AJAX FILTER FUNCTION ======
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('search-input');
            const category = document.getElementById('category-filter');
            const cpuFilter = document.getElementById('cpu-filter');
            const ramFilter = document.getElementById('ram-filter');
            const storageFilter = document.getElementById('storage-filter');

            // Update value displays
            if (cpuFilter) cpuFilter.addEventListener('input', function () {
                document.getElementById('cpu-val').textContent = this.value;
            });
            if (ramFilter) ramFilter.addEventListener('input', function () {
                document.getElementById('ram-val').textContent = this.value;
            });
            if (storageFilter) storageFilter.addEventListener('input', function () {
                document.getElementById('storage-val').textContent = this.value;
            });

            function fetchResources() {
                const params = new URLSearchParams({
                    search: search.value,
                    category: category.value,
                    min_cpu: cpuFilter.value,
                    min_ram: ramFilter.value,
                    min_storage: storageFilter.value
                });
                fetch('{{ route("resources.filter") }}?' + params.toString())
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('inventory-grid').innerHTML = html;
                    })
                    .catch(err => console.error('Filter error:', err));
            }

            if (search) search.addEventListener('input', fetchResources);
            if (category) category.addEventListener('change', fetchResources);
            if (cpuFilter) cpuFilter.addEventListener('input', fetchResources);
            if (ramFilter) ramFilter.addEventListener('input', fetchResources);
            if (storageFilter) storageFilter.addEventListener('input', fetchResources);
        });
    </script>
@endpush