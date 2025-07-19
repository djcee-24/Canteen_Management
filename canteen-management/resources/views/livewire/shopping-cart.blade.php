<div class="relative" x-data="{ open: @entangle('showCart') }">
    <!-- Cart Button -->
    <button @click="open = !open" class="relative bg-yellow-400 text-red-600 px-4 py-2 rounded-full hover:bg-yellow-300 transition duration-150 flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2"></path>
        </svg>
        <span class="font-semibold">Cart</span>
        @if($cartCount > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs font-bold">
                {{ $cartCount }}
            </span>
        @endif
    </button>

    <!-- Cart Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl z-50 border border-gray-200">
        
        <!-- Cart Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-red-600 text-white rounded-t-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Your Order</h3>
                <button @click="open = false" class="text-white hover:text-yellow-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="max-h-96 overflow-y-auto">
            @if(empty($cartItems))
                <div class="p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8m-8 0v-2m8 2v-2"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-900 mb-1">Your cart is empty</p>
                    <p class="text-sm text-gray-500">Add some delicious items to get started!</p>
                </div>
            @else
                <div class="p-4 space-y-4">
                    @foreach($cartItems as $key => $item)
                        <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                            <!-- Item Image -->
                            <div class="flex-shrink-0">
                                @if($item['image'])
                                    <img class="h-12 w-12 object-cover rounded-lg" src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}">
                                @else
                                    <div class="h-12 w-12 bg-gray-300 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Item Details -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-500">${{ number_format($item['price'], 2) }} each</p>
                                @if(!empty($item['customizations']))
                                    <p class="text-xs text-gray-400 mt-1">
                                        @foreach($item['customizations'] as $key => $value)
                                            {{ $key }}: {{ is_array($value) ? implode(', ', $value) : $value }}
                                        @endforeach
                                    </p>
                                @endif
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-2">
                                <button wire:click="updateItemQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" 
                                        class="text-red-600 hover:text-red-800 bg-red-100 rounded-full p-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <span class="text-sm font-medium w-8 text-center">{{ $item['quantity'] }}</span>
                                <button wire:click="updateItemQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" 
                                        class="text-green-600 hover:text-green-800 bg-green-100 rounded-full p-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Item Total -->
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </p>
                            </div>

                            <!-- Remove Button -->
                            <button wire:click="removeItem('{{ $key }}')" 
                                    class="text-red-500 hover:text-red-700 ml-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Cart Footer -->
        @if(!empty($cartItems))
            <div class="border-t border-gray-200 px-4 py-3 bg-gray-50 rounded-b-lg">
                <!-- Preparation Time -->
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-gray-600">Estimated prep time:</span>
                    <span class="font-medium text-red-600">{{ $this->getTotalPreparationTime() }} minutes</span>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold text-gray-900">Total:</span>
                    <span class="text-xl font-bold text-red-600">${{ number_format($cartTotal, 2) }}</span>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <a href="{{ route('checkout') }}" 
                       class="w-full bg-red-600 text-white py-2 px-4 rounded-lg font-semibold text-center block hover:bg-red-700 transition duration-150">
                        Checkout
                    </a>
                    <div class="flex space-x-2">
                        <button wire:click="clearCart" 
                                class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-400 transition duration-150">
                            Clear Cart
                        </button>
                        <button @click="open = false" 
                                class="flex-1 bg-yellow-400 text-red-600 py-2 px-4 rounded-lg font-medium hover:bg-yellow-300 transition duration-150">
                            Continue Shopping
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>