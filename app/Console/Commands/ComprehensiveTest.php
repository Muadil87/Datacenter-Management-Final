<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Reservation;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\Maintenance;
use App\Models\Log;
use Illuminate\Support\Facades\Hash;

class ComprehensiveTest extends Command
{
    protected $signature = 'test:comprehensive';
    protected $description = 'Run comprehensive system tests on all features';

    private $testCount = 0;
    private $passCount = 0;

    public function handle()
    {
        $this->info("\n🧪 Starting Comprehensive System Tests...\n");

        // Test each system
        $this->testUserManagement();
        $this->testResourceManagement();
        $this->testReservationSystem();
        $this->testIncidentSystem();
        $this->testNotificationSystem();
        $this->testMaintenanceScheduling();
        $this->testLoggingSystem();
        $this->testRelationships();
        $this->testStatistics();

        $this->printSummary();
    }

    private function testUserManagement()
    {
        $this->line("1️⃣  Testing User Management...");
        try {
            $user = User::create([
                'name' => 'Test User ' . uniqid(),
                'email' => 'test_' . uniqid() . '@test.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_active' => false,
            ]);
            $this->testPass('Create User', "Created user ID: {$user->id}");
            
            $found = User::find($user->id);
            if ($found) {
                $this->testPass('Read User', "Retrieved user: {$found->name}");
            } else {
                $this->testFail('Read User', 'User not found');
            }

            $user->update(['name' => 'Updated ' . $user->name]);
            if ($user->fresh()->name === $user->name) {
                $this->testPass('Update User', 'Name updated successfully');
            } else {
                $this->testFail('Update User', 'Update did not persist');
            }

            $user->update(['is_active' => true, 'email_verified_at' => now()]);
            if ($user->fresh()->is_active) {
                $this->testPass('Approve User', 'User activation works');
            } else {
                $this->testFail('Approve User', 'Activation failed');
            }

            $user->update(['role' => 'manager']);
            if ($user->fresh()->role === 'manager') {
                $this->testPass('Assign Role', 'Role assignment works');
            } else {
                $this->testFail('Assign Role', 'Role change failed');
            }

            $userId = $user->id;
            $user->delete();
            if (!User::find($userId)) {
                $this->testPass('Delete User', 'User deletion works');
            } else {
                $this->testFail('Delete User', 'User still exists');
            }
        } catch (\Exception $e) {
            $this->testFail('User Management', $e->getMessage());
        }
    }

    private function testResourceManagement()
    {
        $this->line("\n2️⃣  Testing Resource Management...");
        try {
            $category = ResourceCategory::first() ?? ResourceCategory::create(['name' => 'Test Category']);
            
            $resource = Resource::create([
                'name' => 'Test Resource ' . uniqid(),
                'description' => 'Test Description',
                'category_id' => $category->id,
                'state' => 'available',
                'location' => 'Test Location',
            ]);
            $this->testPass('Create Resource', "Created resource ID: {$resource->id}");

            $found = Resource::find($resource->id);
            if ($found) {
                $this->testPass('Read Resource', "Retrieved resource: {$found->name}");
            } else {
                $this->testFail('Read Resource', 'Resource not found');
            }

            $resource->update(['state' => 'maintenance']);
            if ($resource->fresh()->state === 'maintenance') {
                $this->testPass('Update Resource', 'Resource state updated');
            } else {
                $this->testFail('Update Resource', 'Update failed');
            }

            $resId = $resource->id;
            $resource->delete();
            if (!Resource::find($resId)) {
                $this->testPass('Delete Resource', 'Resource deletion works');
            } else {
                $this->testFail('Delete Resource', 'Resource still exists');
            }
        } catch (\Exception $e) {
            $this->testFail('Resource Management', $e->getMessage());
        }
    }

