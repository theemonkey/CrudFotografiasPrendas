/*!
 * Fotografías de Prendas - Sistema Completo
 * Description: Sistema completo para gestión de fotografías de prendas
 *
 * NOTA: Todo javascript funcional
 */
// ================================================================================================
// VARIABLES GLOBALES Y CONFIGURACIÓN - CONSOLIDADAS
// ================================================================================================

let currentImageData = null;
let commentsData = new Map();
let uploadCount = 0;

// Variables para historial - consolidadas aquí
let tipoFotografiaFilter = {
    active: false,
    selectedTypes: [],
    totalCounts: {
        'MUESTRA': 0,
        'PRENDA FINAL': 0,
        'VALIDACION AC': 0
    }
};

const CONFIG = {
    MAX_FILE_SIZE: 10 * 1024 * 1024,
    MAX_COMMENT_LENGTH: 500,
    DEBUG_MODE: true
};

// ================================================================================================
// INICIALIZACIÓN PRINCIPAL - CONSOLIDADA
// ================================================================================================

// MEJORAR la verificación de inicialización
document.addEventListener("DOMContentLoaded", function () {
    console.log('DOM cargado, iniciando sistema...');

    // Verificación más estricta
    if (window.fotografiasSystemInitialized === true) {
        console.warn('Sistema ya inicializado, ABORTANDO completamente');
        return;
    }

    // Prevenir múltiples inicializaciones con flag inmediato
    if (window.fotografiasSystemInitializing === true) {
        console.warn('Sistema en proceso de inicialización, ABORTANDO');
        return;
    }

    //  Marcar como "inicializando"
    window.fotografiasSystemInitializing = true;
    initializeCompleteSystem();
});

function initializeCompleteSystem() {
    try {
        console.log('Iniciando todos los sistemas...');

        // Sistemas principales
        initializeLightbox();  // Visualizar imagenes
        initializeNotifications(); // Toast notifications
        initializeSearch();        // Búsqueda general
        initializeTipoFotografiaFilter(); // Dropdown

        console.log('Sistema completo inicializado correctamente');

    } catch (error) {
        console.error('Error durante la inicialización:', error);
    }
}

// ================================================================================================
// FUNCION DE COMENTARIOS - Agregar Aqui -- >
// ================================================================================================
//- Agregar Aqui logica existente para comentarios del SIO -- >

// Función temporal para validar botón de comentarios
function openCommentsModal(button) {
    console.log("Boton comentarios funciona");
}

/* =============================================================== */

// ================================================================================================
// FILTRO TIPO FOTOGRAFÍA - CONSOLIDADO
// ================================================================================================

function filterByTipoFotografia() {
    console.log('Aplicando filtro por tipo de fotografía...');

    const muestraCheck = document.getElementById('filtroMuestra');
    const prendaFinalCheck = document.getElementById('filtroPrendaFinal');
    const validacionACCheck = document.getElementById('filtroValidacionAC');

    if (!muestraCheck || !prendaFinalCheck || !validacionACCheck) {
        console.error('No se encontraron los checkboxes');
        return;
    }

    tipoFotografiaFilter.selectedTypes = [];

    if (muestraCheck.checked) tipoFotografiaFilter.selectedTypes.push('MUESTRA');
    if (prendaFinalCheck.checked) tipoFotografiaFilter.selectedTypes.push('PRENDA FINAL');
    if (validacionACCheck.checked) tipoFotografiaFilter.selectedTypes.push('VALIDACION AC');

    tipoFotografiaFilter.active = tipoFotografiaFilter.selectedTypes.length > 0;

    console.log('Tipos seleccionados:', tipoFotografiaFilter.selectedTypes);

    applyTipoFotografiaFilter();
    updateTipoFotografiaUI();

    // Actualizar indicador visual
    updateFilterStatusIndicator();
}

/* ======================================================================================= */
function applyTipoFotografiaFilter() {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) {
        console.error('No se encontró el tbody de la tabla');
        return;
    }

    const rows = tableBody.querySelectorAll('tr');
    let visibleCount = 0;
    let hiddenCount = 0;

    rows.forEach(row => {
        const tipoCell = row.querySelector('td[data-column="tipo-fotografia"]');

        if (!tipoCell) {
            console.warn('Fila sin columna de tipo fotografía');
            return;
        }

        const tipoText = tipoCell.textContent.trim().toUpperCase();
        let shouldShow = true;

        if (tipoFotografiaFilter.active) {
            shouldShow = tipoFotografiaFilter.selectedTypes.some(selectedType =>
                tipoText.includes(selectedType)
            );
        }

        if (shouldShow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
            hiddenCount++;
        }
    });

    // Actualizar cards móviles si existen
    if (window.responsiveSystem) {
        setTimeout(() => {
            window.responsiveSystem.refresh();
        }, 100);
    }

    console.log(`Filtro aplicado: ${visibleCount} visibles, ${hiddenCount} ocultas`);
}

/* ======================================================================================= */
function updateTipoFotografiaUI() {
    const label = document.getElementById('tipoFotografiaLabel');
    const button = document.getElementById('tipoFotografiaDropdown');

    if (!label || !button) return;

    if (tipoFotografiaFilter.active) {
        const count = tipoFotografiaFilter.selectedTypes.length;
        label.innerHTML = `<i class="fas fa-filter me-1"></i>Filtrado (${count})`;
        button.classList.add('btn-primary');
        button.classList.remove('btn-buscar');
        button.style.backgroundColor = '#007bff';
        button.style.borderColor = '#007bff';
        button.style.color = 'white';
    } else {
        label.innerHTML = '<i class="fas fa-search me-1"></i>Buscar';
        button.classList.remove('btn-primary');
        button.classList.add('btn-buscar');
        button.style.backgroundColor = 'white';
        button.style.borderColor = '#ced4da';
        button.style.color = '#212529';
    }
}

/* ======================================================================================= */
// FUNCIÓN: Actualizar indicador de estado del filtro
function updateFilterStatusIndicator() {
    const indicator = document.getElementById('filterStatusIndicator');

    if (!indicator) return;

    if (tipoFotografiaFilter.active && tipoFotografiaFilter.selectedTypes.length > 0) {
        indicator.style.display = 'block';
        indicator.innerHTML = `
            <small class="text-primary">
                <i class="fas fa-filter me-1"></i>
                Filtro activo: ${tipoFotografiaFilter.selectedTypes.length} tipo(s)
            </small>
        `;
    } else {
        indicator.style.display = 'none';
    }
}

function selectAllTipoFotografia() {
    console.log('Seleccionando todos los tipos...');

    const muestraCheck = document.getElementById('filtroMuestra');
    const prendaFinalCheck = document.getElementById('filtroPrendaFinal');
    const validacionACCheck = document.getElementById('filtroValidacionAC');

    if (muestraCheck) muestraCheck.checked = true;
    if (prendaFinalCheck) prendaFinalCheck.checked = true;
    if (validacionACCheck) validacionACCheck.checked = true;

    filterByTipoFotografia();

    console.log('Todos los tipos seleccionados');
}

function clearTipoFotografiaFilter() {
    console.log('Limpiando filtro de tipo fotografía...');

    const muestraCheck = document.getElementById('filtroMuestra');
    const prendaFinalCheck = document.getElementById('filtroPrendaFinal');
    const validacionACCheck = document.getElementById('filtroValidacionAC');

    if (muestraCheck) muestraCheck.checked = false;
    if (prendaFinalCheck) prendaFinalCheck.checked = false;
    if (validacionACCheck) validacionACCheck.checked = false;

    tipoFotografiaFilter.active = false;
    tipoFotografiaFilter.selectedTypes = [];

    const tableBody = document.getElementById('imagesTableBody');
    if (tableBody) {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            row.style.display = '';
        });
    }

    updateTipoFotografiaUI();
    updateFilterStatusIndicator();

    if (window.responsiveSystem) {
        setTimeout(() => {
            window.responsiveSystem.refresh();
        }, 100);
    }

    console.log('Filtro limpiado');
}

/* =========================================================================== */
//  Inicialización del filtro con indicadores
function initializeTipoFotografiaFilter() {
    console.log('Inicializando filtro de tipo fotografía...');

    updateFilterStatusIndicator();

    const dropdownMenu = document.getElementById('tipoFotografiaMenu');
    if (dropdownMenu) {
        dropdownMenu.addEventListener('click', function (e) {
            if (e.target.type === 'checkbox' || e.target.closest('label') || e.target.closest('.btn')) {
                e.stopPropagation();
            }
        });
    }

    const tableBody = document.getElementById('imagesTableBody');
    if (tableBody) {
        const observer = new MutationObserver(() => {
            setTimeout(() => {

                updateFilterStatusIndicator();
                if (tipoFotografiaFilter.active) {
                    applyTipoFotografiaFilter();
                }
            }, 100);
        });

        observer.observe(tableBody, {
            childList: true,
            subtree: true
        });
    }

    console.log('Filtro de tipo fotografía inicializado');
}

// ================================================================================================
// SISTEMAS AUXILIARES
// ================================================================================================

function initializeNotifications() {
    if (!document.getElementById('notificationContainer')) {
        const container = document.createElement('div');
        container.id = 'notificationContainer';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
}

// =====>>>>>> Función para mostrar notificaciones ======>>>>>
function showNotification(message, type = 'info', duration = 4000) {
    console.log(`Notificación: ${message} (${type})`);

    const toastEl = document.getElementById('notificationToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');

    if (!toastEl || !toastMessage || !toastIcon) {
        // Fallback a console si no hay elementos de toast
        console.log(`NOTIFICACIÓN: ${message}`);
        return;
    }

    // Limpiar clases anteriores
    toastEl.className = 'toast align-items-center border-0';
    toastIcon.className = '';

    // Configurar según el tipo
    switch (type) {
        case 'success':
            toastEl.classList.add('text-bg-success');
            toastIcon.className = 'fas fa-check-circle text-white';
            break;
        case 'error':
        case 'danger':
            toastEl.classList.add('text-bg-danger');
            toastIcon.className = 'fas fa-exclamation-triangle text-white';
            break;
        case 'warning':
            toastEl.classList.add('text-bg-warning');
            toastIcon.className = 'fas fa-exclamation-circle text-dark';
            break;
        case 'info':
        default:
            toastEl.classList.add('text-bg-primary');
            toastIcon.className = 'fas fa-info-circle text-white';
            break;
    }

    // Configurar mensaje
    toastMessage.textContent = message;

    // Mostrar toast
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: duration
    });

    toast.show();

    // Auto-ocultar después del tiempo especificado
    setTimeout(() => {
        toast.hide();
    }, duration);
}

function initializeLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) {
        lightbox.onclick = function (e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        };
    }

    window.openImageLightbox = openImageLightbox;
    window.closeLightbox = closeLightbox;
    window.downloadImage = downloadImageFromLightbox;
}

/* ========================================================================================== */
// ----->> Uso para visualizar fotografias
function openImageLightbox(imageUrl, alt, description, type) {
    console.log('Abriendo lightbox:', { imageUrl, alt, description, type });

    const lightbox = document.getElementById('imageLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxDescription = document.getElementById('lightboxDescription');
    const lightboxType = document.getElementById('lightboxType');

    if (lightbox && lightboxImage) {
        lightboxImage.src = imageUrl;
        lightboxImage.alt = alt || 'Imagen';

        if (lightboxDescription) {
            lightboxDescription.textContent = description || alt || 'Sin descripción';
        }

        if (lightboxType) {
            lightboxType.textContent = type || 'Sin tipo especificado';
        }

        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        console.log('Lightbox abierto correctamente');
    } else {
        console.error('Error: No se encontraron los elementos del lightbox');
    }
}

function closeLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) {
        lightbox.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function downloadImageFromLightbox() {
    const lightboxImage = document.getElementById('lightboxImage');
    if (lightboxImage && lightboxImage.src) {
        const link = document.createElement('a');
        link.href = lightboxImage.src;
        link.download = lightboxImage.alt || 'imagen.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');

    if (searchInput) {
        searchInput.addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                searchRecords();
            }
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', function (e) {
            e.preventDefault();
            searchRecords();
        });
    }
}

function searchRecords() {
    console.log('Iniciando búsqueda global...');

    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const tableBody = document.getElementById('imagesTableBody');

    if (!tableBody) {
        console.warn('Tabla no encontrada');
        return;
    }

    const allRows = tableBody.querySelectorAll('tr[data-image-id]');
    let visibleCount = 0;
    let hiddenCount = 0;

    if (searchTerm === '') {
        // Sin búsqueda - remover SOLO clase de búsqueda global
        allRows.forEach(row => {
            row.classList.remove('search-hidden');
            // NO cambiar display aquí - dejar que otros filtros manejen la visibilidad
        });

        // Reactivar filtros predictivos si están activos
        if (typeof applyAllFilters === 'function') {
            setTimeout(() => {
                applyAllFilters();
            }, 100);
        }

        console.log('Búsqueda global limpiada');
    } else {
        // Con búsqueda - aplicar solo a columnas específicas
        allRows.forEach(row => {
            // Buscar SOLO en Orden SIT, P.O y O.C (NO en descripción)
            const ordenSitElement = row.querySelector('td[data-column="orden-sit"]');
            const poElement = row.querySelector('td[data-column="po"]');
            const ocElement = row.querySelector('td[data-column="oc"]');

            const ordenSit = ordenSitElement ? ordenSitElement.textContent.toLowerCase().trim() : '';
            const po = poElement ? poElement.textContent.toLowerCase().trim() : '';
            const oc = ocElement ? ocElement.textContent.toLowerCase().trim() : '';

            // Verificar coincidencias SOLO en estos campos
            const matchesSearch = ordenSit.includes(searchTerm) ||
                po.includes(searchTerm) ||
                oc.includes(searchTerm);

            if (matchesSearch) {
                row.classList.remove('search-hidden');
                // Solo contar como visible si no está oculta por otros filtros
                if (!row.classList.contains('type-filtered-out') && !row.classList.contains('filtered-out')) {
                    visibleCount++;
                }
            } else {
                row.classList.add('search-hidden');
                hiddenCount++;
            }
        });

        console.log(`Búsqueda global aplicada: "${searchTerm}" - ${visibleCount} resultados encontrados`);
    }
    // Dejar que los filtros predictivos manejen su propia lógic
    // Indicador visual simple en el input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        if (searchTerm === '') {
            searchInput.classList.remove('search-active');
        } else {
            searchInput.classList.add('search-active');
        }
    }
}

// ================================================================================================
// ACCIONES ADICIONALES (ELIMINAR IMAGEN REGISTRO TABLA)
// ================================================================================================
// ======PASO 1: Función principal para eliminar imagen con confirmación visual
function deleteImage(button) {
    console.log('Iniciando proceso de eliminación...');

    const row = button.closest('tr');
    if (!row) {
        showNotification('Error: No se encontró la fila', 'error');
        return;
    }

    // Extraer datos de la fila
    const imageData = extractImageDataFromRow(row);
    if (!imageData) {
        showNotification('Error: No se pudieron extraer los datos de la imagen', 'error');
        return;
    }

    console.log('Datos de imagen extraídos:', imageData);

    // Mostrar confirmación visual
    showDeleteConfirmation(imageData, row);
}

