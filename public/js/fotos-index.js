
// LOGICA JS REFERENTE AL CONTENIDO DEL INDEX --->> DEscomentar si se requiere(codigo para filtrado por fechas)
//ARCHIVO Javascript para manejo de filtrado por fechas
/*document.addEventListener("DOMContentLoaded", function () {
    console.log(' Inicializando Fotografías de Prendas...');

    // Initialize all components
    initializeDatePickers();
    initializeColumnToggle();
    initializeLightbox();
    initializeNotifications();
    initializeSearch();

    console.log(' Fotografías de Prendas inicializado correctamente');
});

// ===== DATE PICKER FUNCTIONALITY =====
function initializeDatePickers() {
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');

    // Set default dates
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

    if (fechaInicio) {
        fechaInicio.value = thirtyDaysAgo.toISOString().split('T')[0];
    }

    if (fechaFin) {
        fechaFin.value = today.toISOString().split('T')[0];
    }

    console.log(' Date pickers inicializados');
}

function applyDateFilter() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    if (!fechaInicio || !fechaFin) {
        showNotification('Por favor selecciona ambas fechas', 'warning');
        return;
    }

    if (new Date(fechaInicio) > new Date(fechaFin)) {
        showNotification('La fecha de inicio debe ser anterior a la fecha fin', 'error');
        return;
    }

    console.log(' Aplicando filtro de fechas:', fechaInicio, 'a', fechaFin);
    showNotification('Filtro de fechas aplicado correctamente', 'success');

    // Aquí iría la lógica para filtrar los datos
    // filterTableByDate(fechaInicio, fechaFin);
}

// ===== COLUMN TOGGLE FUNCTIONALITY =====
function initializeColumnToggle() {
    const dropdown = document.getElementById('columnsDropdown');

    if (!dropdown) {
        console.warn(' Dropdown de columnas no encontrado');
        return;
    }

    dropdown.addEventListener('change', function (e) {
        if (e.target.type === 'checkbox' && e.target.dataset.column) {
            const columnName = e.target.dataset.column;
            const isVisible = e.target.checked;

            toggleColumn(columnName, isVisible);
            showNotification(
                `Columna "${getColumnDisplayName(columnName)}" ${isVisible ? 'mostrada' : 'ocultada'}`,
                'info'
            );
        }
    });

    console.log(' Control de columnas inicializado');
}

function toggleColumn(columnName, isVisible) {
    const display = isVisible ? '' : 'none';
    const table = document.querySelector('.images-table');

    if (!table) return;

    // Toggle header
    const headerCell = table.querySelector(`th[data-column="${columnName}"]`);
    if (headerCell) {
        headerCell.style.display = display;
    }

    // Toggle filter cell
    const filterCell = table.querySelector(`tr.bg-light td[data-column="${columnName}"]`);
    if (filterCell) {
        filterCell.style.display = display;
    }

    // Toggle data cells
    const dataCells = table.querySelectorAll(`tbody td[data-column="${columnName}"]`);
    dataCells.forEach(cell => {
        cell.style.display = display;
    });
}

function getColumnDisplayName(columnKey) {
    const names = {
        'imagen': 'Imagen',
        'orden-sit': 'Orden SIT',
        'po': 'P.O',
        'oc': 'O.C',
        'descripcion': 'Descripción',
        'tipo-fotografia': 'Tipo Fotografía',
        'acciones': 'Acciones'
    };
    return names[columnKey] || columnKey;
}

// ===== LIGHTBOX FUNCTIONALITY =====
function initializeLightbox() {
    const lightbox = document.getElementById('imageLightbox');

    if (lightbox) {
        // Close lightbox when clicking outside the content
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        // Close lightbox with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && lightbox.style.display !== 'none') {
                closeLightbox();
            }
        });
    }

    console.log(' Lightbox inicializado');
}

function openImageLightbox(imageUrl, alt, description, type) {
    console.log(' Abriendo lightbox para imagen:', imageUrl);

    const lightbox = document.getElementById('imageLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxDescription = document.getElementById('lightboxDescription');
    const lightboxType = document.getElementById('lightboxType');

    if (lightbox && lightboxImage) {
        lightboxImage.src = imageUrl;
        lightboxImage.alt = alt;

        if (lightboxDescription) {
            lightboxDescription.textContent = description || alt || 'Sin descripción';
        }

        if (lightboxType) {
            lightboxType.textContent = type || 'Sin tipo especificado';
        }

        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
}

function closeLightbox() {
    console.log(' Cerrando lightbox');

    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) {
        lightbox.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }
}

function downloadImage() {
    const lightboxImage = document.getElementById('lightboxImage');
    if (lightboxImage && lightboxImage.src) {
        const link = document.createElement('a');
        link.href = lightboxImage.src;
        link.download = lightboxImage.alt || 'imagen';
        link.click();

        showNotification('Descarga iniciada', 'success');
    }
}

// ===== NOTIFICATION SYSTEM =====
function initializeNotifications() {
    // Create notification container if it doesn't exist
    if (!document.getElementById('notificationContainer')) {
        const container = document.createElement('div');
        container.id = 'notificationContainer';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    console.log(' Sistema de notificaciones inicializado');
}

function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notificationContainer');
    if (!container) return;

    const alertTypes = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };

    const icons = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    };

    const notification = document.createElement('div');
    notification.className = `alert ${alertTypes[type] || alertTypes.info} notification alert-dismissible fade show`;
    notification.innerHTML = `
        <i class="${icons[type] || icons.info} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    container.appendChild(notification);

    // Auto remove after duration
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);

    console.log(` Notificación mostrada: ${message}`);
}

// ===== SEARCH FUNCTIONALITY =====
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        // Search on Enter key
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchRecords();
            }
        });

        // Real-time search (optional)
        // searchInput.addEventListener('input', debounce(searchRecords, 500));
    }

    console.log(' Sistema de búsqueda inicializado');
}

function searchRecords() {
    const searchTerm = document.getElementById('searchInput').value.trim();

    if (!searchTerm) {
        showNotification('Ingresa un término de búsqueda', 'warning');
        return;
    }

    console.log(' Buscando:', searchTerm);
    showNotification(`Buscando: "${searchTerm}"`, 'info');

    // Aquí iría la lógica de búsqueda
    // filterTableBySearch(searchTerm);
}

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        console.log(' Búsqueda limpiada');
        showNotification('Búsqueda limpiada', 'info');

        // Aquí iría la lógica para mostrar todos los registros
        // clearTableFilters();
    }
}

// ===== EXPORT FUNCTIONALITY =====
function exportAll() {
    console.log(' Exportando todos los registros...');
    showNotification('Exportando todos los registros...', 'info');

    // Aquí iría la lógica de exportación
    setTimeout(() => {
        showNotification('Exportación completada', 'success');
    }, 2000);
}

function exportSelected() {
    console.log(' Exportando registros seleccionados...');
    showNotification('Exportando registros seleccionados...', 'info');

    // Aquí iría la lógica de exportación
    setTimeout(() => {
        showNotification('Exportación completada', 'success');
    }, 2000);
}

function showFilters() {
    console.log(' Mostrando filtros avanzados...');
    showNotification('Filtros avanzados mostrados', 'info');

    // Aquí iría la lógica para mostrar filtros avanzados
}

// ===== TABLE ACTIONS =====
function deleteImage(button) {
    if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
        const row = button.closest('tr');
        if (row) {
            row.remove();
            showNotification('Imagen eliminada correctamente', 'success');
        }
    }
}

function editImage(button) {
    const row = button.closest('tr');
    if (row) {
        const ordenSit = row.querySelector('[data-column="orden-sit"]').textContent;
        showNotification(`Editando imagen con Orden SIT: ${ordenSit}`, 'info');

        // Aquí iría la lógica de edición
    }
}

// ===== UTILITY FUNCTIONS =====
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Make functions globally available
window.openImageLightbox = openImageLightbox;
window.closeLightbox = closeLightbox;
window.downloadImage = downloadImage;
window.applyDateFilter = applyDateFilter;
window.searchRecords = searchRecords;
window.clearSearch = clearSearch;
window.exportAll = exportAll;
window.exportSelected = exportSelected;
window.showFilters = showFilters;
window.deleteImage = deleteImage;
window.editImage = editImage;*/
