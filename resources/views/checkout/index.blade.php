<x-app-layout>
  <div class="max-w-4xl mx-auto p-4 space-y-6">
    <h1 class="text-2xl font-semibold">Checkout</h1>

    @if($cart->items->isEmpty())
      <div class="text-gray-500">Keranjang kosong. <a href="/shop" class="text-blue-600 underline">Belanja sekarang</a></div>
    @else
      <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <h2 class="text-xl font-medium">Alamat Pengiriman</h2>
          <form id="checkout-form" method="POST" action="{{ url('/checkout') }}" class="space-y-4">
            @csrf

            @if($addresses->isNotEmpty())
              <div>
                <label class="block text-sm font-medium mb-1">Pilih Alamat</label>
                <select name="address_id" class="w-full border rounded px-3 py-2">
                  @foreach($addresses as $addr)
                    <option value="{{ $addr->id }}" {{ $addr->is_default ? 'selected' : '' }}>
                      {{ $addr->label ?? 'Alamat' }} - {{ $addr->recipient_name }} ({{ $addr->phone }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="text-sm text-gray-600">Atau gunakan alamat baru di bawah:</div>
            @endif

            <div class="border rounded p-3 space-y-3">
              <div class="grid grid-cols-2 gap-3">
                <input name="recipient_name" placeholder="Nama Penerima" class="border rounded px-3 py-2" />
                <input name="phone" placeholder="No. HP" class="border rounded px-3 py-2" />
              </div>
              <input name="address_line1" placeholder="Alamat" class="border rounded px-3 py-2 w-full" />
              <div class="grid grid-cols-3 gap-3">
                <input name="city" placeholder="Kota" class="border rounded px-3 py-2" />
                <input name="province" placeholder="Provinsi" class="border rounded px-3 py-2" />
                <input name="postal_code" placeholder="Kode Pos" class="border rounded px-3 py-2" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Metode Pengiriman</label>
              <select name="shipping_method_id" class="w-full border rounded px-3 py-2">
                @foreach($shippingMethods as $sm)
                  <option value="{{ $sm->id }}">{{ $sm->name }} - Rp {{ number_format($sm->base_cost,0,',','.') }} (+ Rp {{ number_format($sm->cost_per_kg ?? 0,0,',','.') }}/kg)</option>
                @endforeach
              </select>
            </div>

            <button class="px-4 py-2 bg-green-600 text-white rounded">Buat Pesanan</button>
          </form>
        </div>

        <div class="space-y-3">
          <h2 class="text-xl font-medium">Ringkasan</h2>
          <div class="border rounded divide-y">
            @foreach($cart->items as $item)
              <div class="p-3 flex justify-between">
                <div>{{ $item->product->name }} x {{ $item->qty }}</div>
                <div>Rp {{ number_format($item->price * $item->qty,0,',','.') }}</div>
              </div>
            @endforeach
            <div class="p-3 font-medium flex justify-between">
              <div>Subtotal</div>
              <div>Rp {{ number_format($cart->items->sum(fn($i)=>$i->price*$i->qty),0,',','.') }}</div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
</x-app-layout>
