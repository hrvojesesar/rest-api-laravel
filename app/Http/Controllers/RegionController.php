<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    public function getAllRegions()
    {
        $regions = Region::all();

        foreach ($regions as $region) {
            $region->RegionDescription = trim($region->RegionDescription);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Regions retrieved successfully',
            'regions' => $regions
        ]);
    }

    public function getRegionById($id)
    {
        $region = Region::where('RegionID', $id)->first();

        $region->RegionDescription = trim($region->RegionDescription);

        if ($region) {
            return response()->json([
                'status' => 'success',
                'message' => 'Region fetched successfully from the database',
                'region' => $region
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Region not found'
            ]);
        }
    }

    public function createRegion(Request $request)
    {
        // Validate the request
        $request->validate([
            'RegionDescription' => 'required|string|max:50'
        ]);

        // Create a new region
        $region = Region::create([
            'RegionDescription' => $request->RegionDescription
        ]);

        if ($region) {
            return response()->json([
                'status' => 'success',
                'message' => 'Region created successfully',
                'region' => $region
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create region'
            ]);
        }
    }

    public function updateRegion(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'RegionDescription' => 'required|string|max:50'
        ]);

        // Find the region
        $region = Region::where('RegionID', $id)->first();

        if ($region) {
            // Update the region
            $region->RegionDescription = $request->RegionDescription;
            $region->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Region updated successfully',
                'region' => $region
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Region not found'
            ]);
        }
    }

    public function deleteRegion($id)
    {
        // Find the region
        $region = Region::where('RegionID', $id)->first();

        if ($region) {
            // Delete the region
            $region->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Region deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Region not found'
            ]);
        }
    }
}
