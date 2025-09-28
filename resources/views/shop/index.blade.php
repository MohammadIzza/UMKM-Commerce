<x-app-layout>
  
  <div class="max-w-7xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Katalog Produk</h1>
    <!-- Search bar -->
    <form method="GET" class="mb-4">
      <div class="flex gap-2 mb-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍Cari produk..." class="border rounded px-3 py-2 w-full">
        <button class="bg-green-600 text-white px-4 py-2 rounded">Cari</button>
      </div>
      
      <!-- Category Filter dibawah search -->
      <div class="flex flex-wrap gap-2">
        <a href="{{ url('/shop') }}" 
           class="px-4 py-2 rounded-full border text-sm {{ !request('category') ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
          Semua Kategori
        </a>
        @foreach($categories as $cat)
          <a href="{{ url('/shop?category=' . $cat->slug . (request('q') ? '&q=' . request('q') : '')) }}" 
             class="px-4 py-2 rounded-full border text-sm {{ request('category') === $cat->slug ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      @forelse($products as $p)
        <a href="{{ url('/shop/'.$p->slug) }}" class="border rounded p-3 block hover:shadow">
          <div class="aspect-square bg-gray-100 mb-2 flex items-center justify-center">
            <span class="text-gray-400">Gambar</span>
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
</x-app-layout>
