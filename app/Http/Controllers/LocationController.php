<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Location as LocationResource;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2019-01-30
 */
class LocationController extends Controller
{
    public function index()
    {
        return new JsonResponse(LocationResource::collection(Location::all()));
    }

    /**
     * Updates the given location
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string'
        ]);

        /** @var Location $location */
        $location = Location::getByExternalId($id);

        if ($location) {
            $location->name = $request->input('name');
            $location->save();

            return new JsonResponse([
                'message' => 'Location ' . $id . ' has been updated',
                'data' => new LocationResource(Location::getByExternalId($id))
            ]);
        }

        // it's a new location, create it
        $location = new Location([
            'name' => $request->input('name'),
            'externalId' => $id
        ]);
        $location->save();

        return new JsonResponse([
            'message' => 'Location ' . $id . ' was created',
            'data' => new LocationResource(Location::getByExternalId($id))
        ]);
    }
}
