//
import './bootstrap';

// Import Vendor JavaScript
import Swal from 'sweetalert2';
import select2 from 'select2';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import JSZip from 'jszip';

// Vendor styles — the admin master layout loads only app.js via @vite, so CSS
// imported here is injected alongside it (FontAwesome + DataTables + Buttons).
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css';

// Make tools globally available for inline Blade scripts if needed
window.Swal = Swal;

window.DataTable = DataTable;
window.JSZip = JSZip;

// Required by the Buttons "excel" (excelHtml5) export.
DataTable.Buttons.jszip(JSZip);

// Initialize components on DOM load
document.addEventListener('DOMContentLoaded', () => {
    // Example: Initialize Select2
    if (window.jQuery) {
        select2(window, window.jQuery);
        window.jQuery('.select2').select2();

        window.jQuery('table.data-table').each(function () {
            DataTable(this);
        });
    }

});