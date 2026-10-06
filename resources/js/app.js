//
import './bootstrap';

// Import Vendor JavaScript
import Swal from 'sweetalert2';
import moment from 'moment';
import select2 from 'select2';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import JSZip from 'jszip';
import daterangepicker from 'daterangepicker';

// Vendor styles — the admin master layout loads only app.js via @vite, so CSS
// imported here is injected alongside it (FontAwesome + DataTables + Buttons).
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css';
import 'select2/dist/css/select2.min.css';
import 'daterangepicker/daterangepicker.css';

// Make tools globally available for inline Blade scripts if needed
window.Swal = Swal;

window.DataTable = DataTable;
window.JSZip = JSZip;
window.daterangepicker = daterangepicker;

window.moment = moment;

// Required by the Buttons "excel" (excelHtml5) export.
DataTable.Buttons.jszip(JSZip);

// Register the Select2 jQuery plugin at module-evaluation time. bootstrap.js has
// already exposed window.jQuery, and deferred modules run before DOMContentLoaded,
// so inline Blade @section('script') handlers can rely on $.fn.select2 existing.
if (window.jQuery) {
    select2(window, window.jQuery);
}

// Initialize components on DOM load
document.addEventListener('DOMContentLoaded', () => {
    // Example: Initialize Select2
    if (window.jQuery) {
        window.jQuery('.select2').select2();

        window.jQuery('table.data-table').each(function () {
            DataTable(this);
        });
    }

});