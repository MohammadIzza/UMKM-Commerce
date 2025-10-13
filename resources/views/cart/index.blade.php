<x-app-layout>
  <div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-6">Keranjang</h1>
    
    @if($cart->items->isEmpty())
      <div class="text-center py-8">
        <div class="text-gray-500 text-lg">Keranjang kosong</div>
        <a href="{{ route('shop.index') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Mulai Belanja
        </a>
      </div>
    @else
      <div class="space-y-4">
        @foreach($cart->items as $item)
          <div class="bg-white border rounded-lg p-4 shadow-sm" data-item-id="{{ $item->id }}">
            <div class="flex items-center justify-between">
              <!-- Product Info -->
              <div class="flex-1">
                <h3 class="font-semibold text-lg text-gray-900">{{ $item->product->name }}</h3>
                <p class="text-gray-600 mt-1">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
              </div>

              <!-- Quantity Controls -->
              <div class="flex items-center gap-4">
                <!-- Minus Button -->
                <button type="button" 
                        class="minus-btn w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        onclick="console.log('Minus clicked for item {{ $item->id }}'); updateQuantity('{{ $item->id }}', 'decrease')"
                        data-item-id="{{ $item->id }}"
                        {{ $item->qty <= 1 ? 'disabled' : '' }}>
                  <span class="text-gray-600 font-medium">−</span>
                </button>

                <!-- Quantity Display -->
                <span class="qty-display font-medium text-lg min-w-[2rem] text-center" data-item-id="{{ $item->id }}">{{ $item->qty }}</span>

                <!-- Plus Button -->
                <button type="button" 
                        class="plus-btn w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50"
                        onclick="console.log('Plus clicked for item {{ $item->id }}'); updateQuantity('{{ $item->id }}', 'increase')"
                        data-item-id="{{ $item->id }}">
                  <span class="text-gray-600 font-medium">+</span>
                </button>

                <!-- Update Button (Hidden, used for form submission) -->
                <form method="POST" action="{{ route('cart.items.update', $item->id) }}" class="hidden update-form" data-item-id="{{ $item->id }}">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="qty" class="qty-input" value="{{ $item->qty }}">
                </form>

                <!-- Delete Button -->
                <form method="POST" action="{{ route('cart.items.destroy', $item->id) }}" class="ml-4" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition-colors">
                    Hapus
                  </button>
                </form>
              </div>
            </div>

            <!-- Subtotal for this item -->
            <div class="mt-3 text-right">
              <span class="text-gray-600">Subtotal: </span>
              <span class="font-semibold text-lg item-subtotal" data-item-id="{{ $item->id }}" data-price="{{ $item->product->price }}">
                Rp {{ number_format($item->product->price * $item->qty, 0, ',', '.') }}
              </span>
            </div>
          </div>
        @endforeach
      </div>

      <!-- Cart Summary -->
      <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <span class="text-lg font-medium">Total Keranjang:</span>
          <span class="text-2xl font-bold text-green-600" id="cart-total">
            Rp {{ number_format($cart->subtotal, 0, ',', '.') }}
          </span>
        </div>
        
        <div class="flex gap-4">
          <a href="{{ route('shop.index') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg text-center hover:bg-gray-50 transition-colors">
            Lanjut Belanja
          </a>
          <a href="{{ route('checkout.index') }}" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg text-center hover:bg-green-700 transition-colors font-medium">
            Checkout
          </a>
        </div>
      </div>
    @endif
  </div>

  <script>
  // Define functions in global scope
  window.updateQuantity = function(itemId, action) {
    console.log('updateQuantity called:', itemId, action);
    
    const qtyDisplay = document.querySelector(`.qty-display[data-item-id="${itemId}"]`);
    const updateForm = document.querySelector(`.update-form[data-item-id="${itemId}"]`);
    const qtyInput = updateForm.querySelector('.qty-input');
    const minusBtn = document.querySelector(`.minus-btn[data-item-id="${itemId}"]`);
    const plusBtn = document.querySelector(`.plus-btn[data-item-id="${itemId}"]`);
    const itemSubtotal = document.querySelector(`.item-subtotal[data-item-id="${itemId}"]`);
    const price = parseFloat(itemSubtotal.dataset.price);
    
    let currentQty = parseInt(qtyDisplay.textContent);
    let newQty = currentQty;
    
    console.log('Current qty:', currentQty, 'Price:', price);
    
    if (action === 'increase') {
      newQty = currentQty + 1;
    } else if (action === 'decrease' && currentQty > 1) {
      newQty = currentQty - 1;
    }
    
    console.log('New qty:', newQty);
    
    if (newQty !== currentQty) {
      // Add loading state
      minusBtn.disabled = true;
      plusBtn.disabled = true;
      qtyDisplay.style.opacity = '0.6';
      
      // Update display immediately for better UX
      qtyDisplay.textContent = newQty;
      qtyInput.value = newQty;
      
      // Update item subtotal
      const newSubtotal = price * newQty;
      itemSubtotal.textContent = 'Rp ' + numberFormat(newSubtotal);
      
      // Update cart total
      updateCartTotal();
      
      // Get CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      console.log('CSRF token:', csrfToken);
      console.log('Form action:', updateForm.action);
      
      // Create form data manually
      const formData = new URLSearchParams();
      formData.append('_token', csrfToken);
      formData.append('_method', 'PATCH');
      formData.append('qty', newQty);
      
      fetch(updateForm.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      })
      .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
          return response.text().then(text => {
            console.log('Error response text:', text);
            throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
          });
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
          return response.json();
        } else {
          return response.text().then(text => {
            console.log('Non-JSON response:', text);
            return { message: 'Updated successfully', text: text };
          });
        }
      })
      .then(data => {
        console.log('Success response:', data);
        qtyDisplay.style.opacity = '1';
        showToast('Keranjang diperbarui', 'success');
      })
      .catch(error => {
        console.error('Error updating quantity:', error);
        showToast('Gagal memperbarui keranjang', 'error');
        // Revert changes on error
        qtyDisplay.textContent = currentQty;
        qtyInput.value = currentQty;
        itemSubtotal.textContent = 'Rp ' + numberFormat(price * currentQty);
        updateCartTotal();
      })
      .finally(() => {
        // Remove loading state
        qtyDisplay.style.opacity = '1';
        plusBtn.disabled = false;
        // Enable/disable minus button based on quantity
        minusBtn.disabled = newQty <= 1;
      });
    }
  };
    
  window.updateCartTotal = function() {
    let total = 0;
    document.querySelectorAll('.item-subtotal').forEach(element => {
      const itemId = element.dataset.itemId;
      const qty = parseInt(document.querySelector(`.qty-display[data-item-id="${itemId}"]`).textContent);
      const price = parseFloat(element.dataset.price);
      total += price * qty;
    });
    
    document.getElementById('cart-total').textContent = 'Rp ' + numberFormat(total);
  };
  
  window.numberFormat = function(number) {
    return new Intl.NumberFormat('id-ID').format(number);
  };
  
  window.showToast = function(message, type = 'success') {
    // Remove existing toast
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
      existingToast.remove();
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast fixed top-4 right-4 px-4 py-2 rounded-lg text-white text-sm font-medium z-50 transition-opacity duration-300 ${
      type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    toast.textContent = message;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  };
  </script>

  @push('scripts')
  @endpush
</x-app-layout>
