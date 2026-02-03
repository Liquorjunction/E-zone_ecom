document.addEventListener('DOMContentLoaded', function() {
    // Helper function to format price with commas
    function formatPrice(price) {
        return parseFloat(price).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function showStockLimitMessage(max) {
        if (!max) return;
        const message = max === 1
            ? 'Only 1 item available in stock.'
            : `Only ${max} items available in stock.`;

        if (window.Toast && typeof window.Toast.info === 'function') {
            window.Toast.info({
                title: 'Stock limit',
                message: message,
            });
            return;
        }

        let banner = document.getElementById('stockLimitBanner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'stockLimitBanner';
            banner.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4 bg-amber-500 text-white px-4 py-3 rounded-xl shadow-xl';
            document.body.appendChild(banner);
        }
        banner.textContent = message;
        banner.style.opacity = '1';
        banner.style.transform = 'translate(-50%, 0)';

        clearTimeout(banner._hideTimer);
        banner._hideTimer = setTimeout(() => {
            banner.style.opacity = '0';
            banner.style.transform = 'translate(-50%, -100%)';
            setTimeout(() => banner.remove(), 300);
        }, 2500);
    }

    // Function to update price display with discount information
    function updatePriceDisplay(discountInfo) {
        if (!discountInfo) return;

        const priceContainer = document.querySelector('.bg-gradient-to-r.from-primary\\/5');
        if (!priceContainer) return;

        // Find the inner flex container (items-baseline) that holds the price
        const flexContainer = priceContainer.querySelector('.flex.items-baseline');
        if (!flexContainer) return;

        // Find existing price wrapper (flex-col div), or create one
        let priceWrapper = flexContainer.querySelector('.flex.flex-col');

        if (!priceWrapper) {
            // Create price wrapper if it doesn't exist
            priceWrapper = document.createElement('div');
            priceWrapper.className = 'flex flex-col gap-2';
            // Insert it at the beginning of flexContainer
            flexContainer.insertBefore(priceWrapper, flexContainer.firstChild);
        }

        // Determine if there's an actual discount
        const originalPrice = parseFloat(discountInfo.original_price) || 0;
        const finalPrice = parseFloat(discountInfo.final_price) || 0;
        const hasActualDiscount = discountInfo.has_discount === true && originalPrice > finalPrice;
        const discountPercentage = parseFloat(discountInfo.discount_percentage) || 0;

        console.log('Discount Info:', {
            has_discount: discountInfo.has_discount,
            original_price: originalPrice,
            final_price: finalPrice,
            hasActualDiscount: hasActualDiscount,
            discount_percentage: discountPercentage,
            discount_name: discountInfo.discount_name
        });

        // Now update only the price wrapper content
        if (hasActualDiscount) {
            // Show discounted price
            priceWrapper.innerHTML = `
                <div class="text-2xl text-gray-400">
                    <span class="line-through">${window.CURRENCY_SYMBOL} ${formatPrice(originalPrice)}</span>
                </div>
                <span class="text-4xl font-bold text-red-600">${window.CURRENCY_SYMBOL} ${formatPrice(finalPrice)}</span>
                ${discountPercentage > 0 ? `
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-red-500 text-white text-sm font-bold self-start">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                    Save ${discountPercentage.toFixed(0)}%
                </span>` : ''}
            `;
        } else {
            // Show regular price (no discount)
            priceWrapper.innerHTML = `
                <span class="text-4xl font-bold text-primary">${window.CURRENCY_SYMBOL} ${formatPrice(finalPrice)}</span>
            `;
        }

        // Handle discount badge (separate from price area)
        let discountBadge = priceContainer.querySelector('.mt-4.flex.items-center.gap-2.px-4.py-3.bg-green-50');
        if (hasActualDiscount && discountInfo.discount_name) {
            if (!discountBadge) {
                discountBadge = document.createElement('div');
                discountBadge.className = 'mt-4 flex items-center gap-2 px-4 py-3 bg-green-50 rounded-lg border border-green-200';
                priceContainer.appendChild(discountBadge);
            }
            discountBadge.innerHTML = `
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-semibold text-green-800">${discountInfo.discount_name} applied!</span>
            `;
        } else if (discountBadge) {
            // Remove discount badge if no discount
            discountBadge.remove();
        }
    }

    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;

            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-primary', 'text-gray-900', 'border-b-3');
                btn.classList.add('text-gray-600');
            });

            button.classList.add('active', 'border-primary', 'text-gray-900', 'border-b-3');
            button.classList.remove('text-gray-600');

            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });

            const targetContent = document.getElementById(targetTab + '-tab');
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('active');
            }
        });
    });

    const thumbButtons = document.querySelectorAll('.thumb-button');
    const mainImage = document.getElementById('main-product-image');
    thumbButtons.forEach(btn => {
        button = btn; // Scope fix if strictly needed, but 'btn' usage below is fine
        btn.addEventListener('click', () => {
            const src = btn.getAttribute('data-image');
            if (src && mainImage) {
                mainImage.src = src;
                const title = document.getElementById('product-title')?.textContent || '';
                mainImage.alt = title;
            }
            thumbButtons.forEach(t => {
                t.classList.remove('ring-2', 'ring-primary/30', 'border-primary');
                t.classList.add('border-gray-200');
            });
            btn.classList.add('ring-2', 'ring-primary/30', 'border-primary');
            btn.classList.remove('border-gray-200');
        });
    });

    const variantButtons = document.querySelectorAll('.variant-button');
    const priceEl = document.getElementById('product-price');
    const skuEl = document.getElementById('product-sku');
    const qtyInput = document.getElementById('cart-quantity');
    const qtyDecrease = document.getElementById('qty-decrease');
    const qtyIncrease = document.getElementById('qty-increase');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');
    let currentMaxStock = null;

    function setMaxStock(value) {
        const parsed = parseInt(value || '', 10);
        if (!Number.isNaN(parsed) && parsed > 0) {
            currentMaxStock = parsed;
            if (qtyInput) qtyInput.setAttribute('max', String(parsed));
        } else {
            currentMaxStock = null;
            if (qtyInput) qtyInput.removeAttribute('max');
        }
    }

    function getMaxStock() {
        return currentMaxStock && currentMaxStock > 0 ? currentMaxStock : null;
    }

    if (qtyInput) {
        const initialMax = parseInt(qtyInput.getAttribute('max') || '', 10);
        if (!Number.isNaN(initialMax) && initialMax > 0) {
            setMaxStock(initialMax);
        }
    }

    if (qtyDecrease && qtyIncrease && qtyInput) {
        qtyDecrease.addEventListener('click', () => {
            let v = parseInt(qtyInput.value || '1', 10);
            if (v > 1) qtyInput.value = v - 1;
        });
        qtyIncrease.addEventListener('click', () => {
            let v = parseInt(qtyInput.value || '1', 10);
            const max = getMaxStock();
            if (max && v >= max) {
                qtyInput.value = String(max);
                showStockLimitMessage(max);
                return;
            }
            qtyInput.value = v + 1;
        });
    }

    function setActiveVariant(btn) {
        variantButtons.forEach(v => {
            v.classList.remove('ring-2', 'ring-primary/30', 'border-primary', 'shadow-md');
            v.classList.add('border-gray-200');
        });
        btn.classList.add('ring-2', 'ring-primary/30', 'border-primary', 'shadow-md');
        btn.classList.remove('border-gray-200');

        const price = btn.getAttribute('data-price');
        const sku = btn.getAttribute('data-sku');
        const qty = parseInt(btn.getAttribute('data-qty') || '0', 10);
        const variantImage = btn.getAttribute('data-image');
        const variantSlug = btn.getAttribute('data-slug');
        const variantName = btn.getAttribute('data-variant-name');
        const variantAttributes = btn.getAttribute('data-attributes');
        const variantId = btn.getAttribute('data-variant-id');
        const discountData = btn.getAttribute('data-discount');

        // Parse and update discount information
        let discountInfo = null;
        if (discountData) {
            try {
                discountInfo = JSON.parse(discountData);
                console.log('Raw Parsed Discount Data:', discountInfo);
                // Ensure has_discount is a boolean
                discountInfo.has_discount = discountInfo.has_discount === true || discountInfo.has_discount === 1 || discountInfo.has_discount === '1' || discountInfo.has_discount === 'true';
                console.log('Processed Discount Info:', discountInfo);
                updatePriceDisplay(discountInfo);
            } catch (e) {
                console.error('Error parsing discount data:', e, 'Raw data:', discountData);
                // Fallback: create a basic discount info object with just the price
                discountInfo = {
                    has_discount: false,
                    original_price: price,
                    final_price: price,
                    discount_percentage: 0,
                    discount_name: null
                };
                updatePriceDisplay(discountInfo);
            }
        } else {
            // No discount data, create empty discount info and show regular price
            discountInfo = {
                has_discount: false,
                original_price: price,
                final_price: price,
                discount_percentage: 0,
                discount_name: null
            };
            updatePriceDisplay(discountInfo);
        }

        if (skuEl && sku) {
            skuEl.textContent = 'SKU: ' + sku;
        }

        // Update the product price element directly
        if (priceEl) {
            const displayPrice = discountInfo?.final_price || price || 0;
            priceEl.textContent = window.CURRENCY_SYMBOL + ' ' + formatPrice(displayPrice);
        }

        const productTitleEl = document.getElementById('product-title');
        if (productTitleEl) {
            const baseName = productTitleEl.getAttribute('data-base-name') || productTitleEl.textContent;
            // Decode HTML entities in variantName if present
            let decodedVariantName = variantName;
            if (variantName && typeof variantName === 'string') {
                const txt = document.createElement('textarea');
                txt.innerHTML = variantName;
                decodedVariantName = txt.value;
            }
            productTitleEl.textContent = decodedVariantName ? baseName + ' - ' + decodedVariantName : baseName;
        }

        if (qtyInput) {
            if (qty > 0) {
            qtyInput.removeAttribute('hidden');
            setMaxStock(qty);
            qtyInput.value = '1';
            } else {
            qtyInput.value = '0';
            setMaxStock(null);
            qtyInput.setAttribute('hidden', 'hidden');
            }
        }
        if(qtyDecrease) {
            if (qty > 0) {
                qtyDecrease.removeAttribute('hidden');
            } else {
                qtyDecrease.setAttribute('hidden', 'hidden');
            }
        }
        if(qtyIncrease) {
            if (qty > 0) {
                qtyIncrease.removeAttribute('hidden');
            } else {
                qtyIncrease.setAttribute('hidden', 'hidden');
            }
        }

        if (addToCartBtn) {
            if (qty > 0) {
                addToCartBtn.removeAttribute('hidden');
                addToCartBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                addToCartBtn.setAttribute('hidden', 'hidden');
                addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        if (buyNowBtn) {
            if (qty > 0) {
                buyNowBtn.removeAttribute('hidden');
                buyNowBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                buyNowBtn.setAttribute('hidden', 'hidden');
                buyNowBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        window.currentSelectedVariantId = variantId ? String(variantId) : null;
        window.currentSelectedVariantSlug = variantSlug ? String(variantSlug) : null;

        const cartVariantInput = document.getElementById('cart-variant-id');
        if (cartVariantInput) cartVariantInput.value = variantId || '';

        if (variantImage && mainImage) {
            mainImage.src = variantImage;
            const title = document.getElementById('product-title')?.textContent || '';
            mainImage.alt = title;
        }

        try {
            const imagesJson = btn.getAttribute('data-images');
            const thumbs = document.querySelectorAll('.thumb-button');
            if (imagesJson && thumbs.length > 0) {
                const imagesList = JSON.parse(imagesJson);
                for (let i = 0; i < thumbs.length && i < imagesList.length; i++) {
                    const imgEl = thumbs[i].querySelector('img');
                    const src = imagesList[i];
                    if (src) {
                        thumbs[i].setAttribute('data-image', src);
                        if (imgEl) imgEl.src = src;
                    }
                }
                thumbButtons.forEach(t => {
                    t.classList.remove('ring-2', 'ring-primary/30', 'border-primary');
                    t.classList.add('border-gray-200');
                });
                if (thumbs[0]) {
                    thumbs[0].classList.add('ring-2', 'ring-primary/30', 'border-primary');
                    thumbs[0].classList.remove('border-gray-200');
                }
            }
        } catch (e) {}

        try {
            if (variantSlug) {
                const url = new URL(window.location.href);
                url.searchParams.set('variant', variantSlug);
                window.history.replaceState({}, '', url.toString());
            }
        } catch (e) {}

        renderVariantSpecs(variantAttributes);
    }

    if (variantButtons.length > 0) {
        variantButtons.forEach(btn => {
            btn.addEventListener('click', () => setActiveVariant(btn));
        });

        const specsRoot = document.getElementById('specifications-root');
        const productSpecsEl = document.getElementById('product-specs');
        const variantSpecsEl = document.getElementById('variant-specs');
        const productHasContent = productSpecsEl && productSpecsEl.innerHTML.trim().length > 20;

        function renderVariantSpecs(specData) {
            if (!specsRoot || !variantSpecsEl) return;
            try {
                if (!specData) {
                    variantSpecsEl.innerHTML = '';
                    return;
                }

                // decode HTML entities (Blade escapes JSON into attributes)
                function htmlDecode(input) {
                    const txt = document.createElement('textarea');
                    txt.innerHTML = input;
                    return txt.value;
                }

                if (typeof specData === 'string') {
                    // decode entities then try parsing JSON
                    const decoded = htmlDecode(specData.trim());
                    if (!decoded || decoded === 'null' || decoded === '[]' || decoded === '{}' || decoded === '""') {
                        variantSpecsEl.innerHTML = '';
                        return;
                    }
                    try {
                        specData = JSON.parse(decoded);
                    } catch (e) {
                        specData = decoded;
                    }
                }

                function renderRowsFromObject(obj) {
                    // Normalize many shapes into [{k,v},...]
                    function getLocaleText(v) {
                        if (typeof v === 'string') return v;
                        if (typeof v === 'object') return v[document.documentElement.lang] || v.en || JSON.stringify(v);
                        return String(v ?? '');
                    }

                    function rowsFromLocalizedArray(o) {
                        const lang = document.documentElement.lang || 'en';
                        let arr = null;
                        if (Array.isArray(o[lang])) arr = o[lang];
                        else if (Array.isArray(o.en)) arr = o.en;
                        else {
                            for (const v of Object.values(o)) {
                                if (Array.isArray(v)) {
                                    arr = v;
                                    break;
                                }
                            }
                        }
                        if (!arr) return null;
                        const pairs = [];
                        arr.forEach(item => {
                            if (item && typeof item === 'object') {
                                const k = item.key || item.title || item.label || '';
                                const v = item.value || item.description || '';
                                pairs.push({
                                    k: k || '',
                                    v: v
                                });
                            }
                        });
                        return pairs;
                    }

                    function normalize(spec) {
                        const pairs = [];
                        if (!spec && spec !== 0) return pairs;
                        if (typeof spec === 'string') {
                            // assume already parsed string handled elsewhere
                            pairs.push({
                                k: '',
                                v: spec
                            });
                            return pairs;
                        }
                        if (Array.isArray(spec)) {
                            spec.forEach((item, idx) => {
                                if (item && typeof item === 'object') {
                                    if ('key' in item && 'value' in item) {
                                        pairs.push({
                                            k: item.key || ('Spec ' + (idx + 1)),
                                            v: item.value
                                        });
                                    } else if ('title' in item && 'value' in item) {
                                        pairs.push({
                                            k: item.title,
                                            v: item.value
                                        });
                                    } else {
                                        // flatten object properties as separate rows
                                        Object.entries(item).forEach(([kk, vv]) => {
                                            const val = typeof vv === 'object' ? getLocaleText(vv) : vv;
                                            pairs.push({
                                                k: kk,
                                                v: val
                                            });
                                        });
                                    }
                                } else {
                                    pairs.push({
                                        k: 'Spec ' + (idx + 1),
                                        v: String(item)
                                    });
                                }
                            });
                            return pairs;
                        }
                        if (spec && typeof spec === 'object') {
                            // maybe localized arrays
                            const localized = rowsFromLocalizedArray(spec);
                            if (localized && localized.length) return localized;
                            // otherwise key->value map
                            Object.entries(spec).forEach(([k, v]) => {
                                const val = typeof v === 'object' ? getLocaleText(v) : v;
                                pairs.push({
                                    k: k,
                                    v: val
                                });
                            });
                            return pairs;
                        }
                        // fallback
                        pairs.push({
                            k: '',
                            v: String(spec)
                        });
                        return pairs;
                    }

                    const normalized = normalize(obj);
                    let rows = '';
                    normalized.forEach(p => {
                        // detect image URLs
                        const val = p.v ?? '';
                        const isImage = typeof val === 'string' && (val.match(/\.(jpg|jpeg|png|gif|webp)(\?|$)/i) || val.includes('/storage/') || val.startsWith('http'));
                        const display = isImage ? `<img src="${val}" class="w-24 h-24 object-contain" alt="${p.k || ''}">` : (val ?? '');
                        rows += `<tr class="py-2"><td class="py-3 text-gray-500 font-medium w-1/3 align-top">${p.k || ''}</td><td class="py-3 text-gray-900">${display}</td></tr>`;
                    });
                    return rows;
                }

                let variantHtml = '';
                if (Array.isArray(specData) && specData.length > 0) {
                    // produce table rows for array items
                    let rows = '';
                    specData.forEach((item, idx) => {
                        if (item && typeof item === 'object') {
                            if (item.title || item.value) {
                                const k = item.key;
                                const v = item.value || item.description || '';
                                rows += `<tr class="py-2"><td class="py-3 text-gray-500 font-medium w-1/3">${k}</td><td class="py-3 text-gray-900">${v}</td></tr>`;
                            } else {
                                rows += renderRowsFromObject(item);
                            }
                        } else if (item !== undefined && item !== null) {
                            rows += `<tr class="py-2"><td class="py-3 text-gray-500 font-medium w-1/3">Spec ${idx + 1}</td><td class="py-3 text-gray-900">${String(item)}</td></tr>`;
                        }
                    });
                    if (rows) {
                        variantHtml = `
                            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Variant Specifications</h4>
                                <table class="w-full text-sm"><tbody class="divide-y divide-gray-100">${rows}</tbody></table>
                            </div>
                        `;
                    }
                } else if (specData && typeof specData === 'object') {
                    const items = renderRowsFromObject(specData);
                    if (items) {
                        variantHtml = `
                            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Variant Specifications</h4>
                                <table class="w-full text-sm"><tbody class="divide-y divide-gray-100">${items}</tbody></table>
                            </div>
                        `;
                    }
                }

                if (variantHtml) {
                    // Auto-switch to Specifications tab when variant specs are present
                    try {
                        const specTabBtn = document.querySelector('.tab-button[data-tab="specifications"]');
                        if (specTabBtn) specTabBtn.click();
                    } catch (e) {}

                    // Render only into the variant container; product content remains untouched
                    variantSpecsEl.innerHTML = variantHtml;
                } else {
                    variantSpecsEl.innerHTML = '';
                }
            } catch (e) {
                variantSpecsEl.innerHTML = '';
            }
        }

        window.renderVariantSpecs = renderVariantSpecs;

        try {
            const url = new URL(window.location.href);
            const vSlug = url.searchParams.get('variant');
            if (vSlug) {
                const match = Array.from(variantButtons).find(b => b.getAttribute('data-slug') === vSlug);
                if (match) {
                    setActiveVariant(match);
                } else {
                    setActiveVariant(variantButtons[0]);
                }
            } else {
                setActiveVariant(variantButtons[0]);
            }
        } catch (e) {
            setActiveVariant(variantButtons[0]);
        }
    }

    // Handle Add-to-Cart form submission with AJAX
    const addToCartForm = document.getElementById('addToCartForm');
    let clickedButton = null;

    if (addToCartForm) {
        // Track which button was clicked
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const buyNowBtn = document.getElementById('buy-now-btn');

        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                clickedButton = 'add_to_cart';
            });
        }

        if (buyNowBtn) {
            buyNowBtn.addEventListener('click', function() {
                clickedButton = 'buy_now';
            });
        }

        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            // Check if buy_now button was clicked
            const isBuyNow = clickedButton === 'buy_now';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Dispatch cart:changed event to update header cart counter
                    if (data.summary && typeof data.summary.itemCount !== 'undefined') {
                        window.dispatchEvent(new CustomEvent('cart:changed', {
                            detail: {
                                summary: data.summary,
                                itemCount: data.summary.itemCount
                            }
                        }));
                    }

                    // Show success message
                    if (window.Toast && typeof window.Toast.success === 'function') {
                        window.Toast.success({
                            title: 'Success',
                            message: data.message || 'Item added to cart.',
                        });
                    } else {
                        alert(data.message || 'Item added to cart.');
                    }

                    // Redirect if buy_now
                    if (isBuyNow) {
                        setTimeout(() => {
                            window.location.href = window.CHECKOUT_URL || '/checkout?step=2';
                        }, 500);
                    }
                } else {
                    if (window.Toast && typeof window.Toast.error === 'function') {
                        window.Toast.error({
                            title: 'Error',
                            message: data.message || 'Could not add item to cart.',
                        });
                    } else {
                        alert(data.message || 'Could not add item to cart.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.Toast && typeof window.Toast.error === 'function') {
                    window.Toast.error({
                        title: 'Error',
                        message: 'An error occurred. Please try again.',
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    }
});
