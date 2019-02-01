<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Location as LocationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'name' => 'required|string',
            'address' => 'string',
            'postal' => 'string',
            'city' => 'string',
        ]);

        /** @var Location $location */
        $location = Location::getByExternalId($id);

        if ($location) {
            $location->name = $request->input('name');
            $location->address = $request->input('address');
            $location->postal = $request->input('postal');
            $location->city = $request->input('city');
            $location->country = $request->input('country');
            $location->save();

            return new JsonResponse([
                'message' => 'Location ' . $id . ' has been updated',
                'data' => new LocationResource(Location::getByExternalId($id))
            ]);
        }

        // it's a new location, create it
        $location = new Location([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'postal' => $request->input('postal'),
            'city' => $request->input('city'),
            'country' => $request->input('country'),
            'externalId' => $id
        ]);
        $location->save();

        return new JsonResponse([
            'message' => 'Location ' . $id . ' was created',
            'data' => new LocationResource(Location::getByExternalId($id))
        ]);
    }
}
