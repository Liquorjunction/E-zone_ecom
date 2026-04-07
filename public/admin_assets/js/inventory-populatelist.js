$(document).ready(function () {
    const $typeSelect = $('#filter-type');

    if (!$typeSelect.length) {
        return;
    }

    const selectedType = $typeSelect.data('selected') || '';
    const typesUrl = $typeSelect.data('types-url') || '/get-types';
    let typesLoaded = false;
    let typesLoading = false;

    function buildOption(label, value) {
        return new Option(label, value);
    }

    function populateTypes(types) {
        $typeSelect.empty();
        $typeSelect.append(buildOption('All', ''));

        types.forEach(function (type) {
            const value = typeof type === 'string' ? type : (type?.type ?? type?.value ?? '');

            if (!value) {
                return;
            }

            const label = typeof type === 'string'
                ? type.charAt(0).toUpperCase() + type.slice(1)
                : (type?.label ?? type?.name ?? value);

            $typeSelect.append(buildOption(label, value));
        });

        if (selectedType) {
            $typeSelect.val(selectedType);
        }

        typesLoaded = true;
    }

    function loadTypes() {
        if (typesLoaded || typesLoading) {
            return;
        }

        typesLoading = true;
        $typeSelect.empty().append(buildOption('Loading types...', ''));

        $.ajax({
            url: typesUrl,
            type: 'GET',
            success: function (response) {
                const types = Array.isArray(response)
                    ? response
                    : (Array.isArray(response.types) ? response.types : []);

                populateTypes(types);
            },
            error: function (xhr, status, error) {
                console.error('Failed to load inventory transaction types:', error);
                $typeSelect.empty().append(buildOption('All', ''));
            },
            complete: function () {
                typesLoading = false;
            }
        });
    }

    $typeSelect.on('click focus', function () {
        loadTypes();
    });
});
