<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MenuCategory;
use App\Models\MenuItem;

class MenuDisplay extends Component
{
    use WithPagination;

    public $selectedCategory = null;
    public $searchTerm = '';
    public $sortBy = 'sort_order';
    public $showFeaturedOnly = false;
    public $priceRange = [0, 100];

    protected $queryString = [
        'selectedCategory' => ['except' => null],
        'searchTerm' => ['except' => ''],
        'sortBy' => ['except' => 'sort_order'],
        'showFeaturedOnly' => ['except' => false],
    ];

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedShowFeaturedOnly()
    {
        $this->resetPage();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    public function addToCart($menuItemId, $quantity = 1)
    {
        $this->dispatch('addToCart', $menuItemId, $quantity);
    }

    public function toggleFavorite($menuItemId)
    {
        // Implementation for user favorites (requires user authentication)
        if (auth()->check()) {
            // Add favorite logic here
            session()->flash('info', 'Favorites feature coming soon!');
        } else {
            session()->flash('error', 'Please login to add favorites');
        }
    }

    public function getMenuItems()
    {
        $query = MenuItem::query()
            ->with(['menuCategory', 'user'])
            ->where('is_available', true);

        // Filter by category
        if ($this->selectedCategory) {
            $query->where('menu_category_id', $this->selectedCategory);
        }

        // Search functionality
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('ingredients', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Featured items filter
        if ($this->showFeaturedOnly) {
            $query->where('is_featured', true);
        }

        // Price range filter
        $query->whereBetween('price', $this->priceRange);

        // Sorting
        switch ($this->sortBy) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('sort_order');
                break;
            default:
                $query->orderBy('sort_order')->orderBy('name');
        }

        return $query->paginate(12);
    }

    public function getCategories()
    {
        return MenuCategory::where('is_active', true)
            ->withCount('menuItems')
            ->orderBy('sort_order')
            ->get();
    }

    public function render()
    {
        return view('livewire.menu-display', [
            'menuItems' => $this->getMenuItems(),
            'categories' => $this->getCategories(),
        ]);
    }
}