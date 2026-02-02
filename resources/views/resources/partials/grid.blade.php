@foreach ($resources as $resource)
    @php
        $classMap = [1 => 'server', 2 => 'vm', 3 => 'storage', 4 => 'network'];
        $colors = [1 => '#60a5fa', 2 => '#ff6b08', 3 => '#8d01ff', 4 => '#0bc6f5'];
        $color = $colors[$resource->category_id] ?? '#94a3b8';

        // Check for active reservations
        $activeReservation = $resource->reservations()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();
        $isOccupied = $activeReservation !== null;
    @endphp

    <div class="res-card {{ $classMap[$resource->category_id] ?? '' }}">

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="status {{ $resource->state }}">
                {{ $isOccupied ? 'reserved' : $resource->state }}
            </span>
            <small style="color: {{ $color }}; font-weight: 800;">
                {{ $resource->category->name ?? '-' }}
            </small>
        </div>

        <h3>{{ $resource->name }}</h3>

        <div class="specs-grid">
            @if($resource->cpu_cores)
                <div class="spec-box"><small>CPU</small><span>{{ $resource->cpu_cores }}</span></div>
            @endif
            @if($resource->ram_gb)
                <div class="spec-box"><small>RAM</small><span>{{ $resource->ram_gb }} GB</span></div>
            @endif
            @if($resource->storage_gb)
                <div class="spec-box" style="grid-column: span 2;"><small>Capacity</small><span>{{ $resource->storage_gb }}
                        GB</span></div>
            @endif
            @if($resource->bandwidth_mbps)
                <div class="spec-box" style="grid-column: span 2;"><small>Bandwidth</small><span>{{ $resource->bandwidth_mbps }}
                        Mbps</span></div>
            @endif
        </div>

        {{-- Main Action Button - Based on User Role --}}
        @auth
            {{-- For regular users: only show Reserve button --}}
            @if(auth()->user()->canReserve() && !auth()->user()->canManage())
                @if($resource->state !== 'maintenance')
                    <button class="btn-reserve" onclick="location.href='{{ route('reservations.create', $resource->id) }}'">
                        {{ $isOccupied ? 'Request to Share' : 'Reserve Resource' }}
                    </button>
                @else
                    <button class="btn-reserve" style="background: #334155; cursor: not-allowed;" disabled>
                        Under Maintenance
                    </button>
                @endif
            @endif

            {{-- For tech/admin: show full functionality --}}
            @if(auth()->user()->canManage())
                @if($resource->state === 'maintenance')
                    <form action="{{ route('maintenances.resolve', $resource->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-reserve" style="background-color: #22c55e;">
                            <i class="fas fa-check"></i> Mark as Repaired
                        </button>
                    </form>
                @elseif($resource->state !== 'available')
                    <button class="btn-reserve" style="background: #334155; cursor: not-allowed;" disabled>
                        Occupied
                    </button>
                @endif
            @endif
        @endauth

        {{-- For guests: no action button, just info --}}
        @guest
            @if($resource->state === 'available')
                <div
                    style="text-align: center; padding: 12px; background: rgba(59, 130, 246, 0.1); border-radius: 8px; color: #3b82f6; font-size: 0.85rem;">
                    <i class="fas fa-info-circle"></i> Login to reserve resources
                </div>
            @elseif($resource->state !== 'maintenance')
                <button class="btn-reserve" style="background: #334155; cursor: not-allowed;" disabled>
                    Occupied
                </button>
            @endif
        @endguest

        {{-- Edit/Delete buttons - Only for Tech/Admin --}}
        @auth
            @if(auth()->user()->canManage())
                <div class="res-actions">
                    <button type="button" class="btn-edit" data-id="{{ $resource->id }}" data-name="{{ $resource->name }}"
                        data-category-id="{{ $resource->category_id }}" data-cpu="{{ $resource->cpu_cores ?? 0 }}"
                        data-ram="{{ $resource->ram_gb ?? 0 }}" data-storage="{{ $resource->storage_gb ?? 0 }}"
                        data-bandwidth="{{ $resource->bandwidth_mbps ?? 0 }}" data-storagetype="{{ $resource->storage_type ?? '' }}"
                        data-state="{{ $resource->state }}" onclick="handleEditClick(this)">
                        Edit
                    </button>

                    <form action="{{ route('resources.destroy', $resource->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete"
                            onclick="return confirm('Are you sure you want to delete this resource?')">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        @endauth

    </div>
@endforeach