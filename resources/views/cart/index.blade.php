<x-app-layout>
  <div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Keranjang</h1>
    @if($cart->items->isEmpty())
      <div class="text-gray-500">Keranjang kosong.</div>
    @else
      <div class="space-y-3">
        @foreach($cart->items as $item)
          <div class="flex items-center justify-between border rounded p-3">
            <div>
              <div class="font-medium">{{ $item->product->name }}</div>
              <div class="text-sm text-gray-600">Rp {{ number_format($item->price,0,',','.') }}</div>
            </div>
            <form method="POST" action="{{ url('/cart/items/'.$item->id) }}" class="flex items-center gap-2">
              @csrf
              @method('PATCH')
              <input type="number" name="qty" value="{{ $item->qty }}" min="1" class="border rounded px-2 py-1 w-20">
              <button class="px-3 py-1 bg-blue-600 text-white rounded">Update</button>
            </form>
            <form method="POST" action="{{ url('/cart/items/'.$item->id) }}">
              @csrf
              @method('DELETE')
              <button class="px-3 py-1 bg-red-600 text-white rounded">Hapus</button>
            </form>
          </div>
        @endforeach
      </div>
      <div class="mt-6">
        <a href="{{ url('/checkout') }}" class="px-4 py-2 bg-green-600 text-white rounded">Checkout</a>
      </div>
    @endif
  </div>
</x-app-layout>
