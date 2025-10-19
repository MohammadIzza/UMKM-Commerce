@extends('layouts.admin')

@section('header')
    Order #{{ $order->id }} Details
@endsection

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Order Information
                </h3>
                <div class="flex items-center justify-between">
                    @if($order->canBeModified())
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex items-center gap-4 w-full">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center gap-3 w-full max-w-2xl">
                                <div class="w-24">
                                    <span class="text-sm font-medium text-gray-900">Status</span>
                                </div>
                                <div class="flex-1">
                                    <select name="status" id="status" class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                                        @php
                                            $availableTransitions = $order->getAvailableStatusTransitions();
                                            $allStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
                                        @endphp
                                        
                                        {{-- Current status is always selectable --}}
                                        <option value="{{ $order->status }}" selected>{{ ucfirst($order->status) }} (Current)</option>
                                        
                                        {{-- Available transitions --}}
                                        @foreach($availableTransitions as $status)
                                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-w-[140px]">
                                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="flex items-center gap-4 w-full">
                            <div class="flex items-center gap-3 w-full max-w-2xl">
                                <div class="w-24">
                                    <span class="text-sm font-medium text-gray-900">Status</span>
                                </div>
                                <div class="flex-1">
                                    <div class="block w-full pl-3 pr-10 py-2 text-sm bg-gray-100 border border-gray-300 rounded-md">
                                        <span class="font-medium text-gray-700">{{ ucfirst($order->status) }}</span>
                                        <span class="text-xs text-gray-500 ml-2">(Final - Cannot be changed)</span>
                                    </div>
                                </div>
                                <div class="min-w-[140px] flex items-center">
                                    <div class="inline-flex items-center px-3 py-2 text-sm text-gray-500 bg-gray-100 rounded-md">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Locked
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->name }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->email }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Order Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                            @elseif($order->status == 'processing') bg-indigo-100 text-indigo-800
                            @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                            @elseif($order->status == 'delivered') bg-green-100 text-green-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @elseif($order->status == 'refunded') bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Shipping Method</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->shipping_method }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Shipping Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $order->shipping_address }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($item->product->images->first())
                                            <img class="h-10 w-10 rounded-full object-cover mr-3" 
                                                src="{{ Storage::url($item->product->images->first()->path) }}" 
                                                alt="{{ $item->product->name }}">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->product->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Shipping Cost</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900">Total Amount</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.orders') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Back to Orders List</a>
    </div>
@endsection