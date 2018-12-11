<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author jimmiw
 * @since 2018-11-21
 */
class OrderTransformData
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isJson()) {
            $this->transformData($request->json());
        } else {
            $this->transformData($request->request);
        }

        return $next($request);
    }

    /**
     * @param ParameterBag $bag
     */
    private function transformData(ParameterBag $bag)
    {
        $bag->replace([
            'company' => $this->transformCompany($bag->get('company'), $bag->get('billing')),
            'participants' => $this->transformParticipants($bag->get('participants'))
        ]);
    }

    /**
     * Transforms the company data from the request, into something we can use to insert into the system
     * @param array $companyData the company data to transform
     * @param array $billingData the billing data to transform
     * @return array the transformed data, matching our requirements
     */
    private function transformCompany($companyData, $billingData)
    {
        return [
            'name' => $companyData['company'] ?? '',
            'cvr' => $companyData['vat'] ?? '',
            'attention' => $companyData['att'] ?? '',
            'address' => $companyData['address'] ?? '',
            'postal' => $companyData['zip'] ?? '',
            'city' => $companyData['city'] ?? '',
            'country' => $companyData['country'] ?? '',
            'phone' => $companyData['phone'] ?? '',
            'email' => $companyData['email'] ?? '',
            'ean' => $companyData['ean'] ?? '',
            'purchase_no' => $companyData['po'] ?? '',
            // billing data
            'billing_name' => $billingData['company'] ?? '',
            'billing_cvr' => $billingData['vat'] ?? '',
            'billing_attention' => $billingData['att'] ?? '',
            'billing_address' => $billingData['address'] ?? '',
            'billing_postal' => $billingData['zip'] ?? '',
            'billing_city' => $billingData['city'] ?? '',
            'billing_country' => $billingData['country'] ?? '',
            'billing_phone' => $billingData['phone'] ?? '',
            'billing_email' => $billingData['email'] ?? ''
        ];
    }

    /**
     * Takes the participant data, and transforms it into something we want in the system
     * @param array $data the participants sent from the frontend
     * @return array the transformed participant data
     */
    private function transformParticipants($data)
    {
        $participants = [];

        foreach ($data as $row) {
            $participants[] = [
                'name' => $row['fullname'],
                'email' => $row['email'],
                'title' => $row['title'],
                'phone' => $row['phone']
            ];
        }

        return $participants;
    }
}
