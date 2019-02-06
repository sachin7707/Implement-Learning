<?php

namespace App\Http\Controllers;

use App\Trainer;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Trainer as TrainerResource;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2019-02-05
 */
class TrainerController extends Controller
{
    /**
     * Fetches the list of trainers
     */
    public function index()
    {
        return new JsonResponse(TrainerResource::collection(Trainer::all()));
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
            'phone' => 'string',
            'email' => 'string'
        ]);

        /** @var Trainer $trainer */
        $trainer = Trainer::getByExternalId($id);

        if ($trainer) {
            $trainer->name = $request->input('name');
            $trainer->email = $request->input('email');
            $trainer->phone = $request->input('phone');
            $trainer->save();

            return new JsonResponse([
                'message' => 'Trainer ' . $id . ' has been updated',
                'data' => new TrainerResource(Trainer::getByExternalId($id))
            ]);
        }

        // it's a new location, create it
        $trainer = new Trainer([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'externalId' => $id
        ]);
        $trainer->save();

        return new JsonResponse([
            'message' => 'Trainer ' . $id . ' was created',
            'data' => new TrainerResource(Trainer::getByExternalId($id))
        ]);
    }
}