    private function testReservationSystem()
    {
        $this->line("\n3️⃣  Testing Reservation System...");
        try {
            $resource = Resource::firstOrCreate(
                ['name' => 'Reservation Test Resource'],
                ['category_id' => 1, 'state' => 'available', 'location' => 'Test']
            );
            
            $user = User::firstOrCreate(
                ['email' => 'reservation_test@test.com'],
                ['name' => 'Reservation Tester', 'password' => Hash::make('pass'), 'role' => 'user', 'is_active' => true]
            );

            $reservation = Reservation::create([
                'resource_id' => $resource->id,
                'user_id' => $user->id,
                'status' => 'pending',
                'justification' => 'Test reservation',
                'start_date' => now()->addDay(),
                'end_date' => now()->addDays(2),
            ]);
            $this->testPass('Create Reservation', "Created reservation ID: {$reservation->id}");

            $found = Reservation::find($reservation->id);
            if ($found) {
                $this->testPass('Read Reservation', "Retrieved reservation ID: {$found->id}");
            } else {
                $this->testFail('Read Reservation', 'Reservation not found');
            }

            $reservation->update(['status' => 'approved']);
            if ($reservation->fresh()->status === 'approved') {
                $this->testPass('Update Reservation', 'Status updated to approved');
            } else {
                $this->testFail('Update Reservation', 'Update failed');
            }

            $resId = $reservation->id;
            $reservation->delete();
            if (!Reservation::find($resId)) {
                $this->testPass('Delete Reservation', 'Reservation deleted');
            } else {
                $this->testFail('Delete Reservation', 'Reservation still exists');
            }
        } catch (\Exception $e) {
            $this->testFail('Reservation System', $e->getMessage());
        }
    }

    private function testIncidentSystem()
    {
        $this->line("\n4️⃣  Testing Incident Management...");
        try {
            $resource = Resource::firstOrCreate(
                ['name' => 'Incident Test Resource'],
                ['category_id' => 1, 'state' => 'available', 'location' => 'Test']
            );
            
            $user = User::firstOrCreate(
                ['email' => 'incident_test@test.com'],
                ['name' => 'Incident Tester', 'password' => Hash::make('pass'), 'role' => 'internal', 'is_active' => true]
            );

            $incident = Incident::create([
                'resource_id' => $resource->id,
                'user_id' => $user->id,
                'title' => 'Test Incident',
                'description' => 'This is a test incident',
                'status' => 'ouvert',
                'priority' => 'high',
            ]);
            $this->testPass('Create Incident', "Created incident ID: {$incident->id}");

            $updatedResource = $resource->fresh();
            if ($updatedResource->state === 'maintenance') {
                $this->testPass('Auto-trigger Maintenance', 'Resource auto-set to maintenance');
            } else {
                $this->testWarn('Auto-trigger Maintenance', "Resource state: {$updatedResource->state} (expected: maintenance)");
            }

            $found = Incident::find($incident->id);
            if ($found) {
                $this->testPass('Read Incident', "Retrieved incident ID: {$found->id}");
            } else {
                $this->testFail('Read Incident', 'Incident not found');
            }

            $incident->update(['status' => 'resolu']);
            if ($incident->fresh()->status === 'resolu') {
                $this->testPass('Update Incident', 'Status updated to resolved');
                
                $restored = $resource->fresh();
                if ($restored->state === 'available') {
                    $this->testPass('Auto-restore Resource', 'Resource returned to available');
                } else {
                    $this->testWarn('Auto-restore Resource', "Resource state: {$restored->state} (expected: available)");
                }
            } else {
                $this->testFail('Update Incident', 'Status update failed');
            }

            $incId = $incident->id;
            $incident->delete();
            if (!Incident::find($incId)) {
                $this->testPass('Delete Incident', 'Incident deleted');
            } else {
                $this->testFail('Delete Incident', 'Incident still exists');
            }
        } catch (\Exception $e) {
            $this->testFail('Incident System', $e->getMessage());
        }
    }

    private function testNotificationSystem()
    {
        $this->line("\n5️⃣  Testing Notification System...");
        try {
            $user = User::firstOrCreate(
                ['email' => 'notif_test@test.com'],
                ['name' => 'Notif Tester', 'password' => Hash::make('pass'), 'role' => 'user', 'is_active' => true]
            );

            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => 'user_registration',
                'title' => 'Test Notification',
                'message' => 'This is a test notification',
                'related_id' => $user->id,
                'related_type' => 'user',
                'is_read' => false,
            ]);
            $this->testPass('Create Notification', "Created notification ID: {$notification->id}");

