// --- Translate All Fields Button Handler ---
$(document).on("click", "#btn-translate-all", function (e) {
    e.preventDefault();
    var btn = $(this);
    var url = btn.data("url");
    var fields = btn.data("fields");
    if (typeof fields === 'string') fields = JSON.parse(fields);
    var data = {};

    fields.forEach(function(field) {
        var input = document.getElementById(field + '_en');
        data[field] = input ? input.value : '';
    });

    btn.prop("disabled", true).text("Translating...");

    axios.post(url, {
        fields: data,
        from: 'en',
        to: ['fr', 'ar']
    }).then(function (resp) {
        var result = resp.data;
        fields.forEach(function(field) {
            if (result.fr && result.fr[field] !== undefined) {
                var inputFr = document.getElementById(field + '_fr');
                if (inputFr) inputFr.value = result.fr[field];
            }
            if (result.ar && result.ar[field] !== undefined) {
                var inputAr = document.getElementById(field + '_ar');
                if (inputAr) inputAr.value = result.ar[field];
            }
        });
        btn.prop("disabled", false).text("Translate All Fields");
    }).catch(function (err) {
        alert("Translation failed. Please try again.");
        btn.prop("disabled", false).text("Translate All Fields");
    });
});

// --- Main Form Logic (Quill + Step Navigation) ---
document.addEventListener("DOMContentLoaded", function () {
    // Initialize Quill editors
    const languages = ["en", "fr", "ar"];
    const quillEditors = {};

    languages.forEach((lang) => {
        const textarea = document.querySelector(`textarea[name="description[${lang}]"]`);
        if (textarea) {
            const container = document.createElement("div");
            container.className = "quill-editor";
            container.id = `editor_description_${lang}`;
            container.style.height = "200px";
            textarea.parentNode.insertBefore(container, textarea);
            textarea.style.display = "none";

            const quill = new Quill(container, {
                theme: "snow",
                placeholder: `Enter description in ${lang.toUpperCase()}...`,
                modules: {
                    toolbar: [
                        [{ font: [] }],
                        [{ size: ["small", false, "large", "huge"] }],
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],
                        ["bold", "italic", "underline", "strike"],
                        [{ color: [] }, { background: [] }],
                        [{ script: "sub" }, { script: "super" }],
                        [{ list: "ordered" }, { list: "bullet" }],
                        [{ list: "check" }],
                        [{ indent: "-1" }, { indent: "+1" }],
                        [{ align: [] }],
                        [{ direction: "rtl" }],
                        ["blockquote", "code-block"],
                        [{ header: 1 }, { header: 2 }],
                        ["link", "image", "video", "formula"],
                        ["clean"]
                    ],
                },
            });

            if (textarea.value) {
                quill.root.innerHTML = textarea.value;
            }

            quill.on("text-change", function () {
                textarea.value = quill.root.innerHTML;
            });

            container.__quill = quill;
            textarea.__quill = quill;

            quillEditors[lang] = quill;
        }
    });

    // Refresh Quill on Tab Switch
    const langTabs = document.querySelectorAll("#langTab a, #langTabEdit a");
    langTabs.forEach((tab) => {
        tab.addEventListener("shown.bs.tab", function () {
            setTimeout(() => {
                Object.values(quillEditors).forEach((quill) => {
                    quill.focus();
                    quill.blur();
                });
            }, 100);
        });
    });

    // Sync Quill to Textarea on Submit
    const form = document.getElementById("showroom-form") || document.querySelector('form[action*="showrooms"]');
    if (form) {
        form.addEventListener("submit", function (e) {
            Object.values(quillEditors).forEach((quill) => {
                const lang = Object.keys(quillEditors).find(key => quillEditors[key] === quill);
                const textarea = document.querySelector(`textarea[name="description[${lang}]"]`);
                if (textarea) {
                    textarea.value = quill.root.innerHTML;
                }
            });
        });
    }

    // --- FIXED STEP NAVIGATION ---
    const nextBtns = document.querySelectorAll(".next-step");
    const prevBtns = document.querySelectorAll(".prev-step");

    // Helper function to handle tab switching manually to prevent stacking
    function switchTab(targetId) {
        // 1. Activate the Nav Link (Top Tab)
        const triggerEl = document.querySelector(`#${targetId}-tab`);
        if (triggerEl) {
            triggerEl.classList.remove("disabled");
            if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                const tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
                tab.show();
            }
        }

        // 2. Manage the Content Pane (The View)
        // Find the target pane
        const targetPane = document.getElementById(targetId);
        if (targetPane) {
            const container = targetPane.parentElement; // This gets the correct .tab-content (works for both Create & Edit)

            // Explicitly loop through all siblings and remove active/show classes
            Array.from(container.children).forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            // Add classes to the target pane
            targetPane.classList.add('show', 'active');
        }
    }

    nextBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const nextTabId = this.getAttribute("data-next");
            switchTab(nextTabId);
        });
    });

    prevBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const prevTabId = this.getAttribute("data-prev");
            switchTab(prevTabId);
        });
    });

    // --- OPENING HOURS: Toggle time inputs based on closed checkbox ---
    const closedCheckboxes = document.querySelectorAll(".is-closed-check");

    function updateTimeInputState(checkbox) {
        const row = checkbox.closest("tr");
        if (row) {
            const openInput = row.querySelector(".open-time");
            const closeInput = row.querySelector(".close-time");

            if (checkbox.checked) {
                openInput.disabled = true;
                closeInput.disabled = true;
                openInput.style.opacity = "0.5";
                closeInput.style.opacity = "0.5";
            } else {
                openInput.disabled = false;
                closeInput.disabled = false;
                openInput.style.opacity = "1";
                closeInput.style.opacity = "1";
            }
        }
    }

    closedCheckboxes.forEach((checkbox) => {
        // Set initial state on page load
        updateTimeInputState(checkbox);

        // Update state on checkbox change
        checkbox.addEventListener("change", function () {
            updateTimeInputState(this);
        });
    });

    // --- GALLERY IMAGES: Handle new image preview and deletion ---
    const galleryInput = document.getElementById("gallery_images_input");
    const newGalleryPreview = document.getElementById("new-gallery-preview");
    const newGalleryContainer = document.getElementById("new-gallery");
    let selectedFiles = [];

    if (galleryInput) {
        galleryInput.addEventListener("change", function (e) {
            selectedFiles = Array.from(this.files);
            renderNewGalleryPreview();
        });
    }

    function renderNewGalleryPreview() {
        if (selectedFiles.length === 0) {
            newGalleryPreview.style.display = "none";
            newGalleryContainer.innerHTML = "";
            return;
        }

        newGalleryPreview.style.display = "block";
        newGalleryContainer.innerHTML = "";

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (event) {
                const imageDiv = document.createElement("div");
                imageDiv.className = "position-relative";
                imageDiv.innerHTML = `
                    <img src="${event.target.result}"
                        style="height:60px; width:60px; object-fit:cover; border-radius:4px;">
                    <button type="button" class="btn btn-sm btn-danger delete-new-image"
                        style="position: absolute; top: -8px; right: -8px; padding: 2px 6px; font-size: 10px;"
                        data-index="${index}"
                        title="Delete Image">
                        ×
                    </button>
                `;
                newGalleryContainer.appendChild(imageDiv);
            };
            reader.readAsDataURL(file);
        });
    }

    // Delete new image from preview
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("delete-new-image")) {
            e.preventDefault();
            const index = parseInt(e.target.dataset.index);
            selectedFiles.splice(index, 1);

            // Update the file input with remaining files using DataTransfer
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            galleryInput.files = dataTransfer.files;

            renderNewGalleryPreview();
        }
    });

    // Delete existing image
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("delete-existing-image")) {
            e.preventDefault();
            const btn = e.target;
            const imageId = btn.dataset.imageId;
            const url = btn.dataset.url;
            const imageItem = btn.closest(".gallery-image-item");

            if (confirm("Are you sure you want to delete this image?")) {
                // Show loading state
                btn.disabled = true;
                btn.textContent = "...";

                axios.delete(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(function (response) {
                    // Remove the image from DOM
                    imageItem.remove();

                    // If no images left, hide the existing gallery section
                    const existingGallery = document.getElementById("existing-gallery");
                    if (existingGallery && existingGallery.children.length === 0) {
                        const section = existingGallery.parentElement;
                        section.remove();
                    }
                }).catch(function (error) {
                    alert("Failed to delete image. Please try again.");
                    btn.disabled = false;
                    btn.textContent = "×";
                });
            }
        }
    });
});
