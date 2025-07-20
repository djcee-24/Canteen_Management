<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search and Filters -->
    <div class="mb-8 bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search -->
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <input wire:model.live.debounce.300ms="searchTerm" 
                           type="text" 
                           placeholder="Search for food..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center space-x-4">
                <!-- Sort -->
                <select wire:model.live="sortBy" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                    <option value="sort_order">Default</option>
                    <option value="name">Name A-Z</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="newest">Newest First</option>
                    <option value="featured">Featured First</option>
                </select>

                <!-- Featured Toggle -->
                <label class="flex items-center">
                    <input wire:model.live="showFeaturedOnly" type="checkbox" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="ml-2 text-sm text-gray-700">Featured Only</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="mb-8">
        <div class="flex overflow-x-auto space-x-4 pb-4">
            <button wire:click="selectCategory(null)" 
                    class="flex-shrink-0 px-6 py-3 rounded-full font-medium transition duration-150 {{ $selectedCategory === null ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                All Items
            </button>
            @foreach($categories as $category)
                <button wire:click="selectCategory({{ $category->id }})" 
                        class="flex-shrink-0 px-6 py-3 rounded-full font-medium transition duration-150 whitespace-nowrap {{ $selectedCategory == $category->id ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    {{ $category->name }}
                    <span class="ml-2 text-xs opacity-75">({{ $category->menu_items_count }})</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Menu Items Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        @forelse($menuItems as $item)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 transform hover:-translate-y-1">
                <!-- Image -->
                <div class="relative h-48 bg-gray-200">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Featured Badge -->
                    @if($item->is_featured)
                        <div class="absolute top-2 left-2 bg-yellow-400 text-red-600 px-2 py-1 rounded-full text-xs font-bold">
                            ⭐ Featured
                        </div>
                    @endif

                    <!-- Favorite Button -->
                    <button wire:click="toggleFavorite({{ $item->id }})" 
                            class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-gray-50 transition duration-150">
                        <svg class="h-5 w-5 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $item->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">by {{ $item->user->name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-bold text-red-600">₱{{ number_format($item->price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($item->description)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $item->description }}</p>
                    @endif

                    <!-- Details -->
                    <div class="flex items-center text-xs text-gray-500 mb-3 space-x-4">
                        <span class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $item->preparation_time }} min
                        </span>
                        @if($item->calories)
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                {{ $item->calories }} cal
                            </span>
                        @endif
                    </div>

                    <!-- Dietary Info -->
                    @if($item->dietary_info)
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach($item->dietary_info as $info)
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                    {{ $info }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add to Cart Button -->
                    <button wire:click="addToCart({{ $item->id }})" 
                            @if(!$item->is_available) disabled @endif
                            class="w-full py-2 px-4 rounded-lg font-semibold transition duration-150 {{ $item->is_available ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                        @if($item->is_available)
                            Add to Cart
                        @else
                            Currently Unavailable
                        @endif
                    </button>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.469 1.009-5.927 2.618l-.184.182C4.933 18.744 5.99 20 7.616 20h8.768c1.626 0 2.683-1.256 1.727-2.2l-.184-.182A7.962 7.962 0 0112 15z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No items found</h3>
                <p class="text-gray-500">Try adjusting your search or filters to find what you're looking for.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $menuItems->links() }}
    </div>
</div>