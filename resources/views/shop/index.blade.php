@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-4">
  <h1 class="text-2xl font-semibold mb-4">Katalog Produk</h1>

  <form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="border rounded px-3 py-2 w-full">
    <select name="category" class="border rounded px-3 py-2">
      <option value="">Semua Kategori</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->slug }}" @selected(request('category')===$cat->slug)>{{ $cat->name }}</option>
      @endforeach
    </select>
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
  </form>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @forelse($products as $p)
      <a href="{{ url('/shop/'.$p->slug) }}" class="border rounded p-3 block hover:shadow">
        <div class="aspect-square bg-gray-100 mb-2 flex items-center justify-center">
          <span class="text-gray-400">Gambar</span>
        </div>
        <div class="font-medium">{{ $p->name }}</div>
        <div class="text-sm text-gray-600">Rp {{ number_format($p->price,0,',','.') }}</div>
      </a>
    @empty
      <div class="col-span-4 text-center text-gray-500">Tidak ada produk</div>
    @endforelse
  </div>

  <div class="mt-6">{{ $products->links() }}</div>
</div>
@endsection