// Función para extraer datos de la fila
function extractImageDataFromRow(row) {
    try {
        const img = row.querySelector('img');
        const ordenSitCell = row.querySelector('[data-column="orden-sit"]');
        const poCell = row.querySelector('[data-column="po"]');
        const ocCell = row.querySelector('[data-column="oc"]');
        const descripcionCell = row.querySelector('[data-column="descripcion"]');
        const tipoCell = row.querySelector('[data-column="tipo-fotografia"]');

        return {
            id: row.dataset.imageId || 'unknown',
            imageUrl: img ? img.src : '',
            imageAlt: img ? img.alt : 'Sin descripción',
            ordenSit: ordenSitCell ? ordenSitCell.textContent.trim() : 'N/A',
            po: poCell ? poCell.textContent.trim() : 'N/A',
            oc: ocCell ? ocCell.textContent.trim() : 'N/A',
            descripcion: descripcionCell ? descripcionCell.textContent.trim() : 'Sin descripción',
            tipo: tipoCell ? tipoCell.textContent.trim() : 'N/A'
        };
    } catch (error) {
        console.error('Error extrayendo datos:', error);
        return null;
    }
}
//======PASO 2: Función para mostrar confirmación de eliminación usando SweetAlert===========
function showDeleteConfirmation(imageData, row) {
    console.log('Mostrando confirmación visual de eliminación');

    // HTML personalizado para el modal
    const htmlContent = `
        <div class="delete-confirmation-container">
            <div class="row">
                <!-- Columna izquierda - Imagen -->
                <div class="col-md-5">
                    <div class="image-preview-container">
                        <div class="image-frame">
                            <img src="${imageData.imageUrl}"
                                 alt="${imageData.imageAlt}"
                                 class="preview-image"
                                 style="width: 100%; height: auto; max-height: 200px; object-fit: cover; border-radius: 8px; border: 2px solid #e9ecef;"
                                 onerror="this.src='https://picsum.photos/id/535/200/300';">
                        </div>
                        <div class="image-info mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Se borrará la imagen y la información asociada.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha - Datos -->
                <div class="col-md-7">
                    <div class="image-details">
                        <h6 class="mb-3 text-danger">
                            Detalles de la Fotografía
                        </h6>

                        <div class="detail-row mb-2">
                            <strong class="detail-label">Orden SIT:</strong>
                            <span class="detail-value">${imageData.ordenSit}</span>
                        </div>

                        <div class="detail-row mb-2">
                            <strong class="detail-label">Tipo:</strong>
                            <span class="detail-value">
                                <span class="badge bg-secondary">${imageData.tipo}</span>
                            </span>
                        </div>

                        <div class="detail-row mb-2">
                            <strong class="detail-label">P.O:</strong>
                            <span class="detail-value">${imageData.po}</span>
                        </div>

                        <div class="detail-row mb-2">
                            <strong class="detail-label">O.C:</strong>
                            <span class="detail-value">${imageData.oc}</span>
                        </div>

                        <div class="detail-row mb-3">
                            <strong class="detail-label">Descripción:</strong>
                            <span class="detail-value text-muted">${imageData.descripcion}</span>
                        </div>

                        <div class="alert alert-warning p-2 mb-0">
                            <small>
                                <i class="fas fa-warning me-1"></i>
                                <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .delete-confirmation-container {
                text-align: left;
                padding: 15px 0;
            }

            .image-frame {
                border: 1px solid #dc3545;
                border-radius: 12px;
                padding: 5px;
                background: white;
                box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
            }

            .detail-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 4px 0;
                border-bottom: 1px solid #f8f9fa;
            }

            .detail-label {
                color: #495057;
                font-size: 0.9rem;
                min-width: 80px;
            }

            .detail-value {
                font-size: 0.9rem;
                color: #212529;
                text-align: right;
                max-width: 150px;
                word-wrap: break-word;
            }

            .image-details {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                border-left: 1px solid #dc3545;
            }

            @media (max-width: 768px) {
                .delete-confirmation-container .col-md-5,
                .delete-confirmation-container .col-md-7 {
                    margin-bottom: 15px;
                }
            }
        </style>
    `;

    // Mostrar SweetAlert con contenido personalizado
    Swal.fire({
        title: '¿Eliminar esta fotografía?',
        html: htmlContent,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Sí, eliminar foto',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
        reverseButtons: true,
        focusCancel: true,
        width: '600px',
        customClass: {
            popup: 'delete-confirmation-popup',
            title: 'delete-confirmation-title',
            htmlContainer: 'delete-confirmation-content'
        },
        showClass: {
            popup: 'animate__animated animate__zoomIn animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__zoomOut animate__faster'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Usuario confirmó eliminación');
            performImageDeletion(imageData, row);
        } else {
            console.log('Usuario canceló eliminación');
        }
    });
}
//======PASO 3: Función para ejecutar la eliminación de la imagen===========
// Función para ejecutar la eliminación real
function performImageDeletion(imageData, row) {
    console.log('Ejecutando eliminación de imagen:', imageData.id);

    // Mostrar loading
    Swal.fire({
        title: 'Eliminando fotografía...',
        html: `
            <div class="text-center">
                <div class="spinner-border text-danger mb-3" role="status">
                    <span class="visually-hidden">Eliminando...</span>
                </div>
                <p class="text-muted">Procesando eliminación de la imagen</p>
                <small class="text-muted">Orden SIT: ${imageData.ordenSit}</small>
            </div>
        `,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: {
            popup: 'loading-popup'
        }
    });

    // ====>>>> Detectar si es imagen de Backend o Frontend ====>>>
    if (imageData.isBackendImage && imageData.backendId) {
        // ===== ELIMINACIÓN DE BACKEND CON AJAX =====
        console.log('Eliminando imagen del backend con ID:', imageData.backendId);

        $.ajax({
            url: `/api/fotografias/${imageData.backendId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function (response) {
                console.log('Respuesta del backend:', response);

                if (response.success) {
                    // Eliminar fila de la tabla con animación
                    if (row && row.parentNode) {
                        row.style.transition = 'all 0.5s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-100%)';
                        row.style.backgroundColor = '#f8d7da';

                        setTimeout(() => {
                            row.remove();
                            console.log('Fila eliminada del DOM');

                            // Actualizar filtros si existen
                            if (typeof refreshPredictiveFiltersData === 'function') {
                                refreshPredictiveFiltersData();
                            }
                        }, 500);
                    }

                    // Mostrar confirmación de éxito
                    Swal.fire({
                        title: '¡Eliminada correctamente!',
                        html: `
                            <div class="text-center">
                                <p>La fotografía ha sido eliminada del servidor</p>
                                <small class="text-muted">Orden SIT: ${imageData.ordenSit} | ID: ${imageData.backendId}</small>
                            </div>
                        `,
                        icon: 'success',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    console.log('Imagen backend eliminada exitosamente');

                } else {
                    throw new Error(response.message || 'Error del servidor');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error AJAX eliminando imagen:', error);
                console.error('Response:', xhr.responseText);

                let errorMessage = 'Error de conexión con el servidor';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMessage = 'La imagen ya no existe en el servidor';
                } else if (xhr.status === 403) {
                    errorMessage = 'No tiene permisos para eliminar esta imagen';
                }

                Swal.fire({
                    title: 'Error al eliminar',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                            <p>${errorMessage}</p>
                            <small class="text-muted">ID: ${imageData.backendId}</small>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc3545'
                });
            }
        });

    } else {

        // ===== ELIMINACION DE FRONTEND =====
        console.log('Eliminando imagen del frontend/localStorage');

        setTimeout(() => {
            try {
                // Eliminar fila de la tabla
                if (row && row.parentNode) {
                    // Animación de salida
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-100%)';
                    row.style.backgroundColor = '#f8d7da';

                    setTimeout(() => {
                        row.remove();
                        console.log('Fila eliminada del DOM');

                        // Actualizar cards móviles si existen
                        if (window.responsiveSystem) {
                            window.responsiveSystem.refresh();
                        }

                        // Actualizar contadores de filtros
                        if (typeof updateFilterStatusIndicator === 'function') {
                            updateFilterStatusIndicator();
                        }
                    }, 500);
                }

                // Limpiar de localStorage si existe
                try {
                    const savedImages = localStorage.getItem('newUploadedImages');
                    if (savedImages) {
                        const imagesData = JSON.parse(savedImages);
                        if (imagesData.images) {
                            imagesData.images = imagesData.images.filter(img => img.id !== imageData.id);
                            localStorage.setItem('newUploadedImages', JSON.stringify(imagesData));
                        }
                    }
                } catch (error) {
                    console.warn('Error limpiando localStorage:', error);
                }

                // Mostrar confirmación de éxito
                Swal.fire({
                    title: '¡Eliminada correctamente!',
                    html: `
                    <div class="text-center">
                        <p>La fotografía ha sido eliminada exitosamente</p>
                        <small class="text-muted">Orden SIT: ${imageData.ordenSit} | Tipo: ${imageData.tipo}</small>
                    </div>
                `,
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: false,
                    customClass: {
                        popup: 'success-popup'
                    }
                });

                console.log('Imagen frontend eliminada exitosamente');

            } catch (error) {
                console.error('Error eliminando imagen frontend:', error);

                Swal.fire({
                    title: 'Error al eliminar',
                    html: `
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <p>No se pudo eliminar la fotografía</p>
                        <small class="text-muted">Error: ${error.message || 'Error desconocido'}</small>
                    </div>
                `,
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc3545'
                });
            }
        }, 2000); // Simular delay de 2 segundos
    }
}

// ================================================================================================
// FUNCIONALIDAD BTN EDITAR INFORMACION --> fotos-index
// ================================================================================================

// Variables globales para el editor
let editCropper = null;
let originalImageSrc = null;
let currentEditingRow = null;
let hasImageBeenCropped = false;
let selectedPhotos = [];

// ===== FUNCIÓN PRINCIPAL PARA EDITAR IMAGEN =====
function editImage(button) {
    const row = button.closest('tr');
    if (!row) {
        showNotification('Error: No se encontró la fila', 'error');
        return;
    }

    // Guardar referencia a la fila actual
    currentEditingRow = row;

    // Extraer datos de la fila
    const imageData = extractEditImageData(row);

    if (!imageData) {
        showNotification('Error: No se pudieron extraer los datos de la imagen', 'error');
        return;
    }

    // Guardar datos actuales
    currentImageData = imageData;

    // Llenar el modal con los datos
    populateEditModal(imageData);

    // Reset estado de recorte y fotos múltiples
    hasImageBeenCropped = false;
    selectedPhotos = [];
    updateResetButtonState();
    clearMultiplePhotosContainer();

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('editImageModal'));
    modal.show();
}

// ===== EXTRAER DATOS DE LA IMAGEN DESDE LA FILA =====
function extractEditImageData(row) {
    try {
        const img = row.querySelector('img');
        const ordenSitCell = row.querySelector('[data-column="orden-sit"]');
        const poCell = row.querySelector('[data-column="po"]');
        const ocCell = row.querySelector('[data-column="oc"]');
        const descripcionCell = row.querySelector('[data-column="descripcion"]');
        const tipoCell = row.querySelector('[data-column="tipo-fotografia"]');

        return {
            id: row.dataset.imageId || 'temp_' + Date.now(),
            imageUrl: img ? img.src : '',
            imageAlt: img ? img.alt : '',
            ordenSit: ordenSitCell ? ordenSitCell.textContent.trim() : '',
            po: poCell ? poCell.textContent.trim() : '',
            oc: ocCell ? ocCell.textContent.trim() : '',
            descripcion: descripcionCell ? descripcionCell.textContent.trim() : '',
            tipo: tipoCell ? tipoCell.textContent.trim() : '',
            fechaSubida: new Date().toLocaleDateString('es-ES')
        };
    } catch (error) {
        console.error('Error extrayendo datos:', error);
        return null;
    }
}

// ===== LLENAR EL MODAL CON LOS DATOS =====
function populateEditModal(imageData) {
    // Imagen
    const modalImage = document.getElementById('editModalImage');
    modalImage.src = imageData.imageUrl;
    originalImageSrc = imageData.imageUrl;

    // Campos del formulario
    document.getElementById('editImageId').value = imageData.id;
    document.getElementById('editTipoFotografia').value = imageData.tipo;
    document.getElementById('editDescripcion').value = imageData.descripcion;

    // Información de solo lectura
    document.getElementById('editOrdenSit').value = imageData.ordenSit;
    document.getElementById('editPO').value = imageData.po;
    document.getElementById('editOC').value = imageData.oc;
    document.getElementById('editFechaSubida').value = imageData.fechaSubida;

    console.log('Modal populado con datos:', imageData);
}

// ===== FUNCIONES PARA MÚLTIPLES FOTOS =====

// Función para limpiar container de fotos múltiples
function clearMultiplePhotosContainer() {
    const container = document.getElementById('multiplePhotosContainer');
    const uploadInfo = document.getElementById('uploadInfo');

    if (container) {
        container.innerHTML = '';
    }
    if (uploadInfo) {
        uploadInfo.classList.add('d-none');
    }
}

// Función para procesar múltiples archivos
function processMultipleFiles(files) {
    const container = document.getElementById('multiplePhotosContainer');

    // Limpiar selecciones anteriores
    selectedPhotos = [];
    container.innerHTML = '';

    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const photoData = {
                id: 'new_' + Date.now() + '_' + index,
                file: file,
                url: e.target.result,
                name: file.name,
                size: file.size,
                isNew: true
            };

            selectedPhotos.push(photoData);
            createPhotoPreview(photoData, container);

            // Actualizar información
            updateUploadInfo();

            // Si es la primera foto, mostrarla en el preview principal
            if (index === 0) {
                document.getElementById('editModalImage').src = e.target.result;
                hasImageBeenCropped = true;
                updateResetButtonState();
            }
        };
        reader.readAsDataURL(file);
    });
}

// Crear preview de foto individual
function createPhotoPreview(photoData, container) {
    const photoDiv = document.createElement('div');
    photoDiv.className = 'photo-preview-item';
    photoDiv.dataset.photoId = photoData.id;

    photoDiv.innerHTML = `
        <div class="photo-preview-card">
            <div class="photo-preview-image">
                <img src="${photoData.url}" alt="${photoData.name}" onclick="selectPhotoForEdit('${photoData.id}')">
                <div class="photo-preview-overlay">
                    <button type="button" class="btn-photo-remove" onclick="removePhotoPreview('${photoData.id}')" title="Eliminar esta foto">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="photo-preview-info">
                <div class="photo-name">${photoData.name}</div>
                <div class="photo-size">${formatFileSize(photoData.size)}</div>
                ${photoData.isNew ? '<div class="photo-status new">Nueva</div>' : ''}
            </div>
        </div>
    `;

    container.appendChild(photoDiv);
}

// Actualizar información de upload
function updateUploadInfo() {
    const uploadInfo = document.getElementById('uploadInfo');
    const uploadInfoText = document.getElementById('uploadInfoText');

    if (selectedPhotos.length > 0) {
        uploadInfo.classList.remove('d-none');
        uploadInfoText.textContent = `Fotos seleccionadas: ${selectedPhotos.length}`;
    } else {
        uploadInfo.classList.add('d-none');
    }
}

// Seleccionar foto para editar
function selectPhotoForEdit(photoId) {
    const photo = selectedPhotos.find(p => p.id === photoId);
    if (photo) {
        document.getElementById('editModalImage').src = photo.url;

        // Actualizar preview cards
        document.querySelectorAll('.photo-preview-item').forEach(item => {
            item.classList.remove('selected');
        });

        const selectedItem = document.querySelector(`[data-photo-id="${photoId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('selected');
        }
    }
}

// Remover preview de foto
function removePhotoPreview(photoId) {
    selectedPhotos = selectedPhotos.filter(p => p.id !== photoId);

    const photoElement = document.querySelector(`[data-photo-id="${photoId}"]`);
    if (photoElement) {
        photoElement.remove();
    }

    updateUploadInfo();

    // Si no quedan fotos, restaurar imagen original
    if (selectedPhotos.length === 0) {
        document.getElementById('editModalImage').src = originalImageSrc;
        hasImageBeenCropped = false;
        updateResetButtonState();
    } else {
        // Mostrar la primera foto disponible
        selectPhotoForEdit(selectedPhotos[0].id);
    }
}

// Formatear tamaño de archivo
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Crear filas adicionales para fotos múltiples
function createAdditionalRows(additionalPhotos, tipo, descripcion) {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody || !currentImageData) return;

    additionalPhotos.forEach((photo, index) => {
        const newRowData = {
            ...currentImageData,
            id: 'new_' + Date.now() + '_' + index,
            url: photo.url,
            tipoFotografia: tipo,
            descripcion: descripcion
        };

        setTimeout(() => {
            addImageToTable(newRowData);
        }, (index + 1) * 200);
    });
}

// ===== FUNCIONALIDAD DE RECORTE =====

// Inicializar funcionalidad de recorte
function initializeCropTool() {
    const cropBtn = document.getElementById('cropImageBtn');
    const applyCropBtn = document.getElementById('applyCropBtn');
    const cancelCropBtn = document.getElementById('cancelCropBtn');
    const resetBtn = document.getElementById('resetImageBtn');
    const cropControls = document.getElementById('cropControls');
    const imageTools = document.querySelector('.image-tools .btn-group');

    // Verificar que los elementos existen
    if (!cropBtn || !applyCropBtn || !cancelCropBtn || !resetBtn) {
        console.warn('Algunos elementos de recorte no se encontraron');
        return;
    }

    // Botón de recorte
    cropBtn.addEventListener('click', function () {
        const image = document.getElementById('editModalImage');

        if (editCropper) {
            editCropper.destroy();
        }

        // Inicializar Cropper.js
        editCropper = new Cropper(image, {
            aspectRatio: NaN,
            viewMode: 2,
            responsive: true,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            background: false,
            modal: true
        });

        // Mostrar controles de recorte
        if (imageTools) imageTools.classList.add('d-none');
        if (cropControls) cropControls.classList.remove('d-none');
    });

    // Aplicar recorte
    applyCropBtn.addEventListener('click', function () {
        if (editCropper) {
            const canvas = editCropper.getCroppedCanvas({
                width: 800,
                height: 600,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            // Convertir recorte a base64 permanente
            const croppedBase64 = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('editModalImage').src = croppedBase64;

            // ===== Actualizar Imagen en tabla inmediatamente =====
            if (currentEditingRow) {
                const tableImage = currentEditingRow.querySelector('img');
                if (tableImage) {
                    const descripcionActual = document.getElementById('editDescripcion').value.trim() ||
                        currentEditingRow.querySelector('[data-column="descripcion"]')?.textContent.trim() ||
                        'Imagen recortada';
                    const nuevoTipo = document.getElementById('editTipoFotografia').value || 'MUESTRA';

                    // Crear nueva imagen con base64
                    const newTableImage = tableImage.cloneNode(true);
                    newTableImage.src = croppedBase64; //  BASE64 del recorte
                    newTableImage.alt = 'Imagen recortada';
                    newTableImage.title = 'Imagen recortada';

                    newTableImage.classList.remove('default-image');
                    newTableImage.style.opacity = '1';
                    newTableImage.removeAttribute('onclick');

                    // Event listener con base64 del recorte
                    newTableImage.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        console.log('Click en imagen recortada');
                        openImageLightbox(croppedBase64, 'Imagen recortada', descripcionActual, nuevoTipo);
                    });

                    // Reemplazar imagen
                    tableImage.parentNode.replaceChild(newTableImage, tableImage);
                    console.log('Imagen recortada aplicada a tabla con base64');
                }
            }

            // Actualizar datos globales
            if (currentImageData) {
                currentImageData.url = croppedBase64;
                currentImageData.nombre = 'imagen_recortada.jpg';
            }

            // Marcar que la imagen ha sido recortada
            hasImageBeenCropped = true;
            updateResetButtonState();

            // Destruir cropper
            editCropper.destroy();
            editCropper = null;

            // Ocultar controles de recorte
            if (cropControls) cropControls.classList.add('d-none');
            if (imageTools) imageTools.classList.remove('d-none');
        }
    });

    // Cancelar recorte
    cancelCropBtn.addEventListener('click', function () {
        if (editCropper) {
            editCropper.destroy();
            editCropper = null;
        }

        // Ocultar controles de recorte
        if (cropControls) cropControls.classList.add('d-none');
        if (imageTools) imageTools.classList.remove('d-none');
    });

    // Botón de restablecer
    resetBtn.addEventListener('click', function () {
        if (hasImageBeenCropped && originalImageSrc) {
            Swal.fire({
                title: '¿Restablecer imagen?',
                text: 'Se perderán los cambios de recorte realizados',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, restablecer',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Restablecer imagen original
                    document.getElementById('editModalImage').src = originalImageSrc;
                    hasImageBeenCropped = false;
                    updateResetButtonState();

                    // Limpiar múltiples fotos
                    clearMultiplePhotosContainer();
                    selectedPhotos = [];

                }
            });
        }
    });
}

// Actualizar estado del botón restablecer
function updateResetButtonState() {
    const resetBtn = document.getElementById('resetImageBtn');
    if (resetBtn) {
        if (hasImageBeenCropped) {
            resetBtn.disabled = false;
            resetBtn.classList.remove('btn-outline-secondary');
            resetBtn.classList.add('btn-outline-warning');
            resetBtn.title = 'Restablecer imagen original';
        } else {
            resetBtn.disabled = true;
            resetBtn.classList.remove('btn-outline-warning');
            resetBtn.classList.add('btn-outline-secondary');
            resetBtn.title = 'Sin cambios que restablecer';
        }
    }
}
/* ======================================================================= */

