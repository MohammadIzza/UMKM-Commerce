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
            
            <div class="p-3 flex justify-between">
              <div>Subtotal</div>
              <div id="subtotal-amount">Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</div>
            </div>
            
            <div class="p-3 flex justify-between">
              <div>
                <div class="font-medium">Ongkos Kirim</div>
                <div class="text-xs text-gray-500" id="shipping-info">
                  @php
                    $defaultShipping = $shippingMethods->first();
                  @endphp
                  {{ $defaultShipping ? $defaultShipping->name : 'Pilih metode pengiriman' }}
                  @if($cart->total_weight > 0)
                    <span class="text-blue-600">({{ number_format($cart->total_weight, 1) }}kg)</span>
                  @endif
                  @if($defaultShipping && $defaultShipping->estimated_days)
                    <br><span class="text-green-600">Est: {{ $defaultShipping->estimated_days }}</span>
                  @endif
                </div>
              </div>
              <div class="text-right">
                              <div id="shipping-cost">
                @if($defaultShipping)
                  Rp {{ number_format($defaultShipping->base_cost + ($defaultShipping->cost_per_kg * ceil($cart->total_weight)), 0, ',', '.') }}
                @else
                  Rp 0
                @endif
              </div>
                @if($defaultShipping && $cart->total_weight > 0)
                  <div class="text-xs text-gray-500 breakdown">
                    Base: {{ number_format($defaultShipping->base_cost, 0, ',', '.') }} + 
                    {{ number_format($defaultShipping->cost_per_kg * ceil($cart->total_weight), 0, ',', '.') }}
                  </div>
                @endif
              </div>
            </div>
            
            <div class="p-3 font-medium flex justify-between bg-gray-50">
              <div>Total</div>
              <div id="total-amount">
                @php
                  $shippingCost = $defaultShipping ? ($defaultShipping->base_cost + ($defaultShipping->cost_per_kg * ceil($cart->total_weight))) : 0;
                @endphp
                Rp {{ number_format($cart->subtotal + $shippingCost, 0, ',', '.') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const shippingSelect = document.querySelector('select[name="shipping_method_id"]');
      const shippingCostElement = document.getElementById('shipping-cost');
      const shippingInfoElement = document.getElementById('shipping-info');
      const totalAmountElement = document.getElementById('total-amount');
      const subtotalAmountElement = document.getElementById('subtotal-amount');
      
      // Shipping methods data
      const shippingMethods = @json($shippingMethods->keyBy('id'));
      const totalWeight = {{ $cart->total_weight }};
      const subtotal = {{ $cart->subtotal }};
      
      function updateShippingCost() {
        const selectedMethodId = shippingSelect.value;
        const selectedMethod = shippingMethods[selectedMethodId];
        
        if (selectedMethod) {
          const baseCost = parseFloat(selectedMethod.base_cost);
          const costPerKg = parseFloat(selectedMethod.cost_per_kg || 0);
          const ceilWeight = Math.ceil(totalWeight);
          const shippingCost = baseCost + (costPerKg * ceilWeight);
          const total = subtotal + shippingCost;
          
          // Format numbers to Indonesian format
          const formatRupiah = (amount) => {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
          };
          
          // Update shipping cost display
          shippingCostElement.textContent = formatRupiah(shippingCost);
          
          // Update breakdown if exists
          const breakdownElement = shippingCostElement.parentElement.querySelector('.breakdown');
          if (breakdownElement && totalWeight > 0) {
            const weightCost = costPerKg * ceilWeight;
            breakdownElement.textContent = `Base: ${formatRupiah(baseCost).replace('Rp ', '')} + ${formatRupiah(weightCost).replace('Rp ', '')}`;
          }
          
          // Update shipping info
          let infoHtml = selectedMethod.name;
          if (totalWeight > 0) {
            infoHtml += ` <span class="text-blue-600">(${totalWeight.toFixed(1)}kg)</span>`;
          }
          if (selectedMethod.estimated_days) {
            infoHtml += `<br><span class="text-green-600">Est: ${selectedMethod.estimated_days}</span>`;
          }
          shippingInfoElement.innerHTML = infoHtml;
          
          // Update total
          totalAmountElement.textContent = formatRupiah(total);
        }
      }
      
      // Update when shipping method changes
      if (shippingSelect) {
        shippingSelect.addEventListener('change', updateShippingCost);
        // Initial update
        updateShippingCost();
      }
    });
  </script>
</x-app-layout>
