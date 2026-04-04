$(document).ready(function () {
    const $typeSelect = $('#filter-type');

    if (!$typeSelect.length) {
        return;
    }

    const selectedType = $typeSelect.data('selected') || '';
    const typesUrl = $typeSelect.data('types-url') || '/get-types';

    $.ajax({
        url: typesUrl,
        type: 'GET',
        success: function (response) {
            const types = Array.isArray(response)
                ? response
                : (Array.isArray(response.types) ? response.types : []);

            $typeSelect.empty();
            $typeSelect.append(new Option('All', ''));

            types.forEach(function (type) {
                const value = typeof type === 'string' ? type : (type?.type ?? type?.value ?? '');

                if (!value) {
                    return;
                }

                const label = typeof type === 'string'
                    ? type.charAt(0).toUpperCase() + type.slice(1)
                    : (type?.label ?? type?.name ?? value);

                $typeSelect.append(new Option(label, value));
            });

            if (selectedType) {
                $typeSelect.val(selectedType);
            }
        },
        error: function (xhr, status, error) {
            console.error('Failed to load inventory transaction types:', error);
            $typeSelect.empty().append(new Option('All', ''));
        }
    });
});
