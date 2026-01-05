document.addEventListener('DOMContentLoaded', function() {
    // Helper function to format price with commas
    function formatPrice(price) {
        return parseFloat(price).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Function to update price display with discount information
    function updatePriceDisplay(discountInfo) {
        if (!discountInfo) return;

        const priceContainer = document.querySelector('.bg-gradient-to-r.from-primary\\/5');
        if (!priceContainer) return;

        const priceEl = document.getElementById('product-price');
        if (!priceEl) return;

        // Get the parent flex container
        const flexContainer = priceEl.closest('.flex.items-baseline');
        if (!flexContainer) return;

        // Get or create price wrapper
        let priceWrapper = flexContainer.querySelector('.flex-col, .flex.flex-col');
        if (!priceWrapper) {
            // If no wrapper exists, create one and wrap the price element
            priceWrapper = document.createElement('div');
            priceWrapper.className = 'flex flex-col gap-2';
            priceEl.parentNode.insertBefore(priceWrapper, priceEl);
            priceWrapper.appendChild(priceEl);
        }

        if (discountInfo.has_discount) {
            // Show discounted price
            priceWrapper.innerHTML = `
                <span class="text-2xl text-gray-400 line-through">$${formatPrice(discountInfo.original_price)}</span>
                <span id="product-price" class="text-4xl font-bold text-red-600">$${formatPrice(discountInfo.final_price)}</span>
                ${discountInfo.discount_percentage > 0 ? `
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-red-500 text-white text-sm font-bold self-start">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                    Save ${discountInfo.discount_percentage.toFixed(0)}%
                </span>` : ''}
            `;

            // Show or update discount badge
            let discountBadge = priceContainer.querySelector('.bg-green-50');
            if (!discountBadge) {
                const discountBadgeContainer = priceContainer.querySelector('.flex.items-center.justify-between');
                if (discountBadgeContainer && discountBadgeContainer.parentElement) {
                    discountBadge = document.createElement('div');
                    discountBadge.className = 'mt-4 flex items-center gap-2 px-4 py-3 bg-green-50 rounded-lg border border-green-200';
                    discountBadgeContainer.parentElement.appendChild(discountBadge);
                }
            }
            if (discountBadge && discountInfo.discount_name) {
                discountBadge.innerHTML = `
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-semibold text-green-800">${discountInfo.discount_name} applied!</span>
                `;
            }
        } else {
            // Show regular price
            // --- FIX START ---
            priceWrapper.innerHTML = `
                <span id="product-price" class="text-4xl font-bold text-primary">$${formatPrice(discountInfo.final_price)}</span>
            `;
            // --- FIX END ---

            // Remove discount badge if exists
            const discountBadge = priceContainer.querySelector('.bg-green-50');
            if (discountBadge) {
                discountBadge.remove();
            }
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

    if (qtyDecrease && qtyIncrease && qtyInput) {
        qtyDecrease.addEventListener('click', () => {
            let v = parseInt(qtyInput.value || '1', 10);
            if (v > 1) qtyInput.value = v - 1;
        });
        qtyIncrease.addEventListener('click', () => {
            let v = parseInt(qtyInput.value || '1', 10);
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
                updatePriceDisplay(discountInfo);
            } catch (e) {
                console.error('Error parsing discount data:', e);
                // Fallback to simple price display
                if (priceEl && price) {
                    priceEl.textContent = '$' + formatPrice(price);
                }
            }
        } else {
            // No discount data, show regular price
            if (priceEl && price) {
                const priceContainer = document.querySelector('.bg-gradient-to-r.from-primary\\/5');
                const flexContainer = priceEl.closest('.flex.items-baseline');
                if (flexContainer) {
                    let priceWrapper = flexContainer.querySelector('.flex-col, .flex.flex-col');
                    if (priceWrapper) {
                        priceWrapper.innerHTML = `<span id="product-price" class="text-4xl font-bold text-primary">$${formatPrice(price)}</span>`;
                    } else {
                        priceEl.textContent = '$' + formatPrice(price);
                    }
                }

                // Remove discount badge if exists
                if (priceContainer) {
                    const discountBadge = priceContainer.querySelector('.bg-green-50');
                    if (discountBadge) {
                        discountBadge.remove();
                    }
                }
            }
        }

        if (skuEl && sku) {
            skuEl.textContent = 'SKU: ' + sku;
        }

        const productTitleEl = document.getElementById('product-title');
        if (productTitleEl) {
            const baseName = productTitleEl.getAttribute('data-base-name') || productTitleEl.textContent;
            productTitleEl.textContent = variantName ? baseName + ' - ' + variantName : baseName;
        }

        if (qtyInput) {
            if (qty > 0) {
                qtyInput.removeAttribute('disabled');
                qtyInput.value = '1';
            } else {
                qtyInput.value = '0';
                qtyInput.setAttribute('disabled', 'disabled');
            }
        }
        if (addToCartBtn) {
            if (qty > 0) {
                addToCartBtn.removeAttribute('disabled');
                addToCartBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                addToCartBtn.setAttribute('disabled', 'disabled');
                addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        if (buyNowBtn) {
            if (qty > 0) {
                buyNowBtn.removeAttribute('disabled');
                buyNowBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                buyNowBtn.setAttribute('disabled', 'disabled');
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
                                const k = item.title || ('Spec ' + (idx + 1));
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
});
