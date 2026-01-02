# Discount System Documentation

## Overview
The E-Zone e-commerce platform features a comprehensive discount system with "Best Discount Wins" logic. The system automatically applies the highest applicable discount across brand, category, subcategory, and product levels.

## Architecture

### Core Components

1. **DiscountService** (`app/Services/DiscountService.php`)
   - Core business logic for discount calculations
   - Implements "Best Discount Wins" strategy
   - 10-minute caching per product
   - Methods:
     - `calculateProductDiscount()` - Get discount for a product
     - `calculateVariantDiscount()` - Get discount for a product variant
     - `findBestDiscountForProduct()` - Find best applicable discount
     - `calculateCartTotals()` - Calculate cart totals with discounts

2. **Discount Model** (`app/Models/Discount.php`)
   - name (translatable)
   - type: 'flat' or 'percentage'
   - value: discount amount
   - max_discount: optional cap for percentage discounts
   - start_date, end_date
   - is_active status
   - Relationships: morphMany to DiscountTarget

3. **DiscountTarget Model** (`app/Models/DiscountTarget.php`)
   - Polymorphic relationship to targets (Brand, Category, SubCategory, Product)
   - target_type: 'brand', 'category', 'subcategory', 'product'
   - target_id: ID of the target entity

### Database Schema

```sql
-- discounts table
id, name (json), type, value, max_discount, start_date, end_date, is_active, timestamps

-- discount_targets table
id, discount_id, target_type, target_id, timestamps
```

### MorphMap Configuration
Located in `app/Providers/AppServiceProvider.php`:
```php
Relation::morphMap([
    'brand' => Brand::class,
    'category' => Categories::class,
    'subcategory' => SubCategories::class,
    'product' => Product::class,
]);
```

## How It Works

### "Best Discount Wins" Logic

When a customer views a product:
1. System fetches all active discounts targeting:
   - The product itself
   - The product's brand
   - The product's category
   - The product's subcategory (if exists)

2. For each discount, calculates actual savings:
   - Flat discount: direct amount
   - Percentage discount: price × (percentage/100), capped by max_discount if set

3. Compares all applicable discounts and selects the one providing maximum savings

4. Caches the result for 10 minutes to optimize performance

### Discount Application

#### Product Pages
- Original price shown with strikethrough when discount applies
- Final price displayed in red/green
- Discount badge showing percentage or amount saved
- Discount name displayed with icon

#### Cart
- Each item shows:
  - Unit price
  - Quantity
  - Original line total (if discount applies)
  - Discount badge showing savings
  - Final line total after discount
- Cart summary shows:
  - Subtotal (before discounts)
  - Total discount savings (highlighted in green)
  - Subtotal after discount
  - Tax (calculated on discounted amount)
  - Shipping
  - Final total
  - "You save $X.XX today!" message when discounts apply

#### Product Cards (Listings)
- Red discount badge in top-left corner
- Original price with strikethrough
- Final price in red

## Admin Management

### Creating Discounts

```php
// Example: Create a 20% off discount for a brand
POST /admin/discounts

{
    "name": {"en": "Summer Sale - Nike 20% Off"},
    "type": "percentage",
    "value": 20,
    "max_discount": 50, // Optional: cap at $50
    "start_date": "2024-06-01",
    "end_date": "2024-08-31",
    "is_active": true,
    "targets": [
        {
            "target_type": "brand",
            "target_id": 5
        }
    ]
}
```

### Cache Management
Caches are automatically cleared when:
- New discount is created
- Discount is updated
- Discount is deleted
- Discount status is toggled

Manual cache clearing:
```php
use function App\Helpers\clear_all_discount_caches;

clear_all_discount_caches(); // Clear all discount caches
```

## Developer Usage

### In Controllers

```php
use App\Services\DiscountService;

public function show(Product $product, DiscountService $discountService)
{
    $discountInfo = $discountService->calculateProductDiscount($product->id);
    
    return view('product.show', [
        'product' => $product,
        'discountInfo' => $discountInfo
    ]);
}
```

