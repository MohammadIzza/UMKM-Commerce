<x-app-layout>
  <div class="max-w-5xl mx-auto p-4 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Pesanan #{{ $order->id }}</h1>
      <span class="px-2 py-1 rounded bg-gray-100">Status: {{ ucfirst($order->status) }}</span>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="border rounded p-4">
        <h2 class="font-medium mb-2">Alamat Pengiriman</h2>
        <div class="text-sm text-gray-700">
          <div>{{ $order->customer_name }} ({{ $order->customer_phone }})</div>
          <div>{{ $order->shipping_address }}</div>
        </div>
      </div>
      <div class="border rounded p-4">
        <h2 class="font-medium mb-2">Ringkasan</h2>
        <div class="text-sm space-y-1">
          <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($order->items->sum(fn($i)=>$i->price*$i->qty),0,',','.') }}</span></div>
          <div class="flex justify-between"><span>Ongkir</span><span>Rp {{ number_format($order->shipping_cost,0,',','.') }}</span></div>
          <div class="flex justify-between font-medium"><span>Total</span><span>Rp {{ number_format($order->total,0,',','.') }}</span></div>
        </div>
      </div>
    </div>

    <div class="border rounded">
      <div class="p-4 font-medium">Produk</div>
      <div class="divide-y">
        @foreach($order->items as $item)
          <div class="p-4 flex justify-between">
            <div>{{ $item->product->name }} x {{ $item->qty }}</div>
            <div>Rp {{ number_format($item->price * $item->qty,0,',','.') }}</div>
          </div>
        @endforeach
      </div>
    </div>

    <a href="{{ url('/orders') }}" class="inline-block px-4 py-2 bg-gray-100 rounded">Kembali</a>
  </div>
</x-app-layout>
