# Discount System - Quick Start Guide

## For Admins

### Creating a Discount

1. Go to Admin Panel → Discounts → Create New
2. Fill in the form:
   - **Name**: Give your discount a name (e.g., "Summer Sale 2024")
   - **Type**: Choose "Percentage" or "Flat"
   - **Value**: Enter the discount value (e.g., 20 for 20% or 10 for $10)
   - **Max Discount** (optional): For percentage discounts, set a maximum cap
   - **Start Date**: When the discount begins
   - **End Date**: When the discount expires
   - **Active**: Toggle to enable/disable
   
3. Add targets:
   - Click "Add Target"
   - Select target type: Brand, Category, Subcategory, or Product
   - Select the specific item
   - You can add multiple targets to one discount

4. Save the discount

### Examples

**20% off all Nike products:**
- Type: Percentage
- Value: 20
- Max Discount: 50 (optional - caps at $50 savings)
- Target Type: Brand
- Target: Nike

**$10 off Electronics category:**
- Type: Flat
- Value: 10
- Target Type: Category
- Target: Electronics

**15% off specific product:**
- Type: Percentage
- Value: 15
- Target Type: Product
- Target: [Select Product]

## For Developers

### Display Discount in Product View

```blade
{{-- Option 1: Use Component (Recommended) --}}
<x-product-price :product="$product" />

{{-- Option 2: Manual --}}
@if($product->hasDiscount())
    @php $discountInfo = $product->getDiscountInfo(); @endphp
    <div class="price">
        <span class="original">${{ number_format($product->price, 2) }}</span>
        <span class="final">${{ number_format($product->getFinalPrice(), 2) }}</span>
        <span class="badge">Save {{ $discountInfo['discount_type'] === 'percentage' ? $discountInfo['discount_value'].'%' : '$'.$discountInfo['discount_amount'] }}</span>
    </div>
@else
    <span class="price">${{ number_format($product->price, 2) }}</span>
@endif
```

### Get Discount Info in Controller

```php
use App\Services\DiscountService;

public function show(Product $product, DiscountService $discountService)
{
    // Get discount info
    $discountInfo = $discountService->calculateProductDiscount($product->id);
    
    // Or use model method
    $discountInfo = $product->getDiscountInfo();
    
    return view('product.show', compact('product', 'discountInfo'));
}
```

### Calculate Cart Totals with Discounts

```php
$cart = Cart::find($cartId);
$totals = $cart->calculateTotals();

// Returns:
[
    'subtotal' => 100.00,        // Original
    'total_discount' => 15.00,   // Savings
    'total' => 85.00             // Final
]
```

### Clear Cache After Changes

```php
use function App\Helpers\clear_all_discount_caches;

// After updating discounts programmatically
clear_all_discount_caches();
```

## Understanding "Best Discount Wins"

When multiple discounts apply to a product, the system automatically selects the one that saves the customer the most money.

### Example Scenario

Product: Nike Air Max - $100

Available Discounts:
1. Brand (Nike): 10% off = $10 savings
2. Category (Shoes): $8 off = $8 savings  
3. Product-specific: 15% off = $15 savings ✅ **WINNER**

The system will apply the 15% product-specific discount because it provides the highest savings.

## Common Use Cases

### Flash Sale
```
Type: Percentage
Value: 30
Start: 2024-06-15 00:00:00
End: 2024-06-15 23:59:59
Target: Category → All Products
```

### Clearance on Specific Items
```
Type: Flat
Value: 25
Start: Now
End: 30 days from now
Target: Multiple Products (add each individually)
```

### Seasonal Brand Promotion
```
Type: Percentage
Value: 20
Max Discount: 100
Start: 2024-06-01
End: 2024-08-31
Target: Brand → Selected Brand
```

### New Product Launch Discount
```
Type: Percentage
Value: 10
Start: Launch Date
End: +14 days
Target: Product → New Product
```

## Troubleshooting Quick Fixes

### Discount Not Showing?
```php
// 1. Check if discount is active
SELECT * FROM discounts WHERE id = X;

// 2. Check if discount has targets
SELECT * FROM discount_targets WHERE discount_id = X;

// 3. Clear cache
php artisan cache:clear

// Or programmatically:
clear_all_discount_caches();

// 4. Check date range
// Ensure current date is between start_date and end_date
```

### Wrong Discount Applied?
- Check all active discounts for that product
- Verify "Best Discount Wins" calculation
- Check if percentage max_discount is limiting savings

### Cache Issues?
```bash
# Clear all cache
php artisan cache:clear

# Clear specific product cache (in PHP)
clear_product_discount_cache($productId);
```

## Best Practices

### DO:
✅ Use descriptive discount names  
✅ Set realistic date ranges  
✅ Test discounts before going live  
✅ Use max_discount caps for high-value items  
✅ Archive expired discounts regularly  

### DON'T:
❌ Create overlapping discounts unless intentional  
❌ Set percentage values > 100  
❌ Forget to set end dates (discounts run forever)  
❌ Leave inactive discounts with is_active = true  
❌ Delete discounts with active orders  

## Performance Tips

1. **Cache is your friend**: Discounts are cached for 10 minutes
2. **Bulk operations**: When possible, update multiple products at once
3. **Clean up**: Remove expired discounts older than 1 year
4. **Monitor**: Check cache hit rates in production

## Testing Checklist

Before launching a discount campaign:

- [ ] Verify discount shows on product page
- [ ] Check discount badge appears on product cards
- [ ] Confirm cart calculates correctly
- [ ] Test with multiple items
- [ ] Verify AJAX updates work
- [ ] Check mobile display
- [ ] Confirm email notifications (if applicable)
- [ ] Test edge cases (0.01 products, high-value items)

## Need Help?

- 📖 Full Documentation: `docs/DISCOUNT_SYSTEM.md`
- 🔧 Admin Panel: `/admin/discounts`
- 📧 Support: [Your Support Email]

---

**Quick Reference Card**

| Action | Code |
|--------|------|
| Check if product has discount | `$product->hasDiscount()` |
| Get discount info | `$product->getDiscountInfo()` |
| Get final price | `$product->getFinalPrice()` |
| Calculate cart totals | `$cart->calculateTotals()` |
| Clear cache | `clear_all_discount_caches()` |
| Show price component | `<x-product-price :product="$product" />` |

