@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('cart.index') }}" class="hover:text-gray-700">Cart</a></li>
                <li><span class="px-2">/</span></li>
                <li class="font-medium text-gray-900">Checkout</li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Form Checkout -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6" id="checkout-form">
                    @csrf
                    
                    <!-- Sections -->
                    <div class="divide-y divide-gray-200">
                        <!-- Section Title -->
                        <div class="px-6 py-4 bg-gray-50">
                            <h2 class="text-lg font-semibold text-gray-900">Complete Your Order</h2>
                        </div>

                        <!-- Shipping Address Section -->
                        <div class="p-6">
                            <h3 class="flex items-center text-base font-medium text-gray-900 mb-4">
                                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-600 text-sm mr-2">1</span>
                                Shipping Address
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" name="customer_name" value="{{ auth()->user()->name }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input type="tel" name="customer_phone" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="shipping_address" rows="3" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">City</label>
                                <input type="text" name="shipping_city" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Province</label>
                                <input type="text" name="shipping_province" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Postal Code</label>
                                <input type="text" name="shipping_postal_code" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                        <!-- Shipping Method -->
                        <div class="p-6">
                            <h3 class="flex items-center text-base font-medium text-gray-900 mb-4">
                                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-600 text-sm mr-2">2</span>
                                Shipping Method
                            </h3>
                            <div class="grid grid-cols-1 gap-3">
                            @foreach($shipping_methods as $method)
                                <label for="shipping_{{ $method->id }}" 
                                    class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-blue-500">
                                    <input type="radio" id="shipping_{{ $method->id }}" name="shipping_method_id" 
                                        value="{{ $method->id }}" required
                                        data-cost="{{ $method->base_cost }}"
                                        class="shipping-method-radio h-4 w-4 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3 flex flex-1 items-center justify-between">
                                        <div>
                                            <span class="block text-sm font-medium text-gray-900">{{ $method->name }}</span>
                                            <span class="block text-sm text-gray-500">{{ $method->estimated_days }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format($method->base_cost, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                        <!-- Payment Method -->
                        <div class="p-6">
                            <h3 class="flex items-center text-base font-medium text-gray-900 mb-4">
                                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-600 text-sm mr-2">3</span>
                                Payment Method
                            </h3>
                            <div class="grid grid-cols-1 gap-3">
                                <label for="payment_bank" 
                                    class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-blue-500">
                                    <input type="radio" id="payment_bank" name="payment_method" value="bank_transfer" required
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Bank Transfer</span>
                                        <span class="block text-sm text-gray-500">Transfer to our bank account</span>
                                    </div>
                                </label>
                        </div>
                    </div>

                        <!-- Place Order Button -->
                        <div class="p-6 bg-gray-50 border-t border-gray-200">
                            <button type="submit" 
                                class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg text-base font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                Place Order
                            </button>
                            <p class="mt-4 text-sm text-center text-gray-500">
                                By placing your order, you agree to our
                                <a href="#" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a>
                            </p>
                        </div>
                </form>
            </div>
        </div>

        <!-- Ringkasan -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900">Ringkasan</h3>
                </div>
                
                <!-- Products List -->
                <div class="p-6">
                    <div class="space-y-4">
                    @foreach($cart->items as $item)
                        <div class="flex justify-between py-3">
                            <div class="flex-1">
                                <h4 class="text-sm text-gray-900">{{ $item->product->name }} x {{ $item->qty }}</h4>
                            </div>
                            <p class="text-sm text-gray-900 ml-4">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>

                    <!-- Cost Summary -->
                    <div class="mt-6 space-y-3 border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-gray-900">Subtotal</span>
                            <span class="text-gray-900">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-900">Total Ongkir</span>
                            <span class="text-gray-900" id="shipping-cost">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-900 font-medium">Total Pembayaran</span>
                            <span class="text-gray-900 font-medium" id="total-cost">
                                Rp {{ number_format($cart->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingRadios = document.querySelectorAll('.shipping-method-radio');
    const shippingCostDisplay = document.getElementById('shipping-cost');
    const totalCostDisplay = document.getElementById('total-cost');
    const subtotal = {{ $cart->subtotal }};

    shippingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const shippingCost = parseFloat(this.dataset.cost);
            const total = subtotal + shippingCost;
            
            shippingCostDisplay.textContent = `Rp ${numberFormat(shippingCost)}`;
            totalCostDisplay.textContent = `Rp ${numberFormat(total)}`;
        });
    });

    function numberFormat(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }
});
</script>
@endpush
@endsection