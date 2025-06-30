<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPrice;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubcriptionPriceController extends Controller
{
    public function edit()
    {
        $subscriptionPrice = SubscriptionPrice::first();

        return view('backend.layouts.settings.subscription_price', compact('subscriptionPrice'));
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $subscriptionPrice = SubscriptionPrice::first();

            if (!$subscriptionPrice) {
                $subscriptionPrice = new SubscriptionPrice();
            }
            $subscriptionPrice->name = $request->name;
            $subscriptionPrice->price = $request->price;
            $subscriptionPrice->save();

            return redirect()->back()->with('t-success', 'Subscription price updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong while updating the price.');
        }
    }
}
