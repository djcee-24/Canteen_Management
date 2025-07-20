<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(empty($cartItems))
        <!-- Empty Cart State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Add some delicious items to proceed with checkout.</p>
            <a href="{{ route('menu') }}" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-150">
                Browse Menu
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Summary -->
            <div class="lg:col-span-2">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Checkout</h2>
                    
                    @if($isGuest)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-yellow-800">Guest Checkout Notice</h3>
                                    <p class="text-sm text-yellow-700 mt-1">As a guest, you can only place online orders with online payment methods.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Cart Items Review -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @foreach($cartItems as $key => $item)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <!-- Image -->
                                <div class="flex-shrink-0">
                                    @if($item['image'])
                                        <img class="h-16 w-16 object-cover rounded-lg" src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}">
                                    @else
                                        <div class="h-16 w-16 bg-gray-300 rounded-lg flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $item['name'] }}</h4>
                                    <p class="text-sm text-gray-600">₱{{ number_format($item['price'], 2) }} each</p>
                                    @if(!empty($item['customizations']))
                                        <p class="text-xs text-gray-500 mt-1">
                                            @foreach($item['customizations'] as $key => $value)
                                                {{ $key }}: {{ is_array($value) ? implode(', ', $value) : $value }}
                                            @endforeach
                                        </p>
                                    @endif
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-3">
                                    <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" 
                                            class="text-red-600 hover:text-red-800 bg-red-100 rounded-full p-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="text-lg font-medium w-8 text-center">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" 
                                            class="text-green-600 hover:text-green-800 bg-green-100 rounded-full p-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Price -->
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        ₱{{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </p>
                                </div>

                                <!-- Remove -->
                                <button wire:click="removeItem('{{ $key }}')" 
                                        class="text-red-500 hover:text-red-700 ml-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="customerName" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input wire:model="customerName" type="text" id="customerName" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @error('customerName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="customerPhone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input wire:model="customerPhone" type="tel" id="customerPhone" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @error('customerPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="customerEmail" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input wire:model="customerEmail" type="email" id="customerEmail" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @error('customerEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="orderType" class="block text-sm font-medium text-gray-700 mb-1">Order Type *</label>
                            <select wire:model.live="orderType" id="orderType" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    @if($isGuest) disabled @endif>
                                <option value="dine_in">Dine In</option>
                                <option value="takeaway">Takeaway</option>
                                <option value="online">Online Order</option>
                            </select>
                            @error('orderType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="paymentMethod" class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                            <select wire:model.live="paymentMethod" id="paymentMethod" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                @if(!$isGuest)
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                @endif
                                <option value="online">Online Payment</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                            @error('paymentMethod') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="specialInstructions" class="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
                            <textarea wire:model="specialInstructions" id="specialInstructions" rows="3" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                      placeholder="Any special requests or dietary requirements..."></textarea>
                            @error('specialInstructions') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <label class="flex items-start">
                        <input wire:model="agreedToTerms" type="checkbox" 
                               class="mt-1 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">
                            I agree to the <a href="#" class="text-red-600 hover:underline">Terms of Service</a> 
                            and <a href="#" class="text-red-600 hover:underline">Privacy Policy</a> *
                        </span>
                    </label>
                    @error('agreedToTerms') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                    
                    <!-- Summary Items -->
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">₱{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax ({{ $taxRate * 100 }}%):</span>
                            <span class="font-medium">₱{{ number_format($taxAmount, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total:</span>
                                <span class="text-xl font-bold text-red-600">₱{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Estimated Time -->
                    <div class="bg-yellow-50 rounded-lg p-3 mb-6">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Estimated prep time:</p>
                                <p class="text-sm text-yellow-600">{{ $this->calculateEstimatedCompletionTime()->diffInMinutes(now()) }} minutes</p>
                            </div>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button wire:click="placeOrder" 
                            wire:loading.attr="disabled"
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-red-700 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Place Order</span>
                        <span wire:loading class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>

                    <!-- Continue Shopping -->
                    <a href="{{ route('menu') }}" 
                       class="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-300 transition duration-150 text-center block mt-3">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>