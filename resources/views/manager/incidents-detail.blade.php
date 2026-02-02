@extends('layout')

@section('content')
    <div style="max-width: none; width: 100%; margin: 0; padding: 40px 0; background: #0f1419; min-height: 100vh;">
        <!-- Back Button -->
        <a href="{{ route('manager.incidents.list') }}" 
            style="display: inline-flex; align-items: center; gap: 8px; color: #06b6d4; text-decoration: none; margin-bottom: 30px; font-weight: 600; transition: all 0.3s;">
            ← {{ __('Back to Incidents') }}
        </a>

        <!-- Success Message -->
        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 16px 20px; border-radius: 8px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 500;"><i class="fas fa-check-circle"></i> {{ session('success') }}</span>
                <button onclick="this.parentElement.style.display='none';" style="background: none; border: none; color: #10b981; cursor: pointer; font-size: 20px;">×</button>
            </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 16px 20px; border-radius: 8px; margin-bottom: 30px;">
                <strong>Error:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div style="margin-bottom: 40px;">
            <h1 style="font-size: 2.5em; font-weight: 700; color: #ffffff; margin: 0 0 10px 0;">{{ $incident->title }}</h1>
            <p style="color: #a0aec0; font-size: 16px; margin: 0;">Incident Details & Management</p>
        </div>

        <!-- Main Content Container -->
        <div style="background: rgba(20, 30, 50, 0.4); backdrop-filter: blur(20px); padding: 40px; border-radius: 12px; border: 1px solid rgba(6, 182, 212, 0.1);">
            
            <!-- Detail Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
                <!-- Resource -->
                <div style="background: rgba(6, 182, 212, 0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(6, 182, 212, 0.15);">
                    <label style="color: #06b6d4; font-weight: 600; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 8px;">Resource</label>
                    <p style="color: #ffffff; font-size: 18px; font-weight: 600; margin: 0;">{{ $incident->resource->name }}</p>
                </div>

                <!-- Reported By -->
                <div style="background: rgba(6, 182, 212, 0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(6, 182, 212, 0.15);">
                    <label style="color: #06b6d4; font-weight: 600; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 8px;">Reported By</label>
                    <p style="color: #ffffff; font-size: 16px; font-weight: 600; margin: 0;">{{ $incident->user->name }}</p>
                    <p style="color: #a0aec0; font-size: 14px; margin: 4px 0 0 0;">{{ $incident->user->email }}</p>
                </div>

                <!-- Priority -->
                <div style="background: rgba(6, 182, 212, 0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(6, 182, 212, 0.15);">
                    <label style="color: #06b6d4; font-weight: 600; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 8px;">Priority</label>
                    <div>
                        @php
                            $priorityColor = match($incident->priority) {
                                'low' => ['bg' => 'rgba(96, 165, 250, 0.2)', 'color' => '#60a5fa', 'border' => 'rgba(96, 165, 250, 0.3)'],
                                'medium' => ['bg' => 'rgba(251, 191, 36, 0.2)', 'color' => '#fbbf24', 'border' => 'rgba(251, 191, 36, 0.3)'],
                                'high' => ['bg' => 'rgba(239, 68, 68, 0.2)', 'color' => '#ef4444', 'border' => 'rgba(239, 68, 68, 0.3)'],
                                'critical' => ['bg' => 'rgba(239, 68, 68, 0.2)', 'color' => '#ef4444', 'border' => 'rgba(239, 68, 68, 0.3)'],
                                default => ['bg' => 'rgba(107, 122, 144, 0.2)', 'color' => '#a0aec0', 'border' => 'rgba(107, 122, 144, 0.3)']
                            };
                        @endphp
                        <span style="display: inline-block; padding: 8px 16px; border-radius: 6px; font-weight: 600; background: {{ $priorityColor['bg'] }}; color: {{ $priorityColor['color'] }}; border: 1px solid {{ $priorityColor['border'] }};">
                            {{ ucfirst($incident->priority) }}
                        </span>
                    </div>
                </div>

                <!-- Status -->
                <div style="background: rgba(6, 182, 212, 0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(6, 182, 212, 0.15);">
                    <label style="color: #06b6d4; font-weight: 600; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 8px;">Status</label>
                    <div>
                        @php
                            $statusColor = match($incident->status) {
                                'resolu' => ['bg' => '#d4edda', 'color' => '#155724'],
                                'en_traitement' => ['bg' => '#fff3cd', 'color' => '#856404'],
                                'ouvert' => ['bg' => '#f8d7da', 'color' => '#721c24'],
                                default => ['bg' => '#ecf0f1', 'color' => '#2c3e50']
                            };
                        @endphp
                        <span style="display: inline-block; padding: 8px 16px; border-radius: 6px; font-weight: 600; background: {{ $statusColor['bg'] }}; color: {{ $statusColor['color'] }};">
                            {{ $incident->status === 'resolu' ? 'Resolved' : ($incident->status === 'en_traitement' ? 'In Progress' : 'Open') }}
                        </span>
                    </div>
                </div>

                <!-- Date -->
                <div style="background: rgba(6, 182, 212, 0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(6, 182, 212, 0.15);">
                    <label style="color: #06b6d4; font-weight: 600; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 8px;">Reported Date</label>
                    <p style="color: #ffffff; font-size: 16px; font-weight: 600; margin: 0;">{{ $incident->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <!-- Updated Date -->
                <div style="background: rgba(6, 182, 212, 0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(6, 182, 212, 0.15);">
                    <label style="color: #06b6d4; font-weight: 600; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 8px;">Last Updated</label>
                    <p style="color: #ffffff; font-size: 16px; font-weight: 600; margin: 0;">{{ $incident->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Description Section -->
            <div style="margin-bottom: 40px;">
                <h2 style="color: #06b6d4; font-size: 1.3em; font-weight: 600; margin: 0 0 15px 0;">Description</h2>
                <div style="background: rgba(6, 182, 212, 0.08); padding: 20px; border-radius: 10px; border: 1px solid rgba(6, 182, 212, 0.2); border-left: 4px solid #06b6d4;">
                    <p style="color: #a0aec0; font-size: 16px; line-height: 1.6; margin: 0;">{{ $incident->description }}</p>
                </div>
            </div>

            <!-- Update Status Section (if not resolved) -->
            @if($incident->status !== 'resolu')
                <div style="background: rgba(6, 182, 212, 0.05); padding: 25px; border-radius: 10px; border: 1px solid rgba(6, 182, 212, 0.15);">
                    <h2 style="color: #06b6d4; font-size: 1.3em; font-weight: 600; margin: 0 0 20px 0;">Update Status</h2>
                    
                    <form method="POST" action="{{ route('manager.incidents.updateStatus', $incident->id) }}" style="display: flex; gap: 15px; align-items: flex-end;">
                        @csrf
                        @method('PATCH')

                        <div style="flex: 1;">
                            <label for="status" style="display: block; color: #ffffff; font-weight: 600; margin-bottom: 8px;">New Status</label>
                            <select name="status" id="status" style="width: 100%; padding: 12px; border: 1px solid rgba(6, 182, 212, 0.2); border-radius: 6px; background: rgba(15, 20, 25, 0.5); color: #ffffff; font-size: 16px; font-family: inherit;">
                                <option value="ouvert" {{ $incident->status === 'ouvert' ? 'selected' : '' }}>Open</option>
                                <option value="en_traitement" {{ $incident->status === 'en_traitement' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolu" {{ $incident->status === 'resolu' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        <button type="submit" style="padding: 12px 30px; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 16px;">
                            Update Status
                        </button>
                    </form>
                </div>
            @else
                <!-- Resolved Message -->
                <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; color: #155724;">
                    <p style="margin: 0; font-size: 16px; font-weight: 600;"><i class="fas fa-check-circle"></i> Incident Resolved</p>
                    <p style="margin: 8px 0 0 0; font-size: 14px;">This incident has been marked as resolved and cannot be modified further.</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        a:hover {
            color: #22d3ee !important;
        }

        select:focus {
            outline: none;
            border-color: #06b6d4 !important;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2) !important;
        }

        button:hover {
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4) !important;
            transform: translateY(-2px) !important;
        }
    </style>
@endsection
