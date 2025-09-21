<x-app-layout>
  <div class="max-w-4xl mx-auto p-4 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Alamat Saya</h1>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="space-y-3">
        <h2 class="font-medium">Daftar Alamat</h2>
        @forelse($addresses as $addr)
          <div class="border rounded p-3 space-y-1">
            <div class="flex items-center justify-between">
              <div class="font-medium">{{ $addr->label ?? 'Alamat' }}</div>
              @if($addr->is_default)
                <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700">Default</span>
              @endif
            </div>
            <div class="text-sm text-gray-700">{{ $addr->recipient_name }} ({{ $addr->phone }})</div>
            <div class="text-sm">{{ $addr->address_line_1 }}, {{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</div>
            <div class="flex gap-2 mt-2">
              @unless($addr->is_default)
              <form method="POST" action="{{ url('/addresses/'.$addr->id.'/default') }}">
                @csrf
                <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Jadikan Default</button>
              </form>
              @endunless
              <form method="POST" action="{{ url('/addresses/'.$addr->id) }}" onsubmit="return confirm('Hapus alamat ini?')">
                @csrf
                @method('DELETE')
                <button class="px-3 py-1 bg-red-600 text-white rounded text-sm">Hapus</button>
              </form>
            </div>
          </div>
        @empty
          <div class="text-gray-500">Belum ada alamat.</div>
        @endforelse
      </div>

      <div class="space-y-3">
        <h2 class="font-medium">Tambah Alamat</h2>
        <form method="POST" action="{{ url('/addresses') }}" class="space-y-3">
          @csrf
          <input name="label" placeholder="Label (Rumah/Kantor)" class="border rounded px-3 py-2 w-full" />
          <div class="grid grid-cols-2 gap-3">
            <input name="recipient_name" placeholder="Nama Penerima" class="border rounded px-3 py-2" />
            <input name="phone" placeholder="No. HP" class="border rounded px-3 py-2" />
          </div>
          <input name="address_line_1" placeholder="Alamat" class="border rounded px-3 py-2 w-full" />
          <div class="grid grid-cols-3 gap-3">
            <input name="city" placeholder="Kota" class="border rounded px-3 py-2" />
            <input name="province" placeholder="Provinsi" class="border rounded px-3 py-2" />
            <input name="postal_code" placeholder="Kode Pos" class="border rounded px-3 py-2" />
          </div>
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_default" value="1" /> Jadikan default
          </label>
          <button class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
