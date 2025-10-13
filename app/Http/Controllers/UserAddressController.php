<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    public function index()
    {
        $addresses = UserAddress::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();
        return response()->json($addresses);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($data['is_default'])) {
            UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $address = UserAddress::create($data + ['user_id' => Auth::id()]);
        
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Address saved', 'address' => $address]);
        }
        
        return redirect()->route('profile.edit')->with('success', 'Address added successfully!');
    }

    public function update(Request $request, UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $data = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($data['is_default'])) {
            UserAddress::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);
        return response()->json(['message' => 'Address updated', 'address' => $address]);
    }

    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);
        $address->delete();
        
        if (request()->wantsJson()) {
            return response()->json(['message' => 'Address deleted']);
        }
        
        return redirect()->route('profile.edit')->with('success', 'Address deleted successfully!');
    }

    public function setDefault(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);
        UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->is_default = true;
        $address->save();
        if (request()->wantsJson()) {
            return response()->json(['message' => 'Default address updated', 'address' => $address]);
        }
        return redirect()->route('profile.edit')->with('success', 'Default address updated successfully!');
    }
}
