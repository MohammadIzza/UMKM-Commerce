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
        <div class="text-xl text-blue-700 mb-4">Rp {{ number_format($product->price,0,',','.') }}</div>
        <div class="prose max-w-none mb-4">{!! nl2br(e($product->description)) !!}</div>
        @auth
        <form method="POST" action="{{ url('/cart/add') }}" class="flex items-center gap-2 mb-4">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <input type="number" name="qty" value="1" min="1" class="border rounded px-3 py-2 w-24">
          <button class="bg-green-600 text-white px-4 py-2 rounded">Tambah ke Keranjang</button>
        </form>
        @else
          <a href="{{ route('login') }}" class="text-blue-600 underline">Login untuk membeli</a>
        @endauth
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
</x-app-layout>
