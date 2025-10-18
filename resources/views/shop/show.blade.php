<x-app-layout>
  <div class="max-w-5xl mx-auto p-4">
    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <div class="aspect-square bg-gray-100 flex items-center justify-center mb-2">
          <span class="text-gray-400">Gambar</span>
        </div>
      </div>

      <div>
        <h1 class="text-2xl font-semibold mb-2">{{ $product->name }}</h1>
        <div class="text-xl text-green-600 mb-4">Rp {{ number_format($product->price,0,',','.') }}</div>

        <!-- Box: Atur Jumlah-->
        <div id="purchaseBox" class="border rounded-lg p-4 shadow-sm">
          <div class="font-semibold mb-3">Atur Jumlah</div>
          <div class="flex items-center justify-between mb-3">
            <div class="inline-flex items-center border rounded-md overflow-hidden {{ $product->stock <= 0 ? 'opacity-50' : '' }}">
              <button type="button" id="decBtn" class="px-3 py-2 text-gray-600 hover:bg-gray-50 disabled:opacity-40" aria-label="Kurangi" {{ $product->stock <= 0 ? 'disabled' : '' }}>−</button>
              <input id="qtyInput" type="number" min="1" step="1" inputmode="numeric" value="1"
                     class="w-24 sm:w-28 shrink-0 text-center border-x px-3 py-2 focus:outline-none"
                     {{ $product->stock <= 0 ? 'disabled' : '' }}>
              <button type="button" id="incBtn" class="px-3 py-2 text-green-600 hover:bg-gray-50 disabled:opacity-40" aria-label="Tambah" {{ $product->stock <= 0 ? 'disabled' : '' }}>+</button>
            </div>
            <div class="text-sm {{ $product->stock <= 5 ? 'text-red-600' : 'text-gray-600' }}">
              Stok Total: <span class="font-semibold">{{ $product->stock ?? 0 }}</span>
              @if($product->stock <= 5 && $product->stock > 0)
                <span class="text-xs">(Stok terbatas!)</span>
              @endif
            </div>
            
            @if($cartItem)
              <div class="text-sm text-blue-600 mt-1">
                ✓ Sudah ada {{ $cartItem->qty }} di keranjang
              </div>
            @endif
          </div>

          <div class="text-sm text-gray-500">Subtotal</div>
          <div class="flex items-baseline gap-3 mb-4">
            @php $original = $product->original_price ?? null; @endphp
            @if($original && $original > $product->price)
              <div class="text-gray-400 line-through">Rp{{ number_format($original,0,',','.') }}</div>
            @endif
            <div id="subtotalText" class="text-2xl font-semibold">Rp {{ number_format($product->price,0,',','.') }}</div>
          </div>

          @auth
            @if($product->stock <= 0)
              <div class="w-full mt-3 bg-red-500 text-white font-semibold py-3 rounded text-center">
                ❌ Stok Habis
              </div>
              <div class="mt-2 text-sm text-red-600 text-center">
                Produk ini tidak tersedia saat ini
              </div>
            @elseif($cartItem && $cartItem->qty >= $product->stock)
              <div class="w-full mt-3 bg-yellow-500 text-white font-semibold py-3 rounded text-center">
                Maksimal Stok Tercapai
              </div>
              <a href="{{ route('cart.index') }}" class="block w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded text-center">
                Lihat Keranjang
              </a>
            @else
              <form method="POST" action="{{ url('/cart/add') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" id="qtyField" name="qty" value="1">
                <button type="submit" class="w-full mt-3 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded">
                  + Keranjang
                  @if($cartItem)
                    ({{ $product->stock - $cartItem->qty }} tersisa)
                  @endif
                </button>
              </form>
            @endif
          @else
            <a href="{{ route('login') }}" class="text-green-600 underline">Login untuk membeli</a>
          @endauth
        </div>

        <!-- Deskripsi Produk -->
        @if(filled($product->description))
        <div class="mt-6">
          <h2 class="text-lg font-semibold mb-2">Deskripsi Produk</h2>
          <div class="text-sm leading-relaxed text-gray-700 whitespace-pre-line">
            {!! nl2br(e($product->description)) !!}
          </div>
        </div>
        @endif
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-lg font-semibold mb-2">Ulasan</h2>
      @forelse($product->reviews as $r)
        <div class="border-b py-3">
          <div class="font-medium">{{ $r->user->name ?? 'User' }} — ⭐ {{ $r->rating }}/5</div>
          <div class="text-sm text-gray-700">{{ $r->title }}</div>
          <div class="text-sm">{{ $r->comment }}</div>
        </div>
      @empty
        <div class="text-gray-500">Belum ada ulasan.</div>
      @endforelse
    </div>
  </div>

  <script>
    (function () {
      const stock = {{ (int)($product->stock ?? 0) }};
      const price = {{ (int)$product->price }};
      const box = document.getElementById('purchaseBox');
      const qtyInput = document.getElementById('qtyInput');
      const qtyField = document.getElementById('qtyField');
      const decBtn = document.getElementById('decBtn');
      const incBtn = document.getElementById('incBtn');
      const subtotalText = document.getElementById('subtotalText');
      const submitBtn = box ? box.querySelector('button[type="submit"]') : null;

      const hasStock = stock > 0;
      const maxQty = hasStock ? stock : Number.POSITIVE_INFINITY;

      function formatIDR(n) { return 'Rp' + n.toLocaleString('id-ID'); }

      function sync() {
        let qty = parseInt(qtyInput.value) || 1;
        qty = Math.max(1, Math.min(maxQty, qty));

        qtyInput.value = qty;
        if (qtyField) qtyField.value = qty;

        subtotalText.textContent = formatIDR(price * qty);

        decBtn.disabled = !hasStock || qty <= 1;
        incBtn.disabled = !hasStock || qty >= maxQty;

        if (submitBtn) {
          submitBtn.disabled = !hasStock;
          submitBtn.classList.toggle('opacity-60', !hasStock);
          submitBtn.classList.toggle('cursor-not-allowed', !hasStock);
          if (!hasStock) submitBtn.textContent = 'Stok Habis';
        }
      }

      // Set max attribute sesuai stok (kalau ada)
      if (hasStock) qtyInput.setAttribute('max', stock); else qtyInput.removeAttribute('max');
      // Cegah scroll mouse mengubah nilai tak sengaja
      qtyInput.addEventListener('wheel', () => qtyInput.blur(), { passive: true });

      decBtn.addEventListener('click', () => { qtyInput.value = (parseInt(qtyInput.value) || 1) - 1; sync(); });
      incBtn.addEventListener('click', () => { qtyInput.value = (parseInt(qtyInput.value) || 1) + 1; sync(); });
      qtyInput.addEventListener('input', sync);

      sync();
    })();
  </script>
</x-app-layout>