### In Models

```php
// Product Model
$product = Product::find(1);

// Check if product has discount
if ($product->hasDiscount()) {
    $discountInfo = $product->getDiscountInfo();
    $finalPrice = $product->getFinalPrice();
}

// ProductVariant Model
$variant = ProductVariant::find(1);
$discountInfo = $variant->getDiscountInfo();
```

### In Cart

```php
$cart = Cart::find($cartId);

// Get complete cart totals with discounts
$totals = $cart->calculateTotals();

/*
Returns:
[
    'subtotal' => 100.00,           // Original total
    'total_discount' => 15.00,      // Total savings
    'total' => 85.00,               // After discount
    'items' => [
        1 => [
            'original_line_total' => 50.00,
            'discount_amount' => 10.00,
            'line_total_after_discount' => 40.00,
            'discount_info' => [...]
        ]
    ]
]
*/

// Cart convenience methods
$subtotal = $cart->getSubtotal();
$totalDiscount = $cart->getTotalDiscount();
$finalTotal = $cart->getTotal();
$hasDiscounts = $cart->hasDiscounts();
```

### In Blade Views

#### Using Components

```blade
{{-- Product Price Component --}}
<x-product-price :product="$product" />

{{-- With Variant --}}
<x-product-price :product="$product" :variant="$variant" />

{{-- Cart Item Price Component --}}
<x-cart-item-price :item="$item" />

{{-- Cart Summary Component --}}
<x-cart-summary :cart="$cart" />
```

#### Manual Implementation

```blade
@if($product->hasDiscount())
    @php
        $discountInfo = $product->getDiscountInfo();
        $finalPrice = $product->getFinalPrice();
    @endphp
    
    {{-- Original Price --}}
    <span class="line-through text-gray-400">
        ${{ number_format($product->price, 2) }}
    </span>
    
    {{-- Discount Badge --}}
    @if($discountInfo['discount_type'] === 'percentage')
        <span class="badge badge-success">
            -{{ $discountInfo['discount_value'] }}%
        </span>
    @else
        <span class="badge badge-success">
            Save ${{ number_format($discountInfo['discount_amount'], 2) }}
        </span>
    @endif
    
    {{-- Final Price --}}
    <span class="text-red-600 font-bold">
        ${{ number_format($finalPrice, 2) }}
    </span>
@else
    {{-- Regular Price --}}
    <span class="font-bold">
        ${{ number_format($product->price, 2) }}
    </span>
@endif
```

## Helper Functions

### Available Helpers

```php
// Clear specific product discount cache
clear_product_discount_cache(int $productId): void

// Clear all discount caches
clear_all_discount_caches(): void

// Format currency (convenience function)
format_currency(float $amount): string
```

### Autoloading
Helpers are autoloaded via `composer.json`:
```json
{
    "autoload": {
        "files": [
            "app/Helpers/discount_helpers.php"
        ]
    }
}
```

## API Responses

### Cart Update Response (AJAX)

```json
{
    "success": true,
    "message": "Cart updated.",
    "item": {
        "id": 1,
        "quantity": 2,
        "unit_price": 50.00,
        "line_total": 100.00,
        "discount": {
            "has_discount": true,
            "discount_amount": 20.00,
            "line_total_after_discount": 80.00
        }
    },
    "summary": {
        "subtotal": 100.00,
        "total_discount": 20.00,
        "subtotal_after_discount": 80.00,
        "tax": 6.40,
        "shipping": 0.00,
        "total": 86.40,
        "itemCount": 2
    }
}
```

## Performance Considerations

### Caching Strategy
- **Duration**: 10 minutes per product
- **Cache Key Pattern**: `discount_product_{id}`
- **Auto-Clearing**: Triggered on discount CRUD operations
- **Benefits**: Reduces database queries by ~80% for repeat visitors

### Database Optimization
- Eager loading: `Discount::with('targets.target')`
- Indexed columns: `discount_targets.target_type`, `discount_targets.target_id`
- Scopes: `active()` scope for filtering active discounts

