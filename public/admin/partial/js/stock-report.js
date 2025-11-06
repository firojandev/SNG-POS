"use strict";

$(document).ready(function() {
    // Handle per page selection
    $('#perPageSelect').on('change', function() {
        const perPage = $(this).val();
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    });
});