// =====>>> Manejar subida de fotos a traves de cámara en el BTN de modal editar información =====>>>
function initializePhotoUpload() {
    const uploadBtn = document.getElementById('uploadNewPhotoBtn');
    const cameraBtn = document.getElementById('takeCameraPhotoBtn');
    const fileInput = document.getElementById('newPhotoInput');
    const cameraInput = document.getElementById('newCameraInput');

    if (!uploadBtn || !cameraBtn || !fileInput || !cameraInput) {
        console.warn('Elementos de subida no encontrados');
        return;
    }
    // ==== Boton subir desde archivo ====
    uploadBtn.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function (e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        processUploadedFiles(files, 'file');
    });

    cameraBtn.addEventListener('click', function () {
        cameraInput.click();
    });

    cameraInput.addEventListener('change', function (e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        console.log(`${files.length} foto(s) tomada(s) con cámara`);
        processUploadedFiles(files, 'camera');
    });
}

/* =========================================================================== */
// ===== FUNCIÓN PARA PROCESAR ARCHIVOS SUBIDOS =====
function processUploadedFiles(files, source) {
    console.log(`Procesando ${files.length} archivo(s) desde ${source}`);

    // Validar archivos
    const validFiles = files.filter(file => {
        if (!file.type.startsWith('image/')) {
            showNotification(`"${file.name}" no es una imagen válida`, 'error');
            return false;
        }
        if (file.size > 10 * 1024 * 1024) {
            showNotification(`"${file.name}" es demasiado grande (máx 10MB)`, 'error');
            return false;
        }
        return true;
    });

    if (validFiles.length === 0) {
        console.log('No hay archivos válidos para procesar');
        return;
    }

    // Validación: Solo una imagen en modal de edición
    if (validFiles.length > 1) {
        showNotification('Solo se permite subir una imagen a la vez en el modo de edición', 'warning');
        return;
    }

    // Procesar imagen única
    const file = validFiles[0];
    console.log(`Procesando imagen desde ${source}:`, file.name);

    // Mostrar información del método de captura
    showCaptureMethodInfo(source, file);

    // Leer archivo como base64
    const reader = new FileReader();
    reader.onload = function (e) {
        const base64Image = e.target.result;

        // Actualizar imagen en el modal
        const modalImage = document.getElementById('editModalImage');
        if (modalImage) {
            modalImage.src = base64Image;
            console.log('Imagen del modal actualizada');
        }

        // Actualizar imagen en la tabla si hay fila actual
        updateCurrentRowImageFromUpload(base64Image, file, source);

        // Actualizar datos globales
        if (currentImageData) {
            currentImageData.url = base64Image;
            currentImageData.nombre = file.name;
            currentImageData.size = file.size;
            currentImageData.source = source;
        }

        hasImageBeenCropped = true;
        updateResetButtonState();
    };

    reader.onerror = function () {
        showNotification('Error al leer el archivo de imagen', 'error');
    };

    reader.readAsDataURL(file);
}

/* =========================================================================== */
// ===== FUNCIÓN PARA MOSTRAR INFORMACIÓN DEL MÉTODO DE CAPTURA =====
function showCaptureMethodInfo(source, file) {
    const infoContainer = document.getElementById('captureMethodInfo');
    const captureIcon = document.getElementById('captureIcon');
    const captureText = document.getElementById('captureText');

    if (!infoContainer || !captureIcon || !captureText) return;

    // Configurar información según el método
    if (source === 'camera') {
        captureIcon.className = 'fas fa-camera text-success me-2';
        captureText.textContent = `Foto tomada con cámara: ${file.name}`;
        infoContainer.classList.remove('d-none');
    } else if (source === 'file') {
        captureIcon.className = 'fas fa-folder text-primary me-2';
        captureText.textContent = `Archivo seleccionado: ${file.name}`;
        infoContainer.classList.remove('d-none');
    }

    console.log(`Información de captura mostrada: ${source}`);
}

// ===== FUNCIÓN PARA ACTUALIZAR IMAGEN EN TABLA DESDE SUBIDA =====
function updateCurrentRowImageFromUpload(base64Image, file, source) {
    if (!currentEditingRow) return;

    console.log(`Actualizando imagen en tabla desde ${source}...`);

    const tableImage = currentEditingRow.querySelector('img');
    if (!tableImage) return;

    // Obtener datos actuales del formulario
    const descripcionInput = document.getElementById('editDescripcion');
    const tipoInput = document.getElementById('editTipoFotografia');

    const descripcionActual = descripcionInput ? descripcionInput.value.trim() : '';
    const tipoActual = tipoInput ? tipoInput.value : '';

    // Usar descripción existente si no hay nueva
    const finalDescripcion = descripcionActual ||
        currentEditingRow.querySelector('[data-column="descripcion"]')?.textContent.trim() ||
        'Imagen actualizada';

    const finalTipo = tipoActual ||
        currentEditingRow.querySelector('[data-column="tipo-fotografia"]')?.textContent.trim() ||
        'MUESTRA';

    // Crear nueva imagen completamente
    const newTableImage = document.createElement('img');
    newTableImage.src = base64Image;
    newTableImage.alt = file.name || finalDescripcion;
    newTableImage.title = `${source === 'camera' ? 'Foto tomada' : 'Archivo subido'} - ${file.name}`;
    newTableImage.className = tableImage.className;
    newTableImage.style.cssText = tableImage.style.cssText;

    // Limpiar clases de imagen por defecto
    newTableImage.classList.remove('default-image');
    newTableImage.style.opacity = '1';

    // Event listener con base64 y información de origen
    newTableImage.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        console.log(`Click en imagen desde ${source}`);
        openImageLightbox(base64Image, file.name || finalDescripcion, finalDescripcion, finalTipo);
    });

    // Reemplazar imagen en la tabla
    tableImage.parentNode.replaceChild(newTableImage, tableImage);

    // Animación visual para confirmar actualización
    newTableImage.style.backgroundColor = source === 'camera' ? '#d1f2eb' : '#d4edda';
    newTableImage.style.transition = 'background-color 0.5s ease';
    setTimeout(() => {
        newTableImage.style.backgroundColor = '';
    }, 1500);

    console.log(`Imagen en tabla actualizada desde ${source}`);
}

/* =========================================================================== */
// ===== FUNCIÓN PARA LIMPIAR INFORMACIÓN DE CAPTURA =====
function clearCaptureMethodInfo() {
    const infoContainer = document.getElementById('captureMethodInfo');
    if (infoContainer) {
        infoContainer.classList.add('d-none');
    }

    // Limpiar inputs
    const fileInput = document.getElementById('newPhotoInput');
    const cameraInput = document.getElementById('newCameraInput');
    if (fileInput) fileInput.value = '';
    if (cameraInput) cameraInput.value = '';

    console.log('Información de captura limpiada');
}

// ===== MEJORAR FUNCIÓN DE RESET =====
function updateResetButtonState() {
    const resetBtn = document.getElementById('resetImageBtn');
    if (resetBtn) {
        if (hasImageBeenCropped) {
            resetBtn.disabled = false;
            resetBtn.classList.remove('btn-outline-secondary');
            resetBtn.classList.add('btn-outline-warning');
            resetBtn.title = 'Restablecer imagen original';
        } else {
            resetBtn.disabled = true;
            resetBtn.classList.remove('btn-outline-warning');
            resetBtn.classList.add('btn-outline-secondary');
            resetBtn.title = 'Sin cambios que restablecer';
        }
    }
}

// ===== MEJORAR FUNCIÓN DE RESET VARIABLES =====
function resetEditVariables() {
    currentEditingRow = null;
    currentImageData = null;
    hasImageBeenCropped = false;
    originalImageSrc = null;
    selectedPhotos = [];

    // Limpiar información de captura
    clearCaptureMethodInfo();

    if (editCropper) {
        editCropper.destroy();
        editCropper = null;
    }

    console.log('Variables de edición reseteadas');
}

/* =========================================================================== */

// ===== FUNCIONALIDAD DE ELIMINACIÓN =====

// Función para borrar solo la foto actual (no la fila)
function initializePhotoDelete() {
    const deleteBtn = document.getElementById('deletePhotoBtn');

    if (!deleteBtn) {
        console.warn('Botón de eliminar no encontrado');
        return;
    }

    deleteBtn.addEventListener('click', function () {
        Swal.fire({
            title: '¿Eliminar esta fotografía?',
            text: 'Esta acción eliminará solo esta imagen, no toda la información',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar foto',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteCurrentPhotoOnly();
            }
        });
    });
}

// Eliminar solo la foto actual (no toda la fila)
function deleteCurrentPhotoOnly() {
    if (currentEditingRow && currentImageData) {
        // Mostrar loading
        const deleteBtn = document.getElementById('deletePhotoBtn');
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Eliminando...';
        deleteBtn.disabled = true;

        // Simular eliminación de la foto
        setTimeout(() => {
            // Actualizar la imagen en la tabla con una imagen por defecto
            const img = currentEditingRow.querySelector('img');
            if (img) {
                const defaultImage = getDefaultImageByType(currentImageData.tipo);
                const descripcionCell = currentEditingRow.querySelector('[data-column="descripcion"]');
                const tipoCell = currentEditingRow.querySelector('[data-column="tipo-fotografia"]');

                const descripcion = descripcionCell ? descripcionCell.textContent.trim() : 'Imagen eliminada';
                const tipo = tipoCell ? tipoCell.textContent.trim() : currentImageData.tipo;

                // ===== CREAR IMAGEN COMPLETAMENTE NUEVA =====
                const newDefaultImage = document.createElement('img');
                newDefaultImage.src = defaultImage;
                newDefaultImage.alt = 'Imagen eliminada';
                newDefaultImage.title = 'Imagen eliminada - Mostrando imagen por defecto';
                newDefaultImage.className = img.className; // Copiar clases CSS
                newDefaultImage.classList.add('default-image');
                newDefaultImage.style.opacity = '0.7';

                // ===== SOLO event listener para imagen por defecto =====
                newDefaultImage.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    console.log('Click en imagen por defecto:', defaultImage);
                    openImageLightbox(defaultImage, 'Imagen eliminada', descripcion, tipo);
                });

                // Reemplazar imagen anterior
                img.parentNode.replaceChild(newDefaultImage, img);

                console.log('Imagen por defecto configurada correctamente');
            }

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editImageModal'));
            if (modal) {
                modal.hide();
            }

            // Reset variables
            resetEditVariables();

            // Reset botón
            deleteBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Borrar Esta Foto';
            deleteBtn.disabled = false;
        }, 1500);
    }
}

// ===== FUNCIONALIDAD DE GUARDADO =====

// Guardar cambios (mejorado para fotos)
function saveImageChanges() {
    const newTipo = document.getElementById('editTipoFotografia').value;
    const newDescripcion = document.getElementById('editDescripcion').value;

    // Validar campos requeridos
    if (!newTipo || !newDescripcion.trim()) {
        showNotification('Por favor complete todos los campos requeridos', 'error');
        return;
    }

    // Mostrar loading
    const saveBtn = document.getElementById('saveChangesBtn');
    saveBtn.classList.add('loading');
    saveBtn.disabled = true;

    // Determinar qué imagen usar
    let finalImageSrc = document.getElementById('editModalImage').src;
    let hasNewImages = selectedPhotos.length > 0;

    // Simular guardado
    setTimeout(() => {
        // Si hay múltiples fotos nuevas, crear nuevas filas
        if (hasNewImages && selectedPhotos.length > 1) {
            createAdditionalRows(selectedPhotos.slice(1), newTipo, newDescripcion);
        }

        // Actualizar la fila actual
        updateTableRow(currentEditingRow, {
            tipo_fotografia: newTipo,
            descripcion: newDescripcion,
            nueva_imagen: finalImageSrc !== originalImageSrc,
            imagen_src: finalImageSrc
        });

        // Actualizar datos para el lightbox
        updateLightboxData(newTipo, newDescripcion, finalImageSrc);

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editImageModal'));
        modal.hide();

        //let message = 'Cambios guardados correctamente';
        if (hasNewImages && selectedPhotos.length > 1) {
            message += ` (${selectedPhotos.length} fotos procesadas)`;
        }

        // Reset variables
        resetEditVariables();

        // Reset botón
        saveBtn.classList.remove('loading');
        saveBtn.disabled = false;
    }, 1500);
}

// Actualizar datos para el lightbox
function updateLightboxData(newTipo, newDescripcion, newImageSrc) {
    if (currentImageData) {
        // Actualizar datos globales
        currentImageData.tipo = newTipo;
        currentImageData.descripcion = newDescripcion;
        currentImageData.imageUrl = newImageSrc;

        // Si el lightbox está abierto, actualizarlo
        const lightbox = document.getElementById('imageLightbox');
        if (lightbox && lightbox.style.display !== 'none') {
            document.getElementById('lightboxImage').src = newImageSrc;
            document.getElementById('lightboxDescription').textContent = newDescripcion;
            document.getElementById('lightboxType').textContent = newTipo;
        }
    }
}

// Actualizar fila de la tabla
function updateTableRow(row, formData) {
    if (!row) return;

    // Actualizar imagen si cambió
    const img = row.querySelector('img');
    if (img && formData.nueva_imagen) {
        img.src = formData.imagen_src;
        // El onclick ya usa openImageLightboxFromRow(this) que es correcto
    }

    // Actualizar descripción
    const descripcionCell = row.querySelector('[data-column="descripcion"]');
    if (descripcionCell) {
        descripcionCell.textContent = formData.descripcion;
    }

    // Actualizar tipo
    const tipoCell = row.querySelector('[data-column="tipo-fotografia"]');
    if (tipoCell) {
        tipoCell.textContent = formData.tipo_fotografia;
    }

    // Animación de actualización
    row.style.backgroundColor = '#d4edda';
    row.style.transition = 'background-color 0.3s ease';
    setTimeout(() => {
        row.style.backgroundColor = '';
    }, 2000);
}

// ===== FUNCIONES AUXILIARES =====

// Reset variables del editor
function resetEditVariables() {
    currentEditingRow = null;
    currentImageData = null;
    hasImageBeenCropped = false;
    originalImageSrc = null;
    selectedPhotos = [];

    if (editCropper) {
        editCropper.destroy();
        editCropper = null;
    }
}

// ===== INICIALIZACIÓN =====

// Event listeners principales
document.addEventListener('DOMContentLoaded', function () {
    // Verificar que los elementos existen antes de inicializar
    const editModal = document.getElementById('editImageModal');
    const saveBtn = document.getElementById('saveChangesBtn');

    if (!editModal || !saveBtn) {
        console.warn('Elementos del modal de edición no encontrados');
        return;
    }

    // Event listener para guardar cambios
    saveBtn.addEventListener('click', saveImageChanges);

    // Event listener para limpiar variables al cerrar modal
    editModal.addEventListener('hidden.bs.modal', function () {
        resetEditVariables();
    });

    // Inicializar herramientas
    initializeCropTool();
    initializePhotoUpload();
    initializePhotoDelete();

    console.log('Editor de imágenes múltiples inicializado correctamente');
});

// ===== FUNCIONES GLOBALES =====

// Hacer funciones globales para ser llamadas desde HTML
window.editImage = editImage;
window.selectPhotoForEdit = selectPhotoForEdit;
window.removePhotoPreview = removePhotoPreview;

console.log('Módulo de edición de imágenes cargado');

// ================================================================================================
// SISTEMA DE HISTORIAL
// ================================================================================================

function openHistorialModal(button) {
    console.log('Abriendo modal de historial...');

    const row = button.closest('tr');
    if (!row) {
        showNotification('Error: No se encontró la fila', 'error');
        return;
    }

    const imageData = extractImageDataFromRow(row);
    if (!imageData) {
        showNotification('Error: No se pudieron extraer datos', 'error');
        return;
    }

    console.log('Datos de imagen para historial:', imageData);
    loadSynchronizedHistorialData(imageData.ordenSit, imageData);

    const modalElement = document.getElementById('historialModal');
    if (modalElement) {
        try {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal de historial abierto');
        } catch (error) {
            console.error('Error abriendo modal:', error);
            showNotification('Error al abrir el historial', 'error');
        }
    }
}

function loadSynchronizedHistorialData(ordenSit, currentImageDataParam = null) {
    console.log('Cargando historial sincronizado para orden:', ordenSit);

    const allImagesFromOrder = collectAllImagesFromOrder(ordenSit);
    console.log('Total imágenes encontradas:', allImagesFromOrder.length);

    const historialData = generateRealHistorialData(ordenSit, allImagesFromOrder, currentImageDataParam);
    console.log('Historial sincronizado generado:', historialData);

    updateSynchronizedProgressSteps(historialData.estados);
    loadSynchronizedPhotosByCategory('muestra', historialData.fotos.muestra);
    loadSynchronizedPhotosByCategory('validacion', historialData.fotos.validacion);
    loadSynchronizedPhotosByCategory('final', historialData.fotos.final);

    console.log('Historial sincronizado cargado correctamente');
}

function collectAllImagesFromOrder(ordenSit) {
    const allImages = [];
    const tableBody = document.getElementById('imagesTableBody');

    if (!tableBody) return allImages;

    const rows = tableBody.querySelectorAll('tr[data-image-id]');
    rows.forEach((row, index) => {
        const ordenCell = row.querySelector('[data-column="orden-sit"]');
        const currentOrdenSit = ordenCell ? ordenCell.textContent.trim() : '';

        if (currentOrdenSit === ordenSit) {
            const imageData = extractImageDataFromRow(row);
            allImages.push({
                ...imageData,
                source: 'table',
                timestamp: Date.now() - (Math.random() * 86400000)
            });
        }
    });

    try {
        const recentData = localStorage.getItem('newUploadedImages');
        if (recentData) {
            const parsed = JSON.parse(recentData);
            if (parsed.images) {
                parsed.images.forEach(img => {
                    if (img.ordenSit === ordenSit) {
                        allImages.push({
                            id: img.id || 'localStorage_' + Date.now(),
                            imageUrl: img.url,
                            imageAlt: img.name || img.descripcion,
                            ordenSit: img.ordenSit,
                            po: img.po,
                            oc: img.oc,
                            descripcion: img.descripcion,
                            tipo: img.tipoFotografia,
                            source: 'localStorage-transfer',
                            timestamp: img.uploadTimestamp || Date.now()
                        });
                    }
                });
            }
        }
    } catch (error) {
        console.warn('Error leyendo localStorage:', error);
    }

    return allImages;
}

function generateRealHistorialData(ordenSit, realImages, currentImage) {
    const imagesByType = {
        muestra: [],
        validacion: [],
        final: []
    };

    const estados = {
        muestra: false,
        validacion: false,
        final: false
    };

    realImages.forEach((imageData, index) => {
        const tipo = imageData.tipo ? imageData.tipo.toUpperCase() : '';
        const imageForHistory = {
            url: imageData.imageUrl,
            fecha: new Date(imageData.timestamp || Date.now()).toISOString(),
            descripcion: imageData.descripcion || imageData.imageAlt || 'Sin descripción',
            ordenSit: imageData.ordenSit,
            po: imageData.po,
            oc: imageData.oc,
            source: imageData.source || 'unknown',
            isReal: true
        };

        if (tipo.includes('MUESTRA')) {
            imagesByType.muestra.push(imageForHistory);
            estados.muestra = true;
        } else if (tipo.includes('VALIDACION') || tipo.includes('VALIDACIÓN')) {
            imagesByType.validacion.push(imageForHistory);
            estados.validacion = true;
        } else if (tipo.includes('FINAL') || tipo.includes('PRENDA FINAL')) {
            imagesByType.final.push(imageForHistory);
            estados.final = true;
        }
    });

    return {
        estados: estados,
        fotos: imagesByType,
        totalImages: realImages.length,
        metadata: {
            ordenSit,
            generatedAt: new Date().toISOString(),
            source: 'real-data-only',
            realImagesOnly: true
        }
    };
}

function updateSynchronizedProgressSteps(estados) {
    const stepMuestra = document.getElementById('stepMuestra');
    const stepValidacion = document.getElementById('stepValidacion');
    const stepFinal = document.getElementById('stepFinal');

    [stepMuestra, stepValidacion, stepFinal].forEach(step => {
        if (step) {
            step.classList.remove('completed', 'pending');
        }
    });

    setTimeout(() => {
        if (stepMuestra) {
            stepMuestra.classList.add(estados.muestra ? 'completed' : 'pending');
        }
    }, 100);

    setTimeout(() => {
        if (stepValidacion) {
            stepValidacion.classList.add(estados.validacion ? 'completed' : 'pending');
        }
    }, 200);

    setTimeout(() => {
        if (stepFinal) {
            stepFinal.classList.add(estados.final ? 'completed' : 'pending');
        }
    }, 300);
}

function loadSynchronizedPhotosByCategory(categoria, fotos) {
    const photosContainer = document.getElementById(`${categoria}Photos`);
    const countBadge = document.getElementById(`${categoria}Count`);

    if (!photosContainer || !countBadge) return;

    countBadge.textContent = `${fotos.length} foto${fotos.length !== 1 ? 's' : ''}`;
    photosContainer.innerHTML = '';

    if (fotos.length === 0) {
        photosContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-image"></i>
                <span>No hay fotografías en esta etapa</span>
            </div>
        `;
    } else {
        const sortedFotos = fotos.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

        sortedFotos.forEach((foto, index) => {
            setTimeout(() => {
                const photoDiv = document.createElement('div');
                photoDiv.className = 'photo-item';
                photoDiv.innerHTML = `
                    <img src="${foto.url}"
                         alt="${foto.descripcion}"
                         title="Descripción: ${foto.descripcion}\n OrdenSIT: ${foto.ordenSit}\n Fuente: ${foto.source}"
                         onclick="openImageLightbox('${foto.url}', '${foto.descripcion}', '${foto.descripcion}', '${categoria.toUpperCase()}')">
                    <div class="photo-date">${formatRealDate(foto.fecha)}</div>
                `;
                photosContainer.appendChild(photoDiv);
            }, index * 100);
        });
    }
}

function formatRealDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// ================================================================================================
// FUNCIONALIDAD DE FILTROS PREDICTIVOS ---> fotos-index
// ================================================================================================
// Variables globales para filtros predictivos
let allTableData = [];
let filteredData = [];
let currentSuggestions = {};
let selectedSuggestionIndex = -1;
let activeFilters = {};
let filterObserver = null;

// ===== INICIALIZACIÓN DE FILTROS PREDICTIVOS =====
function initializePredictiveFilters() {
    console.log('Inicializando filtros predictivos completos...');

    // Recopilar todos los datos de la tabla
    extractTableData();

    // Configurar event listeners para cada filtro
    setupFilterListeners();

    // Configurar observador para cambios en la tabla
    setupTableObserver();

    console.log('Filtros predictivos completos inicializados');
}

// ===== OBSERVADOR DE CAMBIOS EN LA TABLA =====
// FUNCIÓN para limpiar observers globalmente
function cleanupAllObservers() {
    console.log('Limpiando todos los observers...');

    // Limpiar filter observer
    if (window.filterObserver) {
        window.filterObserver.disconnect();
        window.filterObserver = null;
        console.log('Filter observer limpiado');
    }

    // Limpiar otros observers que puedan existir
    if (window.tipoFotografiaObserver) {
        window.tipoFotografiaObserver.disconnect();
        window.tipoFotografiaObserver = null;
        console.log('Tipo fotografía observer limpiado');
    }

    // Limpiar timeouts pendientes
    if (window.observerTimeout) {
        clearTimeout(window.observerTimeout);
        window.observerTimeout = null;
        console.log('Observer timeouts limpiados');
    }
}

// MEJORAR setupTableObserver
function setupTableObserver() {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) {
        console.warn('No se encontró imagesTableBody para observer');
        return;
    }

    // Limpiar observer anterior SIEMPRE
    if (window.filterObserver) {
        console.log('Limpiando observer anterior...');
        window.filterObserver.disconnect();
        window.filterObserver = null;
    }

    console.log('Creando nuevo observer...');
    window.filterObserver = new MutationObserver(function (mutations) {
        let shouldRefresh = false;

        mutations.forEach(function (mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Solo procesar si se agregaron nodos reales
                const hasValidNodes = Array.from(mutation.addedNodes).some(node =>
                    node.nodeType === Node.ELEMENT_NODE && node.tagName === 'TR'
                );
                if (hasValidNodes) {
                    shouldRefresh = true;
                }
            }
        });

        if (shouldRefresh) {
            // Debounce MÁS LARGO para evitar spam
            clearTimeout(window.observerTimeout);
            window.observerTimeout = setTimeout(() => {
                console.log('Observer: Actualizando datos de tabla...');
                extractTableData();
                applyAllFilters();
            }, 500); // Aumentar a 500ms
        }
    });

    window.filterObserver.observe(tableBody, {
        childList: true,
        subtree: false // Reducir scope del observer
    });

    console.log('Observer configurado correctamente');
}

// ===== EXTRAER DATOS DE LA TABLA =====
function extractTableData() {
    allTableData = [];
    const tableBody = document.getElementById('imagesTableBody');

    if (!tableBody) return;

    const rows = tableBody.querySelectorAll('tr[data-image-id]');

    rows.forEach((row, index) => {
        const ordenSitCell = row.querySelector('[data-column="orden-sit"]');
        const poCell = row.querySelector('[data-column="po"]');
        const ocCell = row.querySelector('[data-column="oc"]');
        const descripcionCell = row.querySelector('[data-column="descripcion"]');
        const tipoCell = row.querySelector('[data-column="tipo-fotografia"]');

        const rowData = {
            index: index,
            row: row,
            ordenSit: ordenSitCell ? ordenSitCell.textContent.trim() : '',
            po: poCell ? poCell.textContent.trim() : '',
            oc: ocCell ? ocCell.textContent.trim() : '',
            descripcion: descripcionCell ? descripcionCell.textContent.trim() : '',
            tipo: tipoCell ? tipoCell.textContent.trim() : ''
        };

        allTableData.push(rowData);
    });

    filteredData = [...allTableData];
    console.log(`Datos extraídos: ${allTableData.length} filas (incluyendo nuevas imágenes)`);
}

// ===== CONFIGURAR EVENT LISTENERS =====
function setupFilterListeners() {
    const filterInputs = document.querySelectorAll('.predictive-filter');

    filterInputs.forEach(input => {
        const column = input.dataset.column;
        const suggestionsContainer = document.getElementById(`suggestions${capitalizeFirst(column.replace('-', ''))}`);

        if (!suggestionsContainer) return;

        // Input event - mostrar sugerencias mientras escribe
        input.addEventListener('input', function (e) {
            const query = e.target.value.trim();

            if (query.length >= 1) {
                showSuggestions(column, query, suggestionsContainer, input);
            } else {
                hideSuggestions(suggestionsContainer, input);
                removeActiveFilter(column);
                applyAllFilters();
            }
        });

        // Focus event - mostrar sugerencias si hay texto
        input.addEventListener('focus', function (e) {
            const query = e.target.value.trim();
            if (query.length >= 1) {
                showSuggestions(column, query, suggestionsContainer, input);
            }
        });

        // Blur event - ocultar sugerencias (con delay para permitir clicks)
        input.addEventListener('blur', function (e) {
            setTimeout(() => {
                hideSuggestions(suggestionsContainer, input);
            }, 150);
        });

        // Keyboard navigation
        input.addEventListener('keydown', function (e) {
            handleKeyboardNavigation(e, column, suggestionsContainer, input);
        });
    });

    // Click fuera para cerrar sugerencias
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.autocomplete-wrapper')) {
            hideAllSuggestions();
        }
    });
}

// ===== GENERAR SUGERENCIAS (MEJORADO PARA NÚMEROS Y TEXTO) =====
function generateSuggestions(column, query) {
    const columnMap = {
        'orden-sit': 'ordenSit',
        'po': 'po',
        'oc': 'oc',
        'descripcion': 'descripcion'
    };

    const fieldName = columnMap[column];
    if (!fieldName) return [];

    // Obtener valores únicos que coincidan con la consulta
    const matchingValues = new Map();

    allTableData.forEach(item => {
        const value = item[fieldName];
        if (value && matchesQuery(value, query)) {
            const count = matchingValues.get(value) || 0;
            matchingValues.set(value, count + 1);
        }
    });

    // Convertir a array y ordenar por relevancia
    const suggestions = Array.from(matchingValues.entries()).map(([value, count]) => {
        const relevance = calculateRelevance(value, query);

        return {
            value: value,
            count: count,
            relevance: relevance,
            query: query
        };
    });

    // Ordenar por relevancia y limitar resultados
    return suggestions
        .sort((a, b) => {
            if (b.relevance !== a.relevance) {
                return b.relevance - a.relevance;
            }
            return b.count - a.count;
        })
        .slice(0, 10);
}

// ===== FUNCIÓN MEJORADA PARA COINCIDENCIAS (NÚMEROS Y TEXTO) =====
function matchesQuery(value, query) {
    const lowerValue = value.toLowerCase();
    const lowerQuery = query.toLowerCase();

    // Coincidencia exacta
    if (lowerValue.includes(lowerQuery)) {
        return true;
    }

    // Para números, también verificar coincidencias parciales
    if (isNumeric(query)) {
        // Si la consulta es numérica, buscar en cualquier parte del número
        const valueNumbers = value.match(/\d+/g);
        if (valueNumbers) {
            return valueNumbers.some(num => num.includes(query));
        }
    }

    // Para texto, verificar palabras individuales
    const queryWords = query.split(/\s+/);
    const valueWords = value.toLowerCase().split(/\s+/);

    return queryWords.every(queryWord =>
        valueWords.some(valueWord => valueWord.includes(queryWord))
    );
}

// ===== CALCULAR RELEVANCIA (MEJORADO PARA NÚMEROS) =====
function calculateRelevance(value, query) {
    const lowerValue = value.toLowerCase();
    const lowerQuery = query.toLowerCase();

    // Coincidencia exacta completa
    if (lowerValue === lowerQuery) {
        return 1000;
    }

    // Coincidencia al inicio
    if (lowerValue.startsWith(lowerQuery)) {
        return 500;
    }

    // Para números, alta relevancia si coincide con el inicio de cualquier número
    if (isNumeric(query)) {
        const valueNumbers = value.match(/\d+/g);
        if (valueNumbers) {
            for (let num of valueNumbers) {
                if (num.startsWith(query)) {
                    return 400;
                }
                if (num.includes(query)) {
                    return 200;
                }
            }
        }
    }

    // Coincidencia de palabra completa
    const valueWords = lowerValue.split(/\s+/);
    if (valueWords.includes(lowerQuery)) {
        return 300;
    }

    // Coincidencia al inicio de palabra
    if (valueWords.some(word => word.startsWith(lowerQuery))) {
        return 250;
    }

    // Coincidencia después de espacio
    if (lowerValue.includes(' ' + lowerQuery)) {
        return 150;
    }

    // Coincidencia parcial
    if (lowerValue.includes(lowerQuery)) {
        return 100;
    }

    return 0;
}

// ===== FUNCIÓN AUXILIAR PARA DETECTAR NÚMEROS =====
function isNumeric(str) {
    return /^\d+$/.test(str);
}

// ===== MOSTRAR SUGERENCIAS =====
function showSuggestions(column, query, container, input) {
    const suggestions = generateSuggestions(column, query);

    if (suggestions.length === 0) {
        showEmptySuggestions(container, query);
        return;
    }

    renderSuggestions(suggestions, container, column, query, input);
    container.classList.add('show');
    input.classList.add('has-suggestions');
    selectedSuggestionIndex = -1;
}

// ===== RENDERIZAR SUGERENCIAS (MEJORADO) =====
function renderSuggestions(suggestions, container, column, query, input) {
    const columnNames = {
        'orden-sit': 'Orden SIT',
        'po': 'P.O',
        'oc': 'O.C',
        'descripcion': 'Descripción'
    };

    let html = `
        <div class="suggestions-header">
            ${suggestions.length} ${columnNames[column] || column} encontrada${suggestions.length !== 1 ? 's' : ''}
        </div>
    `;

    suggestions.forEach((suggestion, index) => {
        const highlightedText = highlightQuery(suggestion.value, query);

        html += `
            <div class="suggestion-item"
                 data-value="${escapeHtml(suggestion.value)}"
                 data-index="${index}"
                 onclick="selectSuggestion('${column}', '${escapeHtml(suggestion.value)}', '${container.id}', '${input.id}')">
                <span class="suggestion-text">${highlightedText}</span>
                <span class="suggestion-count">${suggestion.count}</span>
            </div>
        `;
    });

    container.innerHTML = html;
    currentSuggestions[column] = suggestions;
}

// ===== RESALTAR CONSULTA EN TEXTO (MEJORADO PARA NÚMEROS) =====
function highlightQuery(text, query) {
    // Escapar caracteres especiales para regex
    const escapedQuery = escapeRegex(query);

    // Resaltar coincidencias completas
    let highlightedText = text.replace(
        new RegExp(`(${escapedQuery})`, 'gi'),
        '<span class="highlight">$1</span>'
    );

    // Para números, también resaltar dentro de secuencias numéricas
    if (isNumeric(query)) {
        highlightedText = highlightedText.replace(
            new RegExp(`(\\d*)(${escapedQuery})(\\d*)`, 'gi'),
            '$1<span class="highlight">$2</span>$3'
        );
    }

    return highlightedText;
}

// ===== APLICAR FILTROS =====
function applyAllFilters() {
    const hasActiveFilters = Object.keys(activeFilters).length > 0;

    // Verificar si hay búsqueda global activa
    const searchInput = document.getElementById('search-input');
    const hasGlobalSearch = searchInput && searchInput.value.trim() !== '';

    if (!hasActiveFilters && !hasGlobalSearch) {
        // Mostrar todas las filas
        allTableData.forEach(item => {
            if (item.row.parentNode) {  // Verificar que la fila aún exista en el DOM
                item.row.style.display = '';
                item.row.classList.remove('filtered-out', 'search-hidden');
            }
        });
        filteredData = [...allTableData];
        updateFilterStatus();
        return;
    }

    // Filtrar datos considerando solo filtros predictivos
    filteredData = allTableData.filter(item => {
        const passesColumnFilters = Object.entries(activeFilters).every(([column, filterValue]) => {
            const columnMap = {
                'orden-sit': 'ordenSit',
                'po': 'po',
                'oc': 'oc',
                'descripcion': 'descripcion'
            };

            const fieldName = columnMap[column];
            if (!fieldName) return true;

            const itemValue = item[fieldName];

            // Usar la función mejorada de coincidencias
            return matchesQuery(itemValue, filterValue);
        });

        // Verificar si hay búsqueda global activa
        let passesGlobalSearch = true;
        if (hasGlobalSearch) {
            const globalQuery = searchInput.value.trim().toLowerCase();
            const searchableFields = ['item.ordenSit, item.po, item.oc'];
            passesGlobalSearch = searchableFields.some(field => {
                return field && field.toLowerCase().includes(globalQuery);
            });
        }

        return passesColumnFilters && passesGlobalSearch;
    });

    // Mostrar/ocultar filas según filtros
    allTableData.forEach(item => {
        if (item.row.parentNode) {  // Verificar que la fila aún exista en el DOM
            const isVisible = filteredData.includes(item);

            if (isVisible) {
                item.row.style.display = '';
                item.row.classList.remove('filtered-out');
            } else {
                item.row.style.display = 'none';
                item.row.classList.add('filtered-out');
            }
        }
    });

    updateFilterStatus();
    console.log(`Filtros aplicados: ${filteredData.length}/${allTableData.length} filas visibles`);
}

/* ================================================================================================= */
// Limpiar búsqueda global
function clearGlobalSearchOnly() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        searchInput.classList.remove('search-active');
    }

    // Remover SOLO las clases de búsqueda global
    const allRows = document.querySelectorAll('#imagesTableBody tr[data-image-id]');
    allRows.forEach(row => {
        row.classList.remove('search-hidden');
    });

    // Reactivar filtros predictivos
    if (typeof applyAllFilters === 'function') {
        applyAllFilters();
    }

    console.log('Búsqueda global limpiada, filtros predictivos mantienen su estado');
}

// Hacer función global
window.clearGlobalSearchOnly = clearGlobalSearchOnly;

/* ================================================================================================= */
// Función para refrescar filtros manualmente
function refreshFiltersData() {
    console.log('Refrescando datos de filtros...');
    extractTableData();
    applyAllFilters();
}

// ===== FUNCIONES DE NAVEGACIÓN Y SELECCIÓN (mantener las existentes) =====
function handleKeyboardNavigation(e, column, container, input) {
    const suggestions = currentSuggestions[column];
    if (!suggestions || suggestions.length === 0) return;

    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            selectedSuggestionIndex = Math.min(selectedSuggestionIndex + 1, suggestions.length - 1);
            updateSelectedSuggestion(container);
            break;

        case 'ArrowUp':
            e.preventDefault();
            selectedSuggestionIndex = Math.max(selectedSuggestionIndex - 1, -1);
            updateSelectedSuggestion(container);
            break;

        case 'Enter':
            e.preventDefault();
            if (selectedSuggestionIndex >= 0) {
                const selectedSuggestion = suggestions[selectedSuggestionIndex];
                selectSuggestion(column, selectedSuggestion.value, container.id, input.id);
            }
            break;

        case 'Escape':
            hideSuggestions(container, input);
            input.blur();
            break;
    }
}

function updateSelectedSuggestion(container) {
    const items = container.querySelectorAll('.suggestion-item');

    items.forEach((item, index) => {
        if (index === selectedSuggestionIndex) {
            item.classList.add('selected');
            item.scrollIntoView({ block: 'nearest' });
        } else {
            item.classList.remove('selected');
        }
    });
}

function selectSuggestion(column, value, containerId, inputId) {
    const input = document.getElementById(inputId);
    const container = document.getElementById(containerId);

    input.value = value;
    hideSuggestions(container, input);

    // Aplicar filtro
    setActiveFilter(column, value);
    applyAllFilters();

    // Marcar input como activo
    input.classList.add('active');

    console.log(`Filtro aplicado: ${column} = "${value}"`);
}

function setActiveFilter(column, value) {
    if (value && value.trim() !== '') {
        activeFilters[column] = value.trim();
    } else {
        removeActiveFilter(column);
    }
}

function removeActiveFilter(column) {
    delete activeFilters[column];

    // Remover clase activa del input
    const input = document.querySelector(`[data-column="${column}"]`);
    if (input) {
        input.classList.remove('active');
    }
}

function showEmptySuggestions(container, query) {
    container.innerHTML = `
        <div class="suggestions-empty">
            <i class="fas fa-search me-2"></i>
            No se encontraron coincidencias para "${query}"
        </div>
    `;
    container.classList.add('show');
}

function hideSuggestions(container, input) {
    container.classList.remove('show');
    input.classList.remove('has-suggestions');
    selectedSuggestionIndex = -1;
}

function hideAllSuggestions() {
    document.querySelectorAll('.autocomplete-suggestions').forEach(container => {
        container.classList.remove('show');
    });

    document.querySelectorAll('.predictive-filter').forEach(input => {
        input.classList.remove('has-suggestions');
    });

    selectedSuggestionIndex = -1;
}

function updateFilterStatus() {
    const activeFilterCount = Object.keys(activeFilters).length;
    const visibleRows = filteredData.length;
    const totalRows = allTableData.length;

    console.log(`Estado de filtros: ${activeFilterCount} activos, ${visibleRows}/${totalRows} filas visibles`);
}

// ===== FUNCIONES AUXILIARES =====
function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function escapeRegex(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function clearAllFilters() {
    activeFilters = {};

    // Limpiar todos los inputs
    document.querySelectorAll('.predictive-filter').forEach(input => {
        input.value = '';
        input.classList.remove('active');
    });

    hideAllSuggestions();
    applyAllFilters();
}

// ===== INICIALIZACIÓN =====
document.addEventListener('DOMContentLoaded', function () {
    // Esperar un poco para que la tabla esté completamente cargada
    setTimeout(() => {
        initializePredictiveFilters();
    }, 500);
});

// Event listener para actualizar filtros cuando se transfieran imágenes
window.addEventListener('storage', function (e) {
    if (e.key === 'newUploadedImages') {
        setTimeout(() => {
            refreshFiltersData();
        }, 1000);
    }
});

// AGREGAR cleanup global
window.addEventListener('beforeunload', function () {
    console.log('Limpiando sistema antes de salir...');
    cleanupAllObservers();

    // Limpiar cropper si existe
    if (window.editCropper) {
        window.editCropper.destroy();
        window.editCropper = null;
    }

    // Reset flags
    window.fotografiasSystemInitialized = false;
    window.fotografiasSystemInitializing = false;

    console.log('Sistema limpiado completamente');
});

// AGREGAR cleanup cuando se oculta la página
document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        console.log('Página oculta, pausando observers...');
        if (window.filterObserver) {
            window.filterObserver.disconnect();
        }
    } else {
        console.log('Página visible, reactivando observers...');
        // Reinicializar observer si es necesario
        setTimeout(() => {
            if (!window.filterObserver && window.fotografiasSystemInitialized) {
                setupTableObserver();
            }
        }, 1000);
    }
});

// Funciones globales
window.selectSuggestion = selectSuggestion;
window.clearAllFilters = clearAllFilters;
window.refreshFiltersData = refreshFiltersData;

console.log('Sistema de filtros predictivos completo cargado');
// ================================================================================================
// FUNCIONALIDAD DE BÚSQUEDA GLOBAL DINÁMICA - Ord. SIT / P.O / O.C
// ================================================================================================

// Variables para búsqueda global
let globalSearchActive = false;
let globalSearchQuery = '';

// ===== INICIALIZAR BÚSQUEDA GLOBAL DINÁMICA =====
function initializeGlobalSearch() {
    console.log('Inicializando búsqueda global dinámica...');

    const globalSearchInput = document.getElementById('searchInput');
    const globalSearchButton = document.getElementById('searchButton');

    if (!globalSearchInput) {
        console.warn('Campo de búsqueda global no encontrado');
        return;
    }

    // Event listeners para búsqueda dinámica
    setupGlobalSearchListeners(globalSearchInput, globalSearchButton);

    console.log('Búsqueda global dinámica inicializada');
}

// ===== CONFIGURAR EVENT LISTENERS PARA BÚSQUEDA GLOBAL =====
function setupGlobalSearchListeners(searchInput, searchButton) {

    // Input event - filtrar mientras escribe (como en Descripción)
    searchInput.addEventListener('input', function (e) {
        const query = e.target.value.trim();

        if (query.length >= 1) {
            // Actualizar filtros combinados
            if (typeof applyAllFilters === 'function') {
                applyAllFilters();
            }

            // Cambiar estilo del input para indicar filtro activo
            searchInput.classList.add('global-search-active');

            console.log(`Búsqueda global aplicada: "${query}"`);
        } else {
            // Limpiar búsqueda y restaurar tabla
            clearGlobalSearchOnly();
            console.log('Búsqueda global limpiada');
        }
    });

    // Focus event - resaltar campo activo
    searchInput.addEventListener('focus', function (e) {
        searchInput.classList.add('global-search-focused');
    });

    // Blur event - remover resaltado
    searchInput.addEventListener('blur', function (e) {
        searchInput.classList.remove('global-search-focused');
    });

    // Keyboard events
    searchInput.addEventListener('keydown', function (e) {
        switch (e.key) {
            case 'Enter':
                e.preventDefault();
                const query = e.target.value.trim();
                if (query.length >= 1) {
                    applyGlobalSearch(query);
                }
                break;

            case 'Escape':
                clearGlobalSearch();
                searchInput.blur();
                break;
        }
    });

    // Click en botón de búsqueda
    if (searchButton) {
        searchButton.addEventListener('click', function (e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            if (query.length >= 1) {
                applyGlobalSearch(query);
                globalSearchActive = true;
                globalSearchQuery = query;
                searchInput.classList.add('global-search-active');
            }
        });
    }
}

// ===== APLICAR BÚSQUEDA GLOBAL =====
function applyGlobalSearch(query) {
    console.log(`Aplicando búsqueda global para: "${query}"`);

    // Asegurar que los datos de la tabla estén actualizados
    if (typeof extractTableData === 'function') {
        extractTableData();
    }

    // Filtrar datos en múltiples campos
    const globalFilteredData = allTableData.filter(item => {
        const searchableFields = [
            item.ordenSit,
            item.po,
            item.oc
        ];

        // Buscar en cualquiera de los campos numéricos
        return searchableFields.some(field => {
            return field && matchesGlobalQuery(field, query);
        });
    });

    // Mostrar/ocultar filas según resultado de búsqueda global
    allTableData.forEach(item => {
        if (item.row.parentNode) {
            const isVisible = globalFilteredData.includes(item);
            item.row.style.display = isVisible ? '' : 'none';
        }
    });

    // Actualizar estado
    updateGlobalSearchStatus(query, globalFilteredData.length, allTableData.length);
}

// ===== FUNCIÓN DE COINCIDENCIA PARA BÚSQUEDA GLOBAL =====
function matchesGlobalQuery(value, query) {
    if (!value || !query) return false;

    const lowerValue = String(value).toLowerCase();
    const lowerQuery = String(query).toLowerCase();

    // Coincidencia directa
    if (lowerValue.includes(lowerQuery)) {
        return true;
    }

    // Para números, verificar coincidencias parciales más específicas
    if (isNumeric(query)) {
        // Coincidencia exacta de números
        if (lowerValue === lowerQuery) {
            return true;
        }

        // Coincidencia al inicio de número
        if (lowerValue.startsWith(lowerQuery)) {
            return true;
        }

        // Coincidencia de secuencia numérica
        const valueNumbers = value.match(/\d+/g);
        if (valueNumbers) {
            return valueNumbers.some(num => num.includes(query));
        }
    }

    return false;
}

// ===== LIMPIAR BÚSQUEDA GLOBAL =====
function clearGlobalSearch() {
    console.log('Limpiando búsqueda global...');

    // Mostrar todas las filas
    if (allTableData && allTableData.length > 0) {
        allTableData.forEach(item => {
            if (item.row.parentNode) {
                item.row.style.display = '';
            }
        });
    }

    // Limpiar input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        searchInput.classList.remove('global-search-active');
    }

    // Resetear variables
    globalSearchActive = false;
    globalSearchQuery = '';

    // Reactivar otros filtros si los hay
    if (typeof applyAllFilters === 'function') {
        applyAllFilters();
    }

    updateGlobalSearchStatus('', allTableData ? allTableData.length : 0, allTableData ? allTableData.length : 0);
}

// ===== ACTUALIZAR ESTADO DE BÚSQUEDA GLOBAL =====
function updateGlobalSearchStatus(query, visibleCount, totalCount) {
    console.log(`Búsqueda global: "${query}" - ${visibleCount}/${totalCount} resultados`);
}

// ===== INTEGRACIÓN CON FILTROS EXISTENTES =====
function integrateGlobalSearchWithFilters() {
    // Sobrescribir applyAllFilters para considerar búsqueda global
    if (typeof applyAllFilters === 'function') {
        const originalApplyAllFilters = applyAllFilters;

        window.applyAllFilters = function () {
            // Aplicar filtros de columnas normalmente
            originalApplyAllFilters();

            // Si hay búsqueda global activa, aplicarla también
            if (globalSearchActive && globalSearchQuery) {
                applyGlobalSearch(globalSearchQuery);
            }
        };
    }
}

// ===== FUNCIÓN PARA LIMPIAR TODOS LOS FILTROS (INCLUYENDO GLOBAL) =====
function clearAllFiltersIncludingGlobal() {
    // Limpiar búsqueda global
    clearGlobalSearch();

    // Limpiar filtros de columnas
    if (typeof clearAllFilters === 'function') {
        clearAllFilters();
    }

    console.log('Todos los filtros limpiados (global + columnas)');
}

/* ====================================================================================================== */
// Función para refrescar filtros predictivos después de cambios
function refreshPredictiveFiltersData() {
    console.log('Refrescando datos de filtros predictivos...');

    // Limpiar datos anteriores
    allTableData = [];

    // Recolectar NUEVAMENTE todos los datos actualizados de la tabla
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) {
        console.warn('Tabla no encontrada');
        return;
    }

    const rows = tableBody.querySelectorAll('tr[data-image-id]');
    console.log(`Recolectando datos de ${rows.length} filas...`);

    rows.forEach((row, index) => {
        const ordenSitCell = row.querySelector('[data-column="orden-sit"]');
        const poCell = row.querySelector('[data-column="po"]');
        const ocCell = row.querySelector('[data-column="oc"]');
        const descripcionCell = row.querySelector('[data-column="descripcion"]');

        const rowData = {
            row: row,
            ordenSit: ordenSitCell ? ordenSitCell.textContent.trim() : '',
            po: poCell ? poCell.textContent.trim() : '',
            oc: ocCell ? ocCell.textContent.trim() : '',
            descripcion: descripcionCell ? descripcionCell.textContent.trim() : ''
        };

        allTableData.push(rowData);
        console.log(`Fila ${index + 1}: ${JSON.stringify(rowData, null, 2)}`);
    });

    console.log(`${allTableData.length} registros actualizados en filtros predictivos`);

    // Actualizar sugerencias para todos los campos
    updateAllSuggestions();

    // Limpiar filtros activos que ya no son válidos
    validateActiveFilters();
}

// Función para actualizar todas las sugerencias
function updateAllSuggestions() {
    console.log('Actualizando sugerencias de autocompletado...');

    // Obtener valores únicos para cada columna
    const ordenSitValues = [...new Set(allTableData.map(item => item.ordenSit).filter(v => v))];
    const poValues = [...new Set(allTableData.map(item => item.po).filter(v => v))];
    const ocValues = [...new Set(allTableData.map(item => item.oc).filter(v => v))];
    const descripcionValues = [...new Set(allTableData.map(item => item.descripcion).filter(v => v))];

    console.log('Nuevas sugerencias:', {
        ordenSit: ordenSitValues,
        po: poValues,
        oc: ocValues,
        descripcion: descripcionValues
    });

    // Aquí puedes actualizar los arrays de sugerencias si los tienes
    // Por ejemplo, si tienes variables globales para las sugerencias:
    if (typeof window.ordenSitSuggestions !== 'undefined') {
        window.ordenSitSuggestions = ordenSitValues;
    }
    if (typeof window.poSuggestions !== 'undefined') {
        window.poSuggestions = poValues;
    }
    if (typeof window.ocSuggestions !== 'undefined') {
        window.ocSuggestions = ocValues;
    }
    if (typeof window.descripcionSuggestions !== 'undefined') {
        window.descripcionSuggestions = descripcionValues;
    }
}

// Función para validar filtros activos
function validateActiveFilters() {
    console.log('Validando filtros activos...');

    if (!activeFilters || Object.keys(activeFilters).length === 0) {
        console.log('No hay filtros activos que validar');
        return;
    }

    // Verificar cada filtro activo
    Object.keys(activeFilters).forEach(column => {
        const filterValue = activeFilters[column];
        const hasMatchingData = allTableData.some(item => {
            const fieldValue = item[column === 'orden-sit' ? 'ordenSit' : column];
            return fieldValue && fieldValue.toLowerCase().includes(filterValue.toLowerCase());
        });

        if (!hasMatchingData) {
            console.log(`Filtro "${column}: ${filterValue}" ya no tiene datos coincidentes`);
            // Opcional: limpiar automáticamente el filtro
            // delete activeFilters[column];
            // clearSpecificFilter(column);
        }
    });
}
/* ====================================================================================================== */
function clearSpecificFilter(column) {
    console.log(`Limpiando filtro para columna: ${column}`);

    // Limpiar del objeto activeFilters
    if (activeFilters && activeFilters[column]) {
        delete activeFilters[column];
    }

    // Limpiar el input visual
    const input = document.getElementById(`filter${column.charAt(0).toUpperCase() + column.slice(1)}`);
    if (input) {
        input.value = '';
    }

    // Reaplicar filtros sin el filtro eliminado
    if (typeof applyAllFilters === 'function') {
        applyAllFilters();
    }

    console.log(`Filtro de ${column} limpiado`);
}

/* ======================================================================================================== */

// ===== FUNCIÓN PARA REFRESCAR BÚSQUEDA GLOBAL =====
function refreshGlobalSearch() {
    if (globalSearchActive && globalSearchQuery) {
        console.log('Refrescando búsqueda global...');
        applyGlobalSearch(globalSearchQuery);
    }
}

// ===== INTEGRACIÓN CON TABLA DINÁMICA =====
function setupGlobalSearchTableIntegration() {
    // Integrar con función de agregar imagen
    if (typeof window.addImageToTable === 'function') {
        const originalAddImageToTable = window.addImageToTable;

        window.addImageToTable = function (imageData) {
            // Llamar función original
            originalAddImageToTable(imageData);

            // Refrescar búsqueda global si está activa
            setTimeout(() => {
                refreshGlobalSearch();
            }, 200);
        };
    }
}

// ===== INICIALIZACIÓN COMPLETA =====
function initializeCompleteSearch() {
    // Inicializar búsqueda global
    initializeGlobalSearch();

    // Integrar con filtros existentes
    integrateGlobalSearchWithFilters();

    // Integrar con tabla dinámica
    setupGlobalSearchTableIntegration();

    console.log('Sistema de búsqueda completo inicializado');
}

// ===== FUNCIONES GLOBALES =====
window.clearGlobalSearch = clearGlobalSearch;
window.clearAllFiltersIncludingGlobal = clearAllFiltersIncludingGlobal;
window.refreshGlobalSearch = refreshGlobalSearch;

// ===== INICIALIZACIÓN =====
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        initializeCompleteSearch();
    }, 800);
});

// ================================================================================================
// FUNCIONES ACTUALIZACION LIGHTBOX LUEGO DE ELIMINAR UNA IMAGEN Y ACTUALIZARLA
// ================================================================================================

function handleFileUpload(files) {
    if (!files || files.length === 0) return;

    console.log('Procesando nueva imagen subida...');

    // ===== VALIDACIÓN: Solo permitir UNA imagen en modal de edición =====
    if (files.length > 1) {
        showNotification('Solo se permite subir una imagen a la vez en el modo de edición', 'warning');
        return;
    }

    const file = files[0]; // Tomar solo la primera imagen

    console.log('Procesando imagen única para edición:', file.name);

    // Validar tipo de archivo
    if (!file.type.startsWith('image/')) {
        showNotification('Por favor seleccione un archivo de imagen válido', 'error');
        return;
    }

    // Validar tamaño (máximo 10MB)
    if (file.size > 10 * 1024 * 1024) {
        showNotification('La imagen es demasiado grande (máximo 10MB)', 'error');
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const base64Image = e.target.result;  // Url de base64 permanente

        // Actualizar imagen en el modal
        const modalImage = document.getElementById('editModalImage');
        if (modalImage) {
            modalImage.src = base64Image;
            console.log('Imagen del modal actualizada con base64');
        }

        // ===== Limpiar eventos anteriores y crear nuevos =====
        if (currentEditingRow) {
            const tableImage = currentEditingRow.querySelector('img');
            if (tableImage) {
                // Obtener datos actuales del formulario para el nuevo onclick
                const nuevaDescripcion = document.getElementById('editDescripcion').value.trim();
                const nuevoTipo = document.getElementById('editTipoFotografia').value;

                // Si no hay descripción en el formulario, usar la actual de la tabla
                const descripcionActual = nuevaDescripcion ||
                    currentEditingRow.querySelector('[data-column="descripcion"]')?.textContent.trim() ||
                    'Imagen actualizada';

                // Clonar imagen para limpiar eventos completamente - Usar base64
                const newTableImage = tableImage.cloneNode(true);
                newTableImage.src = base64Image; // ← BASE64 en lugar de URL temporal
                newTableImage.alt = file.name || descripcionActual;
                newTableImage.title = 'Imagen subida - ' + (file.name || descripcionActual);

                // Limpiar clases de imagen por defecto
                newTableImage.classList.remove('default-image');
                newTableImage.style.opacity = '1';
                newTableImage.removeAttribute('onclick');

                // ===== EVENT LISTENER CON BASE64 =====
                newTableImage.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    console.log('Click en imagen con base64:', base64Image.substring(0, 50) + '...');
                    openImageLightbox(base64Image, file.name || descripcionActual, descripcionActual, nuevoTipo);
                });

                // Reemplazar imagen anterior
                tableImage.parentNode.replaceChild(newTableImage, tableImage);

                console.log('Imagen de tabla reemplazada con base64 permanente');
            }
        }

        // Actualizar datos de imagen actuales para otras operaciones
        if (currentImageData) {
            currentImageData.url = base64Image;
            currentImageData.nombre = file.name;
            currentImageData.size = file.size;
            console.log('currentImageData actualizado con base64');
        }

        hasImageBeenCropped = true;
        updateResetButtonState();
    };

    // Leer archivo como base64
    reader.readAsDataURL(file);
}

/* >>>>>>>>>>====================>>>>>>>>>>> */
// Asegurar que al guardar cambios también se actualice el onclick
function saveEditChanges() {
    if (!currentEditingRow || !currentImageData) {
        showNotification('Error: No hay datos para guardar', 'error');
        return;
    }

    console.log('Guardando cambios de edición...');

    // Obtener datos del formulario
    const nuevoTipo = document.getElementById('editTipoFotografia').value;
    const nuevaDescripcion = document.getElementById('editDescripcion').value.trim();

    if (!nuevoTipo || !nuevaDescripcion) {
        alert('Por favor complete todos los campos requeridos');
        return;
    }

    // Actualizar celdas de texto
    const tipoCell = currentEditingRow.querySelector('[data-column="tipo-fotografia"]');
    const descripcionCell = currentEditingRow.querySelector('[data-column="descripcion"]');

    if (tipoCell) {
        tipoCell.textContent = nuevoTipo;
    }

    if (descripcionCell) {
        descripcionCell.textContent = nuevaDescripcion;
    }

    const tableImage = currentEditingRow.querySelector('img');

    if (tableImage) {
        console.log('Imagen actual en tabla:', tableImage.src.substring(0, 50) + '...');

        // Solo actualizar el event listener con datos finales
        const finalImageUrl = tableImage.src; // Ya es base64

        // Recrear event listener con datos finales
        const newImage = tableImage.cloneNode(true);
        newImage.removeAttribute('onclick');

        newImage.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openImageLightbox(finalImageUrl, nuevaDescripcion, nuevaDescripcion, nuevoTipo);
        });

        tableImage.parentNode.replaceChild(newImage, tableImage);
        console.log('Event listener final actualizado');
    }

    // Cerrar modal y limpiar campos
    const modal = bootstrap.Modal.getInstance(document.getElementById('editImageModal'));
    if (modal) {
        modal.hide();
    }

    // Limpiar variables
    resetEditVariables();

    // Refrescar filtros después de edición exitosa
    setTimeout(() => {
        console.log('Actualizando filtros después de edición...');

        // Refrescar datos de filtros predictivos
        if (typeof refreshPredictiveFiltersData === 'function') {
            refreshPredictiveFiltersData();
        }

        // Refrescar paginación
        if (window.refreshPagination) {
            window.refreshPagination();
        }

        console.log('Filtros actualizados después de edición');
    }, 300);

    console.log('Edición completada exitosamente');
}

// =====>>>>>>>>>>> EVENT LISTENERS PARA FUNCIONES NUEVAS(handleFileUpload - SavedEditChanges) =====>>>>>>>>>
document.addEventListener('DOMContentLoaded', function () {
    // Event listener para subida de archivos en modal de edición
    const newPhotoInput = document.getElementById('newPhotoInput');
    if (newPhotoInput) {
        newPhotoInput.addEventListener('change', function (e) {
            handleFileUpload(e.target.files);
        });
    }

    // Event listener para botón de guardar cambios (si no existe)
    const saveChangesBtn = document.getElementById('saveChangesBtn');
    if (saveChangesBtn && !saveChangesBtn.onclick) {
        saveChangesBtn.addEventListener('click', saveEditChanges);
    }
});

// ===== HACER FUNCIONES GLOBALES =====
window.handleFileUpload = handleFileUpload;
window.saveEditChanges = saveEditChanges;

// ================================================================================================
// FUNCIONES GLOBALES - CONSOLIDADAS
// ================================================================================================

window.openImageLightbox = openImageLightbox || function () { console.warn('openImageLightbox no definida'); };
window.closeLightbox = closeLightbox || function () { console.warn('closeLightbox no definida'); };
window.downloadImageFromLightbox = downloadImageFromLightbox || function () { console.warn('downloadImageFromLightbox no definida'); };
window.searchRecords = searchRecords || function () { console.warn('searchRecords no definida'); };
window.deleteImage = deleteImage || function () { console.warn('deleteImage no definida'); };
window.editImage = editImage || function () { console.warn('editImage no definida'); };
window.openHistorialModal = openHistorialModal;
window.extractImageDataFromRow = extractImageDataFromRow || function () { console.warn('extractImageDataFromRow no definida'); };
window.filterByTipoFotografia = filterByTipoFotografia;
window.selectAllTipoFotografia = selectAllTipoFotografia || function () { console.warn('selectAllTipoFotografia no definida'); };
window.clearTipoFotografiaFilter = clearTipoFotografiaFilter || function () { console.warn('clearTipoFotografiaFilter no definida'); };

console.log('Sistema JS completo cargado correctamente');
