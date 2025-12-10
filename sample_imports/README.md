# Product Import Sample Files

## Available Sample Files

### 1. **products_sample_basic.csv**
- Contains 6 basic products with 1-2 variants each
- Good for testing basic import functionality
- Mix of cameras, lenses, and lighting equipment
- File size: Small (~1KB)

### 2. **products_sample_with_multiple_variants.csv**
- Contains 5 products with 3 variants each
- Tests handling of multiple variant columns
- Includes smartphones, laptops, and headphones
- File size: Small (~1KB)

### 3. **products_sample_no_variants.csv**
- Contains 6 products without any variants
- Tests single-SKU products (accessories, memory cards)
- Simpler structure for validation
- File size: Small (~1KB)

### 4. **products_sample_large_test.csv**
- Contains 20 products for performance testing
- Mix of products with and without variants
- Tests import speed and progress tracking
- File size: Medium (~3KB)

## Column Structure

### Required Columns:
- `name_en` - Product name in English (required)
- `brand` - Brand name (must exist in system)
- `category` - Category name (must exist in system)
- `price` - Base product price
- `description_en` - Product description in English

### Optional Columns:
- `sub_category` - Sub-category name (must exist if provided)
- `warranty_en` - Warranty information in English
- `dimensions_en` - Product dimensions in English
- `variant_1_name_en`, `variant_1_price` - First variant
- `variant_2_name_en`, `variant_2_price` - Second variant
- `variant_3_name_en`, `variant_3_price` - Third variant (and so on...)
- `spec_1_title`, `spec_1_value` - First specification
- `spec_2_title`, `spec_2_value` - Second specification (and so on...)

## Prerequisites

Before importing, make sure these brands, categories, and sub-categories exist in your system:

### Brands:
- Sony
- Canon
- Nikon
- Apple
- Samsung
- Dell
- Godox
- Peak Design
- Manfrotto
- SanDisk
- Rode
- DJI
- Lowepro
- Fujifilm
- Panasonic
- Sigma
- Tamron
- Profoto
- Aputure
- Western Digital
- Blackmagic Design
- RED
- ARRI

### Categories:
- Cameras
- Lenses
- Lighting
- Audio
- Storage
- Accessories
- Computers
- Smartphones
- Drones

### Sub-Categories:
- Mirrorless (under Cameras)
- Cinema (under Cameras)
- Standard Zoom (under Lenses)
- Telephoto (under Lenses)
- Prime (under Lenses)
- Wide Angle (under Lenses)
- Zoom (under Lenses)
- Speedlights (under Lighting)
- LED Lights (under Lighting)
- Headphones (under Audio)
- Microphones (under Audio)
- Memory Cards (under Storage)
- External Drives (under Storage)
- Bags (under Accessories)
- Tripods (under Accessories)
- Gimbals (under Accessories)
- Laptops (under Computers)
- Flagship (under Smartphones)
- Consumer (under Drones)

## Usage

1. **Choose a sample file** based on your testing needs
2. **Verify prerequisites** - Make sure all brands, categories, and sub-categories exist
3. **Upload the file** through the admin product import interface
4. **Select translation option** - Check "Skip Auto-Translation" for faster import if you don't need translations
5. **Monitor progress** - Watch the import progress notification
6. **Review results** - Check the detailed import report

## Testing Scenarios

### Basic Functionality Test
Use: `products_sample_basic.csv`
- Tests standard product import with variants
- Validates specification handling
- Quick test for general functionality

### Multiple Variants Test
Use: `products_sample_with_multiple_variants.csv`
- Tests handling of 3 variants per product
- Complex variant structures
- Good for UI variant display testing

### Single-SKU Products Test
Use: `products_sample_no_variants.csv`
- Tests products without variants
- Simpler structure validation
- Accessories and consumables

### Performance Test
Use: `products_sample_large_test.csv`
- Tests import speed with 20 products
- Progress tracking validation
- Memory usage monitoring

## Expected Import Times

| File | Products | With Translation | Without Translation |
|------|----------|------------------|---------------------|
| Basic | 6 | ~30-60 seconds | ~5-10 seconds |
| Multiple Variants | 5 | ~25-50 seconds | ~5-8 seconds |
| No Variants | 6 | ~30-60 seconds | ~5-10 seconds |
| Large Test | 20 | ~2-4 minutes | ~20-40 seconds |

*Times may vary based on server performance and Google Translate API response times*

## Tips

1. **Start with small files** to verify your setup
2. **Check logs** if imports fail - logs contain detailed error messages
3. **Use "Skip Translation"** for faster testing during development
4. **Monitor memory usage** with large files (100+ products)
5. **Validate data** before import - correct brand/category names are crucial

## Troubleshooting

### Common Issues:

**"Brand not found"**
- Solution: Create the brand in the admin panel first

**"Category not found"**
- Solution: Create the category in the admin panel first

**"Product already exists"**
- Solution: Delete the existing product or use a different name

**"Duplicate SKU error"**
- Solution: This should be handled automatically, but if you see it, check for soft-deleted products with the same SKU

**Import seems stuck**
- Solution: Check the queue worker is running (`php artisan queue:work`)
- Check server logs for errors
- Monitor the import progress endpoint

## Creating Custom Test Files

You can create your own CSV files with the same structure. Make sure to:
1. Use UTF-8 encoding
2. Include all required columns
3. Use existing brand/category names
4. Follow the price format (decimal with 2 places)
5. Keep variant and spec columns sequential (variant_1, variant_2, etc.)
