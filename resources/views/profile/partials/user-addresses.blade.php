<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('My Addresses') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Manage your delivery addresses. You can add, edit, or delete addresses here.') }}
        </p>
    </header>

    <div class="mt-6 space-y-4">
        <!-- Existing Addresses -->
        <div class="space-y-3">
            @forelse($addresses as $address)
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-medium">{{ $address->label ?? 'Alamat' }}</span>
                                @if($address->is_default)
                                    <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700">Default</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-700 mb-1">
                                <strong>{{ $address->recipient_name }}</strong> ({{ $address->phone }})
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $address->address_line_1 }}
                                @if($address->address_line_2), {{ $address->address_line_2 }}@endif
                                <br>{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            @unless($address->is_default)
                                <form method="POST" action="{{ route('addresses.default', $address) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                        Set Default
                                    </button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('addresses.destroy', $address) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this address?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-gray-500 text-center py-4">
                    No addresses yet. Add your first address below.
                </div>
            @endforelse
        </div>

        <!-- Add New Address Form -->
        <div class="border rounded-lg p-4 bg-white">
            <h3 class="font-medium mb-4">Add New Address</h3>
            <form method="POST" action="{{ route('addresses.store') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700">Label</label>
                    <input type="text" name="label" id="label" placeholder="Home, Office, etc." 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('label')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700">Recipient Name</label>
                        <input type="text" name="recipient_name" id="recipient_name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('recipient_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone" id="phone" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address_line_1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                    <textarea name="address_line_1" id="address_line_1" required rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    @error('address_line_1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address_line_2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                    <textarea name="address_line_2" id="address_line_2" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    @error('address_line_2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" id="city" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700">Province</label>
                        <input type="text" name="province" id="province" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-900">
                        Set as default address
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Add Address
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>