<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{


    public function index(Request $request)
    {
        $query = Equipment::query();

        if ($request->filled('category')) {
            $categoryCode = (string) $request->get('category');
            $categoryId = Category::where('cate_id', $categoryCode)->value('id');
            if ($categoryId) {
                $query->where('categories_id', $categoryId);
            }
        }

        // Handle availability filter (based on quantity, not individual status)
        $availabilityFilter = null;
        if ($request->filled('availability')) {
            $availability = (string) $request->get('availability');
            $allowed = ['available', 'unavailable', 'maintenance'];
            if (in_array($availability, $allowed, true)) {
                if ($availability === 'maintenance') {
                    $query->where('status', 'maintenance');
                } else {
                    // Store availability filter to apply after grouping
                    $availabilityFilter = $availability;
                }
            }
        }

        // Handle name filter
        if ($request->filled('name')) {
            $name = (string) $request->get('name');
            $query->where('name', 'like', "%{$name}%");
        }

        $page = $request->get('page', 1);
        $queryString = $request->getQueryString();
        $equipmentsCacheKey = "equipments:page:{$page}:{$queryString}";

        $equipments = Cache::remember($equipmentsCacheKey, now()->addMinutes(5), function () use ($query, $availabilityFilter) {
            // Group by equipment name and get only one representative item per type
            $groupedEquipments = $query->get()->groupBy('name')->map(function ($equipmentGroup) {
                // Get the first available item, or first item if none available
                $availableItem = $equipmentGroup->where('status', 'available')->first();
                return $availableItem ?: $equipmentGroup->first();
            })->values();

            // Apply availability filter after grouping (based on quantity)
            if ($availabilityFilter && $availabilityFilter !== 'maintenance') {
                $groupedEquipments = $groupedEquipments->filter(function ($equipment) use ($availabilityFilter) {
                    $equipmentName = $equipment->name;
                    $availableCount = Equipment::where('name', $equipmentName)->where('status', 'available')->count();
                    
                    if ($availabilityFilter === 'available') {
                        return $availableCount > 0;
                    } elseif ($availabilityFilter === 'unavailable') {
                        return $availableCount === 0;
                    }
                    
                    return true;
                })->values();
            }

            // Convert to paginated collection manually
            $perPage = 15;
            $currentPage = request()->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $items = $groupedEquipments->slice($offset, $perPage)->values();

            // Create a LengthAwarePaginator instance
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $groupedEquipments->count(),
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
        });

        $categories = Cache::remember('categories:all', now()->addMinutes(10), function () {
            return Category::all();
        });

        return view('home', [
            'equipments' => $equipments,
            'categories' => $categories,
        ]);
    }
}
