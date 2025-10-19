@use('Illuminate\Support\Facades\Storage')
<x-app-layout>
  
  <div class="max-w-7xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Katalog Produk</h1>

    <!-- Promo board (placeholder, auto-rotate) -->
    <div id="promo-board" class="mb-4">
      <div class="relative overflow-hidden rounded-md border border-gray-200">
        <!-- Viewport: fixed ratio 3:1 -->
        <div class="relative aspect-[4/1]">
          <!-- Sliding Track -->
          <div class="promo-inner flex h-full" style="transform: translateX(0%); transition: transform 500ms ease;">
            <a href="#" class="promo-item h-full flex items-center justify-center bg-gray-100 text-gray-400">
              <img class="w-full h-full object-cover" src="https://p16-images-comn-sg.tokopedia-static.net/tos-alisg-i-zr7vqa5nfb-sg/img/NsjrJu/2020/9/25/b1d2ed1e-ef80-4d7a-869f-a0394f0629be.jpg~tplv-zr7vqa5nfb-image.image" alt="Banner 1">
            </a>
            <a href="#" class="promo-item h-full flex items-center justify-center bg-gray-50 text-gray-400">
              <span><img class="success fade" src="https://p16-images-comn-sg.tokopedia-static.net/tos-alisg-i-zr7vqa5nfb-sg/img/home/defaultbanner/59e9ecd0-b91b-40d4-aef8-b1057be0_auto_x2.jpg~tplv-zr7vqa5nfb-image.image"></span>
            </a>
            <a href="#" class="promo-item h-full flex items-center justify-center bg-gray-100 text-gray-400">
              <span><img class="success fade" src="https://p16-images-comn-sg.tokopedia-static.net/tos-alisg-i-zr7vqa5nfb-sg/img/NsjrJu/2020/9/25/ea701ee6-f36b-473d-b429-4d2a1da0713d.jpg~tplv-zr7vqa5nfb-image.image"></span>
            </a>
          </div>
        </div>
        <!-- Dots -->
        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 transform flex gap-2">
          <button type="button" class="promo-dot w-2 h-2 rounded-full bg-white/80 ring-1 ring-gray-300" aria-label="Slide 1"></button>
          <button type="button" class="promo-dot w-2 h-2 rounded-full bg-white/40 ring-1 ring-gray-300" aria-label="Slide 2"></button>
          <button type="button" class="promo-dot w-2 h-2 rounded-full bg-white/40 ring-1 ring-gray-300" aria-label="Slide 3"></button>
        </div>
      </div>
    </div>

    <!-- Search bar -->
    <form method="GET" class="mb-4">
      <div class="flex gap-2 mb-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍Cari produk..." class="border rounded px-3 py-2 w-full border-gray-400 hover:border-gray-700 hover:shadow transition-colors">
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:border-green-300">Cari</button>
      </div>
      
      <!-- Category Filter dibawah search -->
      <div class="flex flex-wrap gap-2">
        <a href="{{ url('/shop') }}" 
           class="px-4 py-2 rounded-full border text-sm {{ !request('category') ? 'bg-green-600 text-white border-green-600 ' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:shadow-lg hover:border-green-500 transition-colors' }}">
          Semua Kategori
        </a>
        @foreach($categories as $cat)
          <a href="{{ url('/shop?category=' . $cat->slug . (request('q') ? '&q=' . request('q') : '')) }}" 
             class="px-4 py-2 rounded-full border text-sm {{ request('category') === $cat->slug ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:shadow-lg hover:border-green-500 transition-colors' }}">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </form>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      @forelse($products as $p)
        <a href="{{ url('/shop/'.$p->slug) }}" class="border rounded p-3 block border-gray-300 hover:border-gray-700 hover:shadow-lg transition-colors">
          <div class="aspect-square bg-gray-100 mb-2 flex items-center justify-center overflow-hidden rounded">
            @php
              $imgPath = null;
              if ($p->image) {
                $imgPath = $p->image;
              } elseif ($p->images->isNotEmpty()) {
                $imgPath = $p->images->first()->image_path;
              } elseif (is_array($p->gallery) && count($p->gallery) > 0) {
                $imgPath = $p->gallery[0];
              }
            @endphp
            @if($imgPath)
              @if(str_starts_with($imgPath, 'seed/'))
                <img src="{{ asset($imgPath) }}" alt="{{ $p->name }}" class="w-full h-full object-cover" loading="lazy">
              @else
                <img src="{{ Storage::url($imgPath) }}" alt="{{ $p->name }}" class="w-full h-full object-cover" loading="lazy">
              @endif
            @else
              <span class="text-gray-400">Gambar</span>
            @endif
          </div>
          <div class="font-medium">{{ $p->name }}</div>
          <div class="text-sm text-gray-600">Rp {{ number_format($p->price,0,',','.') }}</div>
          <div class="text-xs text-yellow-600 mt-1">
            @if($p->reviews_count > 0)
              🌟{{ number_format($p->reviews_avg_rating, 1) }}/5 ({{ $p->reviews_count }})
            @else
              🌟 Belum ada rating
            @endif
          </div>
        </a>
      @empty
        <div class="col-span-4 text-center text-gray-500">Tidak ada produk</div>
      @endforelse
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
  </div>

  <script>
    (function () {
      const root = document.getElementById('promo-board');
      if (!root) return;
      const inner = root.querySelector('.promo-inner');
      const items = Array.from(root.querySelectorAll('.promo-item'));
      const dots = root.querySelectorAll('.promo-dot');
      let i = 0, t = null, width = 0;

      function measure(){
        // Set each item width to viewport width
        const viewport = inner.parentElement; // relative h container
        width = viewport.clientWidth;
        items.forEach(el => { el.style.minWidth = width + 'px'; el.style.width = width + 'px'; });
        inner.style.width = (items.length * width) + 'px';
        // Reposition to current
        inner.style.transform = `translateX(-${i * width}px)`;
      }

      function show(n){
        i = n;
        inner.style.transition = 'transform 500ms ease';
        inner.style.transform = `translateX(-${i * width}px)`;
        dots.forEach((d, idx) => {
          d.classList.toggle('bg-white/80', idx === i);
          d.classList.toggle('bg-white/40', idx !== i);
        });
      }

      function next(){ show((i + 1) % items.length); }
      function restart(){ clearInterval(t); t = setInterval(next, 4000); }

      window.addEventListener('resize', ()=>{ const old = i; measure(); show(old); });
      dots.forEach((d, idx) => d.addEventListener('click', ()=>{ show(idx); restart(); }));

      measure();
      show(0);
      t = setInterval(next, 4000);
    })();
  </script>
</x-app-layout>
