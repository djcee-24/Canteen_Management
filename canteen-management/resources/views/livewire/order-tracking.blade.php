<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Track Your Order</h1>
        <p class="text-gray-600">Enter your order number to see real-time updates</p>
    </div>

    <!-- Order Search -->
    @if(!$order)
        <div class="max-w-md mx-auto mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form wire:submit.prevent="searchOrder">
                    <div class="mb-4">
                        <label for="orderNumber" class="block text-sm font-medium text-gray-700 mb-2">Order Number</label>
                        <input wire:model="orderNumber" 
                               type="text" 
                               id="orderNumber"
                               placeholder="e.g., ORD-20240719-0001"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @error('orderNumber') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-red-700 transition duration-150">
                        Track Order
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Order Details -->
    @if($order)
        <div class="space-y-6">
            <!-- Order Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h2>
                        <p class="text-gray-600">Placed on {{ $order->created_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $this->statusColor }}-100 text-{{ $this->statusColor }}-800">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Customer:</span>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Order Type:</span>
                        <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Total:</span>
                        <p class="font-medium text-lg">₱{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Progress</h3>
                
                <div class="relative">
                    <!-- Progress Line -->
                    <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-200"></div>
                    
                    <!-- Steps -->
                    <div class="space-y-6">
                        @foreach($this->statusSteps as $stepKey => $step)
                            <div class="relative flex items-center">
                                <!-- Step Circle -->
                                <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full border-2 
                                    {{ $step['completed'] ? 'bg-green-500 border-green-500' : ($step['current'] ? 'bg-' . $this->statusColor . '-500 border-' . $this->statusColor . '-500' : 'bg-white border-gray-300') }}">
                                    @if($step['completed'])
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @elseif($step['current'])
                                        <div class="w-3 h-3 bg-white rounded-full"></div>
                                    @else
                                        <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                    @endif
                                </div>
                                
                                <!-- Step Content -->
                                <div class="ml-4 flex-1">
                                    <h4 class="text-sm font-medium {{ $step['completed'] || $step['current'] ? 'text-gray-900' : 'text-gray-500' }}">
                                        {{ $step['label'] }}
                                    </h4>
                                    @if($step['current'])
                                        <p class="text-xs text-{{ $this->statusColor }}-600 mt-1">Current status</p>
                                    @elseif($step['completed'])
                                        <p class="text-xs text-green-600 mt-1">Completed</p>
                                    @endif
                                </div>

                                <!-- Timestamp -->
                                @if($step['completed'] && $stepKey === 'completed' && $order->completed_at)
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">
                                            {{ $order->completed_at->format('g:i A') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Estimated Time -->
            @if($this->estimatedTimeRemaining && $order->status !== 'completed')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Estimated completion time:</h4>
                            <p class="text-sm text-yellow-700">{{ $this->estimatedTimeRemaining }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Order Items</h3>
                    <button wire:click="toggleDetails" 
                            class="text-red-600 hover:text-red-700 text-sm font-medium">
                        {{ $showDetails ? 'Hide Details' : 'Show Details' }}
                    </button>
                </div>

                @if($showDetails)
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $item->menu_item_name }}</h4>
                                    @if($item->special_instructions)
                                        <p class="text-sm text-gray-600 mt-1">Note: {{ $item->special_instructions }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    <p class="font-medium">₱{{ number_format($item->total_price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                                            <p class="text-gray-600">{{ $order->orderItems->count() }} items • ₱{{ number_format($order->total_amount, 2) }}</p>
                @endif
            </div>

            <!-- Special Instructions -->
            @if($order->special_instructions)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-800 mb-1">Special Instructions:</h4>
                    <p class="text-sm text-blue-700">{{ $order->special_instructions }}</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex justify-center space-x-4">
                <button wire:click="$set('order', null)" 
                        class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-300 transition duration-150">
                    Track Another Order
                </button>
                
                @if($order->canBeCancelled())
                    <button onclick="confirm('Are you sure you want to cancel this order?') || event.stopImmediatePropagation()" 
                            class="bg-red-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700 transition duration-150">
                        Cancel Order
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- Real-time Updates Notice -->
    <div class="text-center mt-8 text-sm text-gray-500">
        <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        This page updates automatically when your order status changes
    </div>
</div>