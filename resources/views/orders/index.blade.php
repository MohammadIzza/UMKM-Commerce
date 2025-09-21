<x-app-layout>
  <div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Pesanan Saya</h1>
    @if($orders->count() === 0)
      <div class="text-gray-500">Belum ada pesanan.</div>
    @else
      <div class="border rounded divide-y">
        @foreach($orders as $order)
          <a href="{{ url('/orders/'.$order->id) }}" class="block p-4 hover:bg-gray-50">
            <div class="flex justify-between">
              <div>
                <div class="font-medium">#{{ $order->id }} - {{ ucfirst($order->status) }}</div>
                <div class="text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</div>
              </div>
                <div class="text-right">
                <div>Total: Rp {{ number_format($order->total,0,',','.') }}</div>
                <div class="text-sm text-gray-600">Pengiriman: Rp {{ number_format($order->shipping_cost,0,',','.') }}</div>
              </div>
            </div>
          </a>
        @endforeach
      </div>
      <div class="mt-4">
        {{ $orders->links() }}
      </div>
    @endif
  </div>
</x-app-layout>