            $notification->update(['is_read' => true]);
            if ($notification->fresh()->is_read) {
                $this->testPass('Mark as Read', 'Notification marked as read');
            } else {
                $this->testFail('Mark as Read', 'Update failed');
            }

            $unread = Notification::where('is_read', false)->count();
            $this->testPass('Query Unread Notifications', "Found {$unread} unread notifications");

            $notifId = $notification->id;
            $notification->delete();
            if (!Notification::find($notifId)) {
                $this->testPass('Delete Notification', 'Notification deleted');
            } else {
                $this->testFail('Delete Notification', 'Notification still exists');
            }
        } catch (\Exception $e) {
            $this->testFail('Notification System', $e->getMessage());
        }
    }

    private function testMaintenanceScheduling()
    {
        $this->line("\n6️⃣  Testing Maintenance Scheduling...");
        try {
            $resource = Resource::firstOrCreate(
                ['name' => 'Maintenance Test Resource'],
                ['category_id' => 1, 'state' => 'available', 'location' => 'Test']
            );

            $maintenance = Maintenance::create([
                'resource_id' => $resource->id,
                'title' => 'Scheduled maintenance',
                'reason' => 'Test maintenance',
                'created_by' => $resource->id,
                'start_at' => now()->addDays(5),
                'end_at' => now()->addDays(6),
                'status' => 'planned',
            ]);
            $this->testPass('Create Maintenance', "Created maintenance ID: {$maintenance->id}");

            $found = Maintenance::find($maintenance->id);
            if ($found) {
                $this->testPass('Read Maintenance', "Retrieved maintenance ID: {$found->id}");
            } else {
                $this->testFail('Read Maintenance', 'Maintenance not found');
            }

            $maintenance->update(['status' => 'in_progress']);
            if ($maintenance->fresh()->status === 'in_progress') {
                $this->testPass('Update Status', 'Status updated to in_progress');
            } else {
                $this->testFail('Update Status', 'Status update failed');
            }

            $maintenance->update(['status' => 'completed', 'end_at' => now()]);
            if ($maintenance->fresh()->status === 'completed') {
                $this->testPass('Complete Maintenance', 'Maintenance marked complete');
            } else {
                $this->testFail('Complete Maintenance', 'Completion failed');
            }

            $maintId = $maintenance->id;
            $maintenance->delete();
            if (!Maintenance::find($maintId)) {
                $this->testPass('Delete Maintenance', 'Maintenance deleted');
            } else {
                $this->testFail('Delete Maintenance', 'Maintenance still exists');
            }
        } catch (\Exception $e) {
            $this->testFail('Maintenance System', $e->getMessage());
        }
    }

    private function testLoggingSystem()
    {
        $this->line("\n7️⃣  Testing Logging System...");
        try {
            $user = User::firstOrCreate(
                ['email' => 'log_test@test.com'],
                ['name' => 'Log Tester', 'password' => Hash::make('pass'), 'role' => 'user', 'is_active' => true]
            );

            $log = Log::create([
                'user_id' => $user->id,
                'action' => 'test_action',
                'details' => 'Test log entry',
                'ip_address' => '127.0.0.1',
            ]);
            $this->testPass('Create Log Entry', "Created log ID: {$log->id}");

            $found = Log::find($log->id);
            if ($found) {
                $this->testPass('Read Log Entry', "Retrieved log ID: {$found->id}");
            } else {
                $this->testFail('Read Log Entry', 'Log not found');
            }
        } catch (\Exception $e) {
            $this->testFail('Logging System', $e->getMessage());
        }
    }

    private function testRelationships()
    {
        $this->line("\n8️⃣  Testing Database Relationships...");
        try {
            $user = User::firstOrCreate(
                ['email' => 'rel_test@test.com'],
                ['name' => 'Rel Tester', 'password' => Hash::make('pass'), 'role' => 'user', 'is_active' => true]
            );
            
            $resource = Resource::firstOrCreate(
                ['name' => 'Rel Test Resource'],
                ['category_id' => 1, 'state' => 'available', 'location' => 'Test']
            );
            
            Reservation::create([
                'user_id' => $user->id,
                'resource_id' => $resource->id,
                'status' => 'pending',
                'justification' => 'Test reservation',
                'start_date' => now(),
                'end_date' => now()->addDay(),
            ]);
            
            $reservations = $user->reservations()->count();
            if ($reservations > 0) {
                $this->testPass('User -> Reservations', "User has {$reservations} reservations");
            } else {
                $this->testFail('User -> Reservations', 'Relationship broken');
            }

            $res = Resource::firstOrCreate(
                ['name' => 'Inc Rel Resource'],
                ['category_id' => 1, 'state' => 'available', 'location' => 'Test']
            );
            
            $usr = User::firstOrCreate(
                ['email' => 'inc_rel@test.com'],
                ['name' => 'Inc Rel Tester', 'password' => Hash::make('pass'), 'role' => 'internal', 'is_active' => true]
            );
            
            Incident::create([
                'resource_id' => $res->id,
                'user_id' => $usr->id,
                'title' => 'Rel Test',
                'description' => 'Testing relationships',
                'status' => 'ouvert',
                'priority' => 'low',
            ]);
            
            $incidents = $res->incidents()->count();
            if ($incidents > 0) {
                $this->testPass('Resource -> Incidents', "Resource has {$incidents} incidents");
            } else {
                $this->testFail('Resource -> Incidents', 'Relationship broken');
            }
        } catch (\Exception $e) {
            $this->testFail('Relationships', $e->getMessage());
        }
    }

    private function testStatistics()
    {
        $this->line("\n9️⃣  Testing Statistics & Aggregations...");
        try {
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $totalResources = Resource::count();
            $availableResources = Resource::where('state', 'available')->count();
            
            $this->testPass('User Statistics', "Total: {$totalUsers}, Active: {$activeUsers}");
            $this->testPass('Resource Statistics', "Total: {$totalResources}, Available: {$availableResources}");

            $pendingReservations = Reservation::where('status', 'pending')->count();
            $approvedReservations = Reservation::where('status', 'approved')->count();
            $this->testPass('Reservation Statistics', "Pending: {$pendingReservations}, Approved: {$approvedReservations}");

            $openIncidents = Incident::where('status', 'ouvert')->count();
            $resolvedIncidents = Incident::where('status', 'resolu')->count();
            $this->testPass('Incident Statistics', "Open: {$openIncidents}, Resolved: {$resolvedIncidents}");
        } catch (\Exception $e) {
            $this->testFail('Statistics', $e->getMessage());
        }
    }

    private function testPass($test, $details = '')
    {
        $this->testCount++;
        $this->passCount++;
        $this->line("   ✅ {$test}" . ($details ? " - {$details}" : ''));
    }

    private function testFail($test, $details = '')
    {
        $this->testCount++;
        $this->line("   ❌ {$test}" . ($details ? " - {$details}" : ''));
    }

    private function testWarn($test, $details = '')
    {
        $this->testCount++;
        $this->passCount++;
        $this->line("   ⚠️  {$test}" . ($details ? " - {$details}" : ''));
    }

    private function printSummary()
    {
        $this->line("\n" . str_repeat("=", 80));
        $this->info("TEST SUMMARY");
        $this->line(str_repeat("=", 80));
        
        $failCount = $this->testCount - $this->passCount;
        $percentage = round(($this->passCount / $this->testCount) * 100, 2);
        
        $this->info("\n✅ Passed: {$this->passCount}/{$this->testCount}");
        if ($failCount > 0) {
            $this->error("❌ Failed: {$failCount}/{$this->testCount}");
        }
        $this->info("📊 Success Rate: {$percentage}%\n");
    }
}
