<?php

namespace App\Http\Controllers;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /*
      Affiche la liste des ressources pour la page publique (accueil)
      Accessible à tous (connectés et non connectés)
     */
    public function publicIndex()
    {
        $resources = Resource::all();
        return view('resources.index', compact('resources'));
    }
  public function home() {
    $resources = Resource::all(); 
    return view('welcome', compact('resources')); 
}
    /*
      Affiche le tableau de bord des ressources pour l'Admin
      Accessible uniquement aux administrateurs
     */
    public function adminIndex()
    {
        $resources = Resource::all();
        return view('admin.resources', compact('resources'));
    }

    public function index(Request $request)
    {
        // Page d'accueil - affiche toutes les ressources SAUF maintenance
        $resources = Resource::with(['category', 'reservations'])->where('state', '!=', 'maintenance')->get();
        return view('resources.index', compact('resources'));
    }

    public function indexFilter(Request $request)
    {
        $query = \App\Models\Resource::with('category')
                    ->where('state', '!=', 'maintenance');      

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category != 'ALL') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->filled('min_cpu')) {
            $query->where('cpu_cores', '>=', $request->min_cpu);
        }

        if ($request->filled('min_ram')) {
            $query->where('ram_gb', '>=', $request->min_ram);
        }

        if ($request->filled('min_storage')) {
            $query->where('storage_gb', '>=', $request->min_storage);
        }

        $resources = $query->get();

        return view('resources.index', compact('resources'));
    }

    public function ajaxFilter(Request $request)
    {
        $query = Resource::with(['category', 'reservations'])
            ->where('state', '!=', 'maintenance');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->category && $request->category !== 'ALL') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->min_cpu) {
            $query->where('cpu_cores', '>=', $request->min_cpu);
        }

        if ($request->min_ram > 0) {
            $query->whereNotNull('ram_gb')
                  ->where('ram_gb', '>=', $request->min_ram);
        }

        if ($request->min_storage > 0) {
            $query->whereNotNull('storage_gb')
                  ->where('storage_gb', '>=', $request->min_storage);
        }

        $resources = $query->get();
        return view('resources.partials.grid', compact('resources'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->canManage()) {
            abort(403, 'Unauthorized action.');
        }

        $resource = Resource::find($id);
        if (!$resource) {
            abort(404, 'Resource not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|in:available,maintenance',
            'cpu_cores' => 'nullable|integer|min:0',
            'ram_gb' => 'nullable|integer|min:0',
            'storage_gb' => 'nullable|integer|min:0',
            'storage_type' => 'nullable|string|max:50',
            'bandwidth_mbps' => 'nullable|integer|min:0',
        ]);

        $optionalFields = ['cpu_cores', 'ram_gb', 'storage_gb', 'storage_type', 'bandwidth_mbps'];
        $updateData = [
            'name'  => $validated['name'],
            'state' => $validated['state'],
        ];

        foreach ($optionalFields as $field) {
            if ($request->filled($field)) {
                $updateData[$field] = $validated[$field];
            }
        }

        try {
            Resource::where('id', $id)->update($updateData);
            return redirect()->back()->with('success', 'Resource updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Update error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    public function destroy(Resource $resource)
    {
        // Only tech and admin can delete
        if (!auth()->check() || !auth()->user()->canManage()) {
            abort(403, 'Unauthorized action.');
        }

        $resource->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Resource deleted']);
        }

        return redirect()->back();
    }

    public function store(Request $request)
    {
        // Only admin and manager can create resources
        if (!auth()->check() || !auth()->user()->canManage()) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:resource_categories,id',
            'state' => 'required|in:available,maintenance',
        ];

        $categoryId = $request->category_id;

        
        if ($categoryId == 1 || $categoryId == 2) { // Server/VM
            $rules['cpu_cores'] = 'required|integer|min:1';
            $rules['ram_gb'] = 'required|integer|min:1';
            $rules['storage_gb'] = 'required|integer|min:1';
        } elseif ($categoryId == 3) { // Storage
            $rules['storage_type'] = 'required|string|max:50';
            $rules['storage_gb'] = 'required|integer|min:1';
        } elseif ($categoryId == 4) { // Network
            $rules['bandwidth_mbps'] = 'required|integer|min:1';
        }

        try {
            $data = $request->validate($rules);
            $resource = Resource::create($data);
            
            return redirect()->back()->with('success', 'Resource created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Resource creation error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Creation failed: ' . $e->getMessage()])->withInput();
        }
    }
}