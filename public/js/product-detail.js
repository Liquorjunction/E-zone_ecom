document.addEventListener("DOMContentLoaded", function () {
    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────
    function formatPrice(price) {
        return parseFloat(price).toLocaleString("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function showStockLimitMessage(max) {
        if (!max) return;
        const message =
            max === 1
                ? trans('Only 1 item available in stock.')
                : `(${trans('Only')}) ${max} ${trans('items available in stock.')}`;

        if (window.Toast && typeof window.Toast.info === "function") {
            window.Toast.info({ title: trans('Stock limit'), message });
            return;
        }

        let banner = document.getElementById("stockLimitBanner");
        if (!banner) {
            banner = document.createElement("div");
            banner.id = "stockLimitBanner";
            banner.className =
                "fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4 bg-amber-500 text-white px-4 py-3 rounded-xl shadow-xl";
            document.body.appendChild(banner);
        }
        banner.textContent = message;
        banner.style.opacity = "1";
        banner.style.transform = "translate(-50%, 0)";

        clearTimeout(banner._hideTimer);
        banner._hideTimer = setTimeout(() => {
            banner.style.opacity = "0";
            banner.style.transform = "translate(-50%, -100%)";
            setTimeout(() => banner.remove(), 300);
        }, 2500);
    }

    function selectQuantityInputText(input) {
        if (!input) return;
        try {
            if (typeof input.select === "function") {
                input.select();
            }
        } catch (e) {}
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Price display
    // ─────────────────────────────────────────────────────────────────────────

    // Find the price container once and cache it.
    // We use a dedicated id to avoid fragile class-chain selectors.
    const priceContainer = document.getElementById("price-box");

    function updatePriceDisplay(discountInfo) {
        if (!discountInfo || !priceContainer) return;

        // Use the stable id injected by the blade — no fragile class chains needed
        const priceWrapper = document.getElementById("js-price-wrapper");
        if (!priceWrapper) return;

        const originalPrice = parseFloat(discountInfo.original_price) || 0;
        const finalPrice = parseFloat(discountInfo.final_price) || 0;
        const hasActualDiscount =
            discountInfo.has_discount === true && originalPrice > finalPrice;
        const discountPercentage =
            parseFloat(discountInfo.discount_percentage) || 0;

        if (hasActualDiscount) {
            priceWrapper.innerHTML = `
                <div class="text-xl text-gray-400">
                    <span class="line-through">${window.CURRENCY_SYMBOL} ${formatPrice(originalPrice)}</span>
                </div>
                <span id="product-price" class="text-4xl font-extrabold text-red-600 tracking-tight">${window.CURRENCY_SYMBOL} ${formatPrice(finalPrice)}</span>
                ${
                    discountPercentage > 0
                        ? `
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-500 text-white text-xs font-bold self-start">
                    ${trans('Save')} ${discountPercentage.toFixed(0)}%
                </span>`
                        : ""
                }
                <span class="text-xs text-gray-400 font-medium">${trans('incl. taxes')}</span>
            `;
        } else {
            priceWrapper.innerHTML = `
                <span id="product-price" class="text-4xl font-extrabold text-primary tracking-tight">${window.CURRENCY_SYMBOL} ${formatPrice(finalPrice || originalPrice)}</span>
                <span class="text-xs text-gray-400 font-medium">${trans('incl. taxes')}</span>
            `;
        }

        // ── Discount applied badge ────────────────────────────────────────
        // Remove ALL existing badges (both server-rendered and any previously
        // injected by JS) before deciding whether to show a fresh one.
        priceContainer
            .querySelectorAll(".discount-applied-badge")
            .forEach((el) => el.remove());

        if (hasActualDiscount && discountInfo.discount_name) {
            const badge = document.createElement("div");
            badge.className =
                "discount-applied-badge mt-3 flex items-center gap-2 px-3 py-2.5 bg-emerald-50 rounded-xl border border-emerald-200";
            badge.innerHTML = `
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-emerald-800">${discountInfo.discount_name} ${trans('applied!')}</span>
            `;
            priceContainer.appendChild(badge);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tabs
    // ─────────────────────────────────────────────────────────────────────────
    const tabButtons = document.querySelectorAll(".tab-button");
    const tabContents = document.querySelectorAll(".tab-content");

    tabButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const targetTab = button.dataset.tab;

            tabButtons.forEach((btn) => {
                btn.classList.remove(
                    "active",
                    "border-primary",
                    "text-gray-900",
                    "border-b-3",
                );
                btn.classList.add("text-gray-600");
            });

            button.classList.add(
                "active",
                "border-primary",
                "text-gray-900",
                "border-b-3",
            );
            button.classList.remove("text-gray-600");

            tabContents.forEach((content) => {
                content.classList.add("hidden");
                content.classList.remove("active");
            });

            const targetContent = document.getElementById(targetTab + "-tab");
            if (targetContent) {
                targetContent.classList.remove("hidden");
                targetContent.classList.add("active");
            }
        });
    });

    // ─────────────────────────────────────────────────────────────────────────
    // Thumbnail gallery
    // Note: product-image-gallery.js also attaches listeners to .thumb-button;
    // this block adds the mainImage.src update as a fallback when gallery.js
    // is not loaded, without conflicting when it is.
    // ─────────────────────────────────────────────────────────────────────────
    const thumbButtons = document.querySelectorAll(".thumb-button");
    const mainImage = document.getElementById("main-product-image");

    // Only run the basic thumb handler if the gallery module didn't already init
    if (!window.ProductGallery) {
        thumbButtons.forEach((btn) => {
            btn.addEventListener("click", () => {
                const src = btn.getAttribute("data-image");
                if (src && mainImage) {
                    mainImage.src = src;
                    mainImage.alt =
                        document.getElementById("product-title")?.textContent ||
                        "";
                }
                thumbButtons.forEach((t) => {
                    t.classList.remove(
                        "ring-2",
                        "ring-primary/30",
                        "border-primary",
                    );
                    t.classList.add("border-gray-200");
                });
                btn.classList.add(
                    "ring-2",
                    "ring-primary/30",
                    "border-primary",
                );
                btn.classList.remove("border-gray-200");
            });
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Variant selection
    // ─────────────────────────────────────────────────────────────────────────
    const variantButtons = document.querySelectorAll(".variant-button");
    const priceEl = document.getElementById("product-price");
    const skuEl = document.getElementById("product-sku");
    const qtyInput = document.getElementById("cart-quantity");
    const qtyDecrease = document.getElementById("qty-decrease");
    const qtyIncrease = document.getElementById("qty-increase");
    const addToCartBtn = document.getElementById("add-to-cart-btn");
    const buyNowBtn = document.getElementById("buy-now-btn");
    let currentMaxStock = null;

    function setMaxStock(value) {
        const parsed = parseInt(value || "", 10);
        if (!Number.isNaN(parsed) && parsed > 0) {
            currentMaxStock = parsed;
            if (qtyInput) qtyInput.setAttribute("max", String(parsed));
        } else {
            currentMaxStock = null;
            if (qtyInput) qtyInput.removeAttribute("max");
        }
    }

    function getMaxStock() {
        return currentMaxStock && currentMaxStock > 0 ? currentMaxStock : null;
    }

    if (qtyInput) {
        const initialMax = parseInt(qtyInput.getAttribute("max") || "", 10);
        if (!Number.isNaN(initialMax) && initialMax > 0) {
            setMaxStock(initialMax);
        }
        qtyInput.addEventListener("focus", function () {
            selectQuantityInputText(qtyInput);
        });
            // Manual input validation (like checkout)
            qtyInput.addEventListener('input', function () {
                let max = getMaxStock();
                const raw = String(qtyInput.value || "");
                if (raw === "") {
                    return;
                }

                let val = parseInt(raw, 10);
                if (isNaN(val) || val < 1) val = 1;
                if (max && val > max) {
                    val = max;
                    showStockLimitMessage(max);
                    qtyInput.classList.add('border-red-500', 'bg-red-50');
                } else {
                    qtyInput.classList.remove('border-red-500', 'bg-red-50');
                }
                qtyInput.value = String(val);
            });
            qtyInput.addEventListener('blur', function () {
                let max = getMaxStock();
                let val = parseInt(qtyInput.value, 10);
                if (isNaN(val) || val < 1) val = 1;
                if (max && val > max) val = max;
                qtyInput.value = String(val);
                qtyInput.classList.remove('border-red-500', 'bg-red-50');
            });
    }

    if (qtyDecrease && qtyIncrease && qtyInput) {
        qtyDecrease.addEventListener("click", () => {
            let v = parseInt(qtyInput.value || "1", 10);
            if (v > 1) qtyInput.value = v - 1;
        });
        qtyIncrease.addEventListener("click", () => {
            let v = parseInt(qtyInput.value || "1", 10);
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
        variantButtons.forEach((v) => {
            v.classList.remove(
                "ring-2",
                "ring-primary/30",
                "border-primary",
                "shadow-md",
            );
            v.classList.add("border-gray-200");
        });
        btn.classList.add(
            "ring-2",
            "ring-primary/30",
            "border-primary",
            "shadow-md",
        );
        btn.classList.remove("border-gray-200");

        const price = btn.getAttribute("data-price");
        const sku = btn.getAttribute("data-sku");
        const qty = parseInt(btn.getAttribute("data-qty") || "0", 10);
        const variantImage = btn.getAttribute("data-image");
        const variantSlug = btn.getAttribute("data-slug");
        const variantName = btn.getAttribute("data-variant-name");
        const variantAttribs = btn.getAttribute("data-attributes");
        const variantId = btn.getAttribute("data-variant-id");
        const discountData = btn.getAttribute("data-discount");

        // ── Discount ──────────────────────────────────────────────────────
        let discountInfo = null;
        if (discountData) {
            try {
                discountInfo = JSON.parse(discountData);
                discountInfo.has_discount =
                    discountInfo.has_discount === true ||
                    discountInfo.has_discount === 1 ||
                    discountInfo.has_discount === "1" ||
                    discountInfo.has_discount === "true";
                updatePriceDisplay(discountInfo);
            } catch (e) {
                discountInfo = {
                    has_discount: false,
                    original_price: price,
                    final_price: price,
                    discount_percentage: 0,
                    discount_name: null,
                };
                updatePriceDisplay(discountInfo);
            }
        } else {
            discountInfo = {
                has_discount: false,
                original_price: price,
                final_price: price,
                discount_percentage: 0,
                discount_name: null,
            };
            updatePriceDisplay(discountInfo);
        }

        // ── SKU ───────────────────────────────────────────────────────────
        if (skuEl && sku) skuEl.textContent = "SKU: " + sku;

        // ── Price text ────────────────────────────────────────────────────
        if (priceEl) {
            const displayPrice = discountInfo?.final_price || price || 0;
            priceEl.textContent =
                window.CURRENCY_SYMBOL + " " + formatPrice(displayPrice);
        }

        // ── Title ─────────────────────────────────────────────────────────
        const productTitleEl = document.getElementById("product-title");
        if (productTitleEl) {
            const baseName =
                productTitleEl.getAttribute("data-base-name") ||
                productTitleEl.textContent;
            let decodedVariantName = variantName;
            if (variantName && typeof variantName === "string") {
                const txt = document.createElement("textarea");
                txt.innerHTML = variantName;
                decodedVariantName = txt.value;
            }
            productTitleEl.textContent = decodedVariantName || baseName;
        }

        // ── Quantity / stock ──────────────────────────────────────────────
        if (qtyInput) {
            if (qty > 0) {
                qtyInput.removeAttribute("hidden");
                setMaxStock(qty);
                qtyInput.value = "1";
            } else {
                qtyInput.value = "0";
                setMaxStock(null);
                qtyInput.setAttribute("hidden", "hidden");
            }
        }
        [qtyDecrease, qtyIncrease].forEach((el) => {
            if (!el) return;
            qty > 0
                ? el.removeAttribute("hidden")
                : el.setAttribute("hidden", "hidden");
        });

        // ── Cart / Buy buttons ─────────────────────────────────────────────
        [addToCartBtn, buyNowBtn].forEach((el) => {
            if (!el) return;
            if (qty > 0) {
                el.removeAttribute("hidden");
                el.classList.remove("opacity-50", "cursor-not-allowed");
            } else {
                el.setAttribute("hidden", "hidden");
                el.classList.add("opacity-50", "cursor-not-allowed");
            }
        });

        // ── Global state ──────────────────────────────────────────────────
        window.currentSelectedVariantId = variantId ? String(variantId) : null;
        window.currentSelectedVariantSlug = variantSlug
            ? String(variantSlug)
            : null;
        window.PRODUCT_VARIANT_ID = variantId ? parseInt(variantId, 10) : null;

        const cartVariantInput = document.getElementById("cart-variant-id");
        if (cartVariantInput) cartVariantInput.value = variantId || "";

        // ── Update gallery images ─────────────────────────────────────────
        try {
            const imagesJson = btn.getAttribute("data-images");
            const productName =
                document.getElementById("product-title")?.textContent || "";

            if (imagesJson && window.ProductGallery) {
                // Use the gallery module for a proper animated swap
                const imagesList = JSON.parse(imagesJson);
                if (Array.isArray(imagesList) && imagesList.length > 0) {
                    window.ProductGallery.setImages(
                        imagesList.map((src) => ({ src, alt: productName })),
                    );
                } else if (variantImage) {
                    // Single image fallback
                    window.ProductGallery.setImages([
                        { src: variantImage, alt: productName },
                    ]);
                }
            } else if (imagesJson) {
                // Fallback: no gallery module — update thumbs manually
                const imagesList = JSON.parse(imagesJson);
                const thumbs = document.querySelectorAll(".thumb-button");
                thumbs.forEach((t, i) => {
                    const src = imagesList[i];
                    if (src) {
                        t.setAttribute("data-image", src);
                        const img = t.querySelector("img");
                        if (img) img.src = src;
                    }
                });
                thumbs.forEach((t) => {
                    t.classList.remove(
                        "ring-2",
                        "ring-primary/30",
                        "border-primary",
                    );
                    t.classList.add("border-gray-200");
                });
                if (thumbs[0]) {
                    thumbs[0].classList.add(
                        "ring-2",
                        "ring-primary/30",
                        "border-primary",
                    );
                    thumbs[0].classList.remove("border-gray-200");
                }
                if (mainImage && variantImage) {
                    mainImage.src = variantImage;
                    mainImage.alt = productName;
                }
            } else if (variantImage && mainImage) {
                mainImage.src = variantImage;
                mainImage.alt =
                    document.getElementById("product-title")?.textContent || "";
            }
        } catch (e) {
            // Last resort: just swap the main image
            if (variantImage && mainImage) {
                mainImage.src = variantImage;
            }
        }

        // ── URL slug ──────────────────────────────────────────────────────
        try {
            if (variantSlug) {
                const url = new URL(window.location.href);
                url.searchParams.set("variant", variantSlug);
                window.history.replaceState({}, "", url.toString());
            }
        } catch (e) {}

        // ── Variant specs ─────────────────────────────────────────────────
        renderVariantSpecs(variantAttribs);

        // ── Dispatch variant changed event ────────────────────────────────
        try {
            document.dispatchEvent(
                new CustomEvent("variantChanged", {
                    detail: { variantId: window.PRODUCT_VARIANT_ID },
                }),
            );
        } catch (e) {
            if (
                window.ProductReviews &&
                typeof window.ProductReviews.reload === "function"
            ) {
                window.ProductReviews.reload();
            }
        }
    }

    if (variantButtons.length > 0) {
        variantButtons.forEach((btn) => {
            btn.addEventListener("click", () => setActiveVariant(btn));
        });

        const specsRoot = document.getElementById("specifications-root");
        const variantSpecsEl = document.getElementById("variant-specs");

        function renderVariantSpecs(specData) {
            if (!specsRoot || !variantSpecsEl) return;
            try {
                if (!specData) {
                    variantSpecsEl.innerHTML = "";
                    return;
                }

                function htmlDecode(input) {
                    const txt = document.createElement("textarea");
                    txt.innerHTML = input;
                    return txt.value;
                }

                if (typeof specData === "string") {
                    const decoded = htmlDecode(specData.trim());
                    if (
                        !decoded ||
                        ["null", "[]", "{}", '""'].includes(decoded)
                    ) {
                        variantSpecsEl.innerHTML = "";
                        return;
                    }
                    try {
                        specData = JSON.parse(decoded);
                    } catch (e) {
                        specData = decoded;
                    }
                }

                function getLocaleText(v) {
                    if (typeof v === "string") return v;
                    if (typeof v === "object")
                        return (
                            v[document.documentElement.lang] ||
                            v.en ||
                            JSON.stringify(v)
                        );
                    return String(v ?? "");
                }

                function rowsFromLocalizedArray(o) {
                    const lang = document.documentElement.lang || "en";
                    let arr = Array.isArray(o[lang])
                        ? o[lang]
                        : Array.isArray(o.en)
                          ? o.en
                          : null;
                    if (!arr) {
                        for (const v of Object.values(o)) {
                            if (Array.isArray(v)) {
                                arr = v;
                                break;
                            }
                        }
                    }
                    if (!arr) return null;
                    return arr
                        .filter((item) => item && typeof item === "object")
                        .map((item) => ({
                            k: item.key || item.title || item.label || "",
                            v: item.value || item.description || "",
                        }));
                }

                function normalize(spec) {
                    const pairs = [];
                    if (!spec && spec !== 0) return pairs;
                    if (typeof spec === "string") {
                        pairs.push({ k: "", v: spec });
                        return pairs;
                    }
                    if (Array.isArray(spec)) {
                        spec.forEach((item, idx) => {
                            if (item && typeof item === "object") {
                                if ("key" in item && "value" in item)
                                    pairs.push({
                                        k: item.key || "Spec " + (idx + 1),
                                        v: item.value,
                                    });
                                else if ("title" in item && "value" in item)
                                    pairs.push({
                                        k: item.title,
                                        v: item.value,
                                    });
                                else
                                    Object.entries(item).forEach(([kk, vv]) =>
                                        pairs.push({
                                            k: kk,
                                            v:
                                                typeof vv === "object"
                                                    ? getLocaleText(vv)
                                                    : vv,
                                        }),
                                    );
                            } else {
                                pairs.push({
                                    k: "Spec " + (idx + 1),
                                    v: String(item),
                                });
                            }
                        });
                        return pairs;
                    }
                    if (spec && typeof spec === "object") {
                        const localized = rowsFromLocalizedArray(spec);
                        if (localized && localized.length) return localized;
                        Object.entries(spec).forEach(([k, v]) => {
                            pairs.push({
                                k,
                                v: typeof v === "object" ? getLocaleText(v) : v,
                            });
                        });
                    }
                    return pairs;
                }

                function renderRowsFromObject(obj) {
                    const normalized = normalize(obj);
                    let rows = "";
                    normalized.forEach((p) => {
                        const val = p.v ?? "";
                        const isImage =
                            typeof val === "string" &&
                            (val.match(/\.(jpg|jpeg|png|gif|webp)(\?|$)/i) ||
                                val.includes("/storage/") ||
                                val.startsWith("http"));
                        const display = isImage
                            ? `<img src="${val}" class="w-24 h-24 object-contain" alt="${p.k || ""}">`
                            : (val ?? "");
                        rows += `<tr><td class="py-3 text-gray-500 font-medium w-1/3 align-top">${p.k || ""}</td><td class="py-3 text-gray-900">${display}</td></tr>`;
                    });
                    return rows;
                }

                let variantHtml = "";
                if (Array.isArray(specData) && specData.length > 0) {
                    let rows = "";
                    specData.forEach((item, idx) => {
                        if (item && typeof item === "object") {
                            if (item.title || item.value) {
                                rows += `<tr><td class="py-3 text-gray-500 font-medium w-1/3">${item.key || ""}</td><td class="py-3 text-gray-900">${item.value || item.description || ""}</td></tr>`;
                            } else {
                                rows += renderRowsFromObject(item);
                            }
                        } else if (item !== undefined && item !== null) {
                            rows += `<tr><td class="py-3 text-gray-500 font-medium w-1/3">Spec ${idx + 1}</td><td class="py-3 text-gray-900">${String(item)}</td></tr>`;
                        }
                    });
                    if (rows) {
                        variantHtml = `
                            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">${trans('Variant Specifications')}</h4>
                                <table class="w-full text-sm"><tbody class="divide-y divide-gray-100">${rows}</tbody></table>
                            </div>`;
                    }
                } else if (specData && typeof specData === "object") {
                    const items = renderRowsFromObject(specData);
                    if (items) {
                        variantHtml = `
                            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">${trans('Variant Specifications')}</h4>
                                <table class="w-full text-sm"><tbody class="divide-y divide-gray-100">${items}</tbody></table>
                            </div>`;
                    }
                }

                if (variantHtml) {
                    try {
                        document
                            .querySelector(
                                '.tab-button[data-tab="specifications"]',
                            )
                            ?.click();
                    } catch (e) {}
                    variantSpecsEl.innerHTML = variantHtml;
                } else {
                    variantSpecsEl.innerHTML = "";
                }
            } catch (e) {
                variantSpecsEl.innerHTML = "";
            }
        }

        window.renderVariantSpecs = renderVariantSpecs;

        // Auto-select variant from URL or pick first
        try {
            const vSlug = new URL(window.location.href).searchParams.get(
                "variant",
            );
            const match = vSlug
                ? Array.from(variantButtons).find(
                      (b) => b.getAttribute("data-slug") === vSlug,
                  )
                : null;
            setActiveVariant(match || variantButtons[0]);
        } catch (e) {
            setActiveVariant(variantButtons[0]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Add to cart form
    // ─────────────────────────────────────────────────────────────────────────
    const addToCartForm = document.getElementById("addToCartForm");
    let clickedButton = null;
    let isInCartForSelectedVariant = false;

    if (addToCartForm) {
        const _addBtn = document.getElementById("add-to-cart-btn");
        const _addBtnLabel = document.getElementById("add-to-cart-btn-label");
        const _buyBtn = document.getElementById("buy-now-btn");
        const _variantInput = document.getElementById("cart-variant-id");
        const _productInput = addToCartForm.querySelector('input[name="product_id"]');
        const _csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

        function setAddToCartButtonState(inCart) {
            isInCartForSelectedVariant = !!inCart;
            if (!_addBtn) return;

            if (isInCartForSelectedVariant) {
                _addBtn.disabled = true;
                _addBtn.setAttribute("aria-disabled", "true");
                _addBtn.classList.add("opacity-60", "cursor-not-allowed");
                _addBtn.classList.remove("hover:shadow-lg", "hover:-translate-y-0.5", "active:translate-y-0");
                if (_addBtnLabel) {
                    _addBtnLabel.textContent = trans('In Cart');
                }
            } else {
                _addBtn.disabled = false;
                _addBtn.removeAttribute("aria-disabled");
                _addBtn.classList.remove("opacity-60", "cursor-not-allowed");
                _addBtn.classList.add("hover:shadow-lg", "hover:-translate-y-0.5", "active:translate-y-0");
                if (_addBtnLabel) {
                    _addBtnLabel.textContent = trans('Add to Cart');
                }
            }
        }

        async function syncAddToCartStateFromServer() {
            if (!_productInput) return;

            const url = addToCartForm.dataset.itemStatusUrl;
            if (!url) return;

            const variantId = (_variantInput?.value || window.currentSelectedVariantId || "").trim();
            const payload = {
                product_id: Number(_productInput.value || 0),
            };

            if (variantId) {
                payload.variant_id = Number(variantId);
            }

            try {
                const resp = await fetch(url, {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": _csrf,
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                });

                if (!resp.ok) return;

                const data = await resp.json();
                if (data && data.success) {
                    setAddToCartButtonState(!!data.in_cart);
                }
            } catch (e) {
                // Ignore transient network failures and keep current button state.
            }
        }

        if (_addBtn)
            _addBtn.addEventListener("click", () => {
                clickedButton = "add_to_cart";
            });
        if (_buyBtn)
            _buyBtn.addEventListener("click", () => {
                clickedButton = "buy_now";
            });

        addToCartForm.addEventListener("submit", function (e) {
            e.preventDefault();

            if (clickedButton !== "buy_now" && isInCartForSelectedVariant) {
                return;
            }

            const formData = new FormData(this);
            const isBuyNow = clickedButton === "buy_now";
            const actionUrl = isBuyNow
                ? this.dataset.buyNowUrl || "/cart/buy-now"
                : this.dataset.addUrl || this.action;

            fetch(actionUrl, {
                method: "POST",
                body: formData,
                headers: { Accept: "application/json" },
            })
                .then((r) => r.json())
                .then((data) => {
                    if (data.success) {
                        if (!isBuyNow && data.summary?.itemCount !== undefined) {
                            window.dispatchEvent(
                                new CustomEvent("cart:changed", {
                                    detail: {
                                        summary: data.summary,
                                        itemCount: data.summary.itemCount,
                                    },
                                }),
                            );
                        }

                        const msg = data.message || trans('Item added to cart.');
                        if (window.showUniversalPopup)
                            showUniversalPopup(msg, "success", 2000);
                        else if (window.Toast?.success)
                            window.Toast.success({
                                title: "Success",
                                message: msg,
                            });
                        else alert(msg);

                        if (isBuyNow) {
                            setTimeout(() => {
                                window.location.href =
                                    data.redirect_to || window.CHECKOUT_URL || "/checkout?step=2";
                            }, 500);
                        } else {
                            setAddToCartButtonState(true);
                        }
                    } else {
                        const msg =
                            data.message || trans('Could not add item to cart.');
                        if (window.showUniversalPopup)
                            showUniversalPopup(msg, "error", 3000);
                        else if (window.Toast?.error)
                            window.Toast.error({
                                title: "Error",
                                message: msg,
                            });
                        else alert(msg);
                    }
                })
                .catch(() => {
                    const msg = trans('An error occurred. Please try again.');
                    if (window.showUniversalPopup)
                        showUniversalPopup(msg, "error", 3000);
                    else if (window.Toast?.error)
                        window.Toast.error({ title: "Error", message: msg });
                    else alert(msg);
                });
        });

        document.addEventListener("variantChanged", function () {
            syncAddToCartStateFromServer();
        });

        window.addEventListener("cart:changed", function () {
            syncAddToCartStateFromServer();
        });

        syncAddToCartStateFromServer();
    }
});