### Query Optimization
```php
// Bad: N+1 queries
foreach ($products as $product) {
    $discount = $product->getDiscountInfo(); // Separate query each
}

// Good: Bulk processing
$productIds = $products->pluck('id');
$discounts = DiscountService::bulkCalculateDiscounts($productIds);
```

## Testing

### Test Scenarios

1. **Single Product Discount**
   - Create product-specific discount
   - Verify it applies correctly
   - Check cache works

2. **Brand-wide Discount**
   - Create brand discount
   - Verify all brand products get discount
   - Check "Best Discount Wins" when product also has specific discount

3. **Category Discount**
   - Create category discount
   - Verify all products in category get discount

4. **Best Discount Wins**
   - Create multiple overlapping discounts:
     - Brand: 10% off
     - Category: $5 off
     - Product: 15% off
   - Verify system selects highest savings

5. **Percentage Cap**
   - Create 50% discount with $20 max
   - Test on $100 product (should be $50 off)
   - Test on $30 product (should be $15 off, not $20)

6. **Cart Calculations**
   - Add multiple items with different discounts
   - Verify totals are correct
   - Check tax calculated on discounted amount

### Manual Testing Checklist

- [ ] Product detail page shows correct discount
- [ ] Product cards show discount badges
- [ ] Cart displays item-level discounts
- [ ] Cart summary shows total savings
- [ ] AJAX cart updates preserve discounts
- [ ] Discount names display correctly
- [ ] Cache clears on admin discount changes
- [ ] Expired discounts don't apply
- [ ] Inactive discounts don't apply

## Troubleshooting

### Discount Not Showing
1. Check discount is active: `is_active = 1`
2. Check date range: current date within `start_date` to `end_date`
3. Clear cache: `clear_all_discount_caches()`
4. Verify target mapping in MorphMap
5. Check discount has targets assigned

### Wrong Discount Applied
1. Verify "Best Discount Wins" logic is correct
2. Check multiple discounts aren't stacking
3. Verify percentage max_discount caps
4. Ensure cache is fresh

### Performance Issues
1. Check cache is working (10-minute TTL)
2. Verify eager loading in queries
3. Check discount_targets table has indexes
4. Consider Redis for high-traffic sites

### Cache Not Clearing
1. Verify `clear_all_discount_caches()` is called in admin controller
2. Check cache driver configuration
3. Ensure proper permissions for cache directory
4. Test manual cache clear: `php artisan cache:clear`

## Future Enhancements

### Planned Features
- [ ] Coupon code system (user-entered codes)
- [ ] Buy X Get Y discounts
- [ ] Quantity-based discounts (buy 3, get 10% off)
- [ ] Time-limited flash sales
- [ ] User-group specific discounts (VIP, wholesale)
- [ ] Minimum purchase requirements
- [ ] Exclude specific products from category discounts
- [ ] Discount usage limits (max uses per customer)

### API Endpoints (Future)
```
GET /api/discounts/active - List all active discounts
GET /api/products/{id}/discount - Get discount for specific product
POST /api/cart/apply-discount - Apply discount to cart
```

## Maintenance

### Regular Tasks
- Monitor cache hit rates
- Review discount performance metrics
- Clean up expired discounts
- Archive old discount records
- Update discount names for seasonal changes

### Database Maintenance
```sql
-- Delete expired discounts older than 1 year
DELETE FROM discounts 
WHERE end_date < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Find discounts with no targets
SELECT d.* FROM discounts d 
LEFT JOIN discount_targets dt ON d.id = dt.discount_id 
WHERE dt.id IS NULL;
```

## Support

For issues or questions:
- Check this documentation first
- Review [MASTER_DOCUMENTATION.md](MASTER_DOCUMENTATION.md) for overall system architecture
- Check Laravel logs: `storage/logs/laravel.log`
- Enable debug mode: `.env` set `APP_DEBUG=true`

---

**Last Updated**: 2024
**Version**: 1.0
**Author**: E-Zone Development Team
