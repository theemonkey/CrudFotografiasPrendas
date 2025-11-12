// =======================================================================
// SISTEMA DE PAGINACIÓN SIMPLE
// =======================================================================

let paginationSystem = {
    itemsPerPage: 10,
    currentPage: 1,
    totalItems: 0,
    totalPages: 0,
    isInitialized: false,
    allAvailableRows: [], //Cache de todas las filas disponibles
    isUpdating: false     //Flag para evitar loops
};

// ===== INICIALIZAR PAGINACIÓN SIMPLE =====
function initializeSimplePagination() {
    console.log('Inicializando paginación simple...');

    if (paginationSystem.isInitialized) {
        console.log('Paginación ya inicializada');
        return;
    }

    // Crear controles de paginación si no existen
    createPaginationControls();

    // Configurar event listeners
    setupPaginationListeners();

    //SINCRONIZAR selector con sistema al inicializar
    const recordsSelect = document.getElementById('recordsPerPageSelect');
    if (recordsSelect) {
        recordsSelect.value = paginationSystem.itemsPerPage.toString();
        console.log(`Selector sincronizado con ${paginationSystem.itemsPerPage} registros por página`);
    }

    // Actualizar paginación inicial
    updatePagination();

    paginationSystem.isInitialized = true;
    console.log('Paginación simple inicializada correctamente');
}

// ===== CREAR CONTROLES DE PAGINACIÓN =====
function createPaginationControls() {
    const tableContainer = document.querySelector('.table-responsive');
    if (!tableContainer) {
        console.warn('Contenedor de tabla no encontrado');
        return;
    }

    // Verificar si ya existen los controles
    if (document.getElementById('paginationControls')) {
        console.log('Controles de paginación ya existen');
        return;
    }

    const paginationHTML = `
        <div id="paginationControls" class="d-flex justify-content-between align-items-center mt-3">
            <!-- Información de registros -->
            <div class="pagination-info">
                <span class="text-muted">
                    Mostrando <span id="startRecord">0</span> a <span id="endRecord">0</span> de <span id="totalRecords">0</span> registros
                </span>
            </div>

            <!-- Selector de registros por página -->
            <div class="pagination-selector">
                <select id="recordsPerPageSelect" class="form-select form-select-sm" style="width: auto;">
                    <option value="10" selected>10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                </select>
            </div>

            <!-- Controles de navegación -->
            <nav aria-label="Navegación de páginas">
                <ul id="paginationNav" class="pagination pagination-sm mb-0">
                    <!-- Se genera dinámicamente -->
                </ul>
            </nav>
        </div>
    `;

    // Insertar después de la tabla
    tableContainer.insertAdjacentHTML('afterend', paginationHTML);
    console.log('Controles de paginación creados');
}

// ===== CONFIGURAR EVENT LISTENERS =====
function setupPaginationListeners() {
    // Selector de registros por página
    const recordsSelect = document.getElementById('recordsPerPageSelect');
    if (recordsSelect) {
        //ASEGURAR que el valor inicial coincida con el sistema
        recordsSelect.value = paginationSystem.itemsPerPage.toString();

        recordsSelect.addEventListener('change', function (e) {
            const newValue = parseInt(e.target.value);
            console.log(`Cambiando registros por página de ${paginationSystem.itemsPerPage} a: ${newValue}`);

            paginationSystem.itemsPerPage = newValue;
            paginationSystem.currentPage = 1; //Resetear a página 1

            //Forzar actualización completa
            setTimeout(() => {
                updatePagination();
            }, 50);
        });

        console.log(`Event listener configurado - Valor inicial: ${recordsSelect.value}`);
    } else {
        console.warn('Selector de registros por página no encontrado');
    }
}

// ===== OBTENER FILAS DISPONIBLES (SIN FILTROS DE PAGINACIÓN) =====
function getAvailableRows() {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) {
        console.warn('Tabla no encontrada');
        return [];
    }

    const allRows = Array.from(tableBody.querySelectorAll('tr[data-image-id]'));

    //LÓGICA SIMPLIFICADA: Solo excluir filas que están explícitamente filtradas
    const availableRows = allRows.filter(row => {
        // Verificar clases específicas de filtros (NO paginación)
        const isFilteredBySearch = row.classList.contains('search-hidden');
        const isFilteredByType = row.classList.contains('type-filtered-out');
        const isFilteredGeneral = row.classList.contains('filtered-out');

        //NUEVA LÓGICA: Solo considerar filtros externos, NO paginación
        const isHiddenByFilters = isFilteredBySearch || isFilteredByType || isFilteredGeneral;

        //IGNORAR display:none si es por paginación
        const isHiddenByPagination = row.classList.contains('pagination-hidden');

        // Una fila está disponible si NO está filtrada por filtros externos
        // (ignoramos si está oculta por paginación)
        return !isHiddenByFilters;
    });

    console.log(`Análisis de filas:`, {
        totalDOM: allRows.length,
        disponibles: availableRows.length,
        filtradosSearch: allRows.filter(r => r.classList.contains('search-hidden')).length,
        filtradosType: allRows.filter(r => r.classList.contains('type-filtered-out')).length,
        filtradosGeneral: allRows.filter(r => r.classList.contains('filtered-out')).length,
        ocultospagination: allRows.filter(r => r.classList.contains('pagination-hidden')).length
    });

    return availableRows;
}

// ===== ACTUALIZAR PAGINACIÓN =====
function updatePagination() {
    if (paginationSystem.isUpdating) {
        console.log('Paginación ya actualizándose, omitiendo...');
        return;
    }

    paginationSystem.isUpdating = true;
    console.log('Actualizando paginación...');

    verifySelectConsistency();

    //OBTENER filas disponibles con verificación
    const previousCount = paginationSystem.totalItems;
    paginationSystem.allAvailableRows = getAvailableRows();
    paginationSystem.totalItems = paginationSystem.allAvailableRows.length;
    paginationSystem.totalPages = Math.ceil(paginationSystem.totalItems / paginationSystem.itemsPerPage);

    console.log(`Cambio en filas: ${previousCount} → ${paginationSystem.totalItems}`);
    console.log(`Paginación calculada: ${paginationSystem.totalItems} elementos, ${paginationSystem.totalPages} páginas, ${paginationSystem.itemsPerPage} por página`);

    //VALIDAR página actual más estrictamente
    if (paginationSystem.totalItems === 0) {
        paginationSystem.currentPage = 1;
        paginationSystem.totalPages = 0;
    } else {
        if (paginationSystem.currentPage > paginationSystem.totalPages) {
            console.log(`Ajustando página de ${paginationSystem.currentPage} a ${paginationSystem.totalPages}`);
            paginationSystem.currentPage = paginationSystem.totalPages;
        }

        if (paginationSystem.currentPage < 1) {
            paginationSystem.currentPage = 1;
        }
    }

    //MOSTRAR filas de la página actual
    showCurrentPageRows();

    //ACTUALIZAR controles UI
    updatePaginationInfo();
    updatePaginationNavigation();

    //VERIFICACIÓN POST-ACTUALIZACIÓN
    setTimeout(() => {
        const tableBody = document.getElementById('imagesTableBody');
        if (tableBody) {
            const visibleRows = Array.from(tableBody.querySelectorAll('tr[data-image-id]'))
                .filter(row => window.getComputedStyle(row).display !== 'none');

            const expectedVisible = Math.min(paginationSystem.itemsPerPage,
                paginationSystem.totalItems - ((paginationSystem.currentPage - 1) * paginationSystem.itemsPerPage));

            if (visibleRows.length !== expectedVisible) {
                console.warn(`INCONSISTENCIA: Se esperaban ${expectedVisible} filas visibles pero hay ${visibleRows.length}`);
                console.log('Ejecutando verificación automática...');
                if (window.verifyPagination) {
                    window.verifyPagination();
                }
            }
        }
    }, 100);

    paginationSystem.isUpdating = false;

    console.log(`Paginación actualizada: Página ${paginationSystem.currentPage}/${paginationSystem.totalPages} - ${paginationSystem.totalItems} elementos visibles`);
}

// ===== MOSTRAR FILAS DE LA PÁGINA ACTUAL =====
function showCurrentPageRows() {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) return;

    const startIndex = (paginationSystem.currentPage - 1) * paginationSystem.itemsPerPage;
    const endIndex = startIndex + paginationSystem.itemsPerPage;

    console.log(`Mostrando página ${paginationSystem.currentPage}: filas ${startIndex + 1} a ${Math.min(endIndex, paginationSystem.totalItems)}`);

    //PASO 1: RESET - Limpiar todas las filas de paginación anterior
    const allRows = tableBody.querySelectorAll('tr[data-image-id]');
    allRows.forEach(row => {
        // Solo agregar clase pagination-hidden, NO modificar display directamente aquí
        row.classList.add('pagination-hidden');
    });

    //PASO 2: Mostrar solo las filas de la página actual
    const currentPageRows = paginationSystem.allAvailableRows.slice(startIndex, endIndex);

    console.log(`Filas a mostrar:`, currentPageRows.map(row => {
        const ordenSit = row.querySelector('[data-column="orden-sit"]')?.textContent || 'N/A';
        return ordenSit;
    }));

    //PASO 3: Aplicar visibilidad correcta
    allRows.forEach(row => {
        const shouldBeVisible = currentPageRows.includes(row);

        if (shouldBeVisible) {
    //MOSTRAR: Remover clase de paginación y mostrar si no hay otros filtros
            row.classList.remove('pagination-hidden');

            // Solo mostrar si no está filtrada por otros criterios
            const isFilteredByOthers = row.classList.contains('search-hidden') ||
                row.classList.contains('type-filtered-out') ||
                row.classList.contains('filtered-out');

            if (!isFilteredByOthers) {
                row.style.display = '';
            }
        } else {
            //OCULTAR: Mantener clase pagination-hidden y ocultar
            row.classList.add('pagination-hidden');
            row.style.display = 'none';
        }
    });

    //VERIFICACIÓN: Contar filas realmente visibles
    const actuallyVisible = Array.from(allRows).filter(row => {
        const computed = window.getComputedStyle(row);
        return computed.display !== 'none';
    });

    console.log(`Resultado: ${actuallyVisible.length} filas visibles de ${currentPageRows.length} esperadas`);
}

// ===== ACTUALIZAR INFORMACIÓN DE REGISTROS =====
function updatePaginationInfo() {
    const startRecord = document.getElementById('startRecord');
    const endRecord = document.getElementById('endRecord');
    const totalRecords = document.getElementById('totalRecords');

    if (!startRecord || !endRecord || !totalRecords) {
        console.warn('Elementos de información de paginación no encontrados');
        return;
    }

    const start = paginationSystem.totalItems > 0 ?
        (paginationSystem.currentPage - 1) * paginationSystem.itemsPerPage + 1 : 0;
    const end = Math.min(paginationSystem.currentPage * paginationSystem.itemsPerPage, paginationSystem.totalItems);

    startRecord.textContent = start;
    endRecord.textContent = end;
    totalRecords.textContent = paginationSystem.totalItems;

    console.log(`Info actualizada: ${start} a ${end} de ${paginationSystem.totalItems}`);
}

// ===== ACTUALIZAR NAVEGACIÓN =====
function updatePaginationNavigation() {
    const paginationNav = document.getElementById('paginationNav');
    if (!paginationNav) {
        console.warn('Navegación de paginación no encontrada');
        return;
    }

    let navHTML = '';

    //OCULTAR navegación si solo hay una página o ninguna
    if (paginationSystem.totalPages <= 1) {
        paginationNav.innerHTML = '';
        return;
    }

    // Botón Anterior
    const isFirstPage = paginationSystem.currentPage === 1;
    navHTML += `
        <li class="page-item ${isFirstPage ? 'disabled' : ''}">
            <a class="page-link" href="#" ${!isFirstPage ? `onclick="goToPage(${paginationSystem.currentPage - 1}); return false;"` : ''} aria-label="Anterior">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;

    // Números de página (máximo 5 páginas visibles)
    const maxVisiblePages = 5;
    let startPage = Math.max(1, paginationSystem.currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(paginationSystem.totalPages, startPage + maxVisiblePages - 1);

    // Ajustar si estamos cerca del final
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    // Página 1 si no está visible
    if (startPage > 1) {
        navHTML += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(1); return false;">1</a></li>`;
        if (startPage > 2) {
            navHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Páginas visibles
    for (let i = startPage; i <= endPage; i++) {
        navHTML += `
            <li class="page-item ${i === paginationSystem.currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>
            </li>
        `;
    }

    // Última página si no está visible
    if (endPage < paginationSystem.totalPages) {
        if (endPage < paginationSystem.totalPages - 1) {
            navHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        navHTML += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${paginationSystem.totalPages}); return false;">${paginationSystem.totalPages}</a></li>`;
    }

    // Botón Siguiente
    const isLastPage = paginationSystem.currentPage === paginationSystem.totalPages;
    navHTML += `
        <li class="page-item ${isLastPage ? 'disabled' : ''}">
            <a class="page-link" href="#" ${!isLastPage ? `onclick="goToPage(${paginationSystem.currentPage + 1}); return false;"` : ''} aria-label="Siguiente">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;

    paginationNav.innerHTML = navHTML;
}

// ===== NAVEGAR A PÁGINA ESPECÍFICA =====
function goToPage(pageNumber) {
    console.log(`Navegando a página: ${pageNumber}`);

    if (pageNumber < 1 || pageNumber > paginationSystem.totalPages) {
        console.warn(`Página inválida: ${pageNumber} (rango: 1-${paginationSystem.totalPages})`);
        return;
    }

    paginationSystem.currentPage = pageNumber;

    //Actualizar inmediatamente sin delay
    updatePagination();
}

// ===== REFRESCAR PAGINACIÓN (PARA USO EXTERNO) =====
function refreshPagination() {
    console.log('Refrescando paginación externamente...');

    if (paginationSystem.isUpdating) {
        console.log('Sistema actualizándose, programando refresh...');
        setTimeout(() => {
            refreshPagination();
        }, 500);
        return;
    }

    // NO resetear a página 1 automáticamente, mantener página actual si es válida
    const currentRows = getAvailableRows();
    const newTotalPages = Math.ceil(currentRows.length / paginationSystem.itemsPerPage);

    // Solo resetear a página 1 si la página actual ya no es válida
    if (paginationSystem.currentPage > newTotalPages && newTotalPages > 0) {
        console.log(`Página actual ${paginationSystem.currentPage} > ${newTotalPages}, reseteando a 1`);
        paginationSystem.currentPage = 1;
    }

    // Actualizar con pequeño delay para asegurar que otros procesos terminen
    setTimeout(() => {
        updatePagination();
    }, 150);
}

// ===== FUNCIONES GLOBALES =====
window.goToPage = goToPage;
window.refreshPagination = refreshPagination;

// ===== INICIALIZACIÓN =====
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        console.log('Verificando inicialización de paginación...');

        const tableBody = document.getElementById('imagesTableBody');
        const rows = tableBody ? tableBody.querySelectorAll('tr[data-image-id]') : [];

        console.log(`Filas encontradas para paginación: ${rows.length}`);

        if (rows.length > 0) {
            initializeSimplePagination();

            //VERIFICAR sincronización después de inicializar
            setTimeout(() => {
                const recordsSelect = document.getElementById('recordsPerPageSelect');
                if (recordsSelect) {
                    const selectValue = parseInt(recordsSelect.value);
                    if (selectValue !== paginationSystem.itemsPerPage) {
                        console.warn(`Inconsistencia detectada: Selector=${selectValue}, Sistema=${paginationSystem.itemsPerPage}`);
                        console.log('Corrigiendo selector...');
                        recordsSelect.value = paginationSystem.itemsPerPage.toString();
                    } else {
                        console.log(`Selector y sistema sincronizados: ${selectValue} registros por página`);
                    }
                }
            }, 500);

            console.log('Sistema de paginación inicializado');
        } else {
            console.log('No hay filas - reintentando en 2 segundos...');

            setTimeout(() => {
                const retryRows = tableBody ? tableBody.querySelectorAll('tr[data-image-id]') : [];
                if (retryRows.length > 0) {
                    console.log('Filas detectadas en retry - inicializando...');
                    initializeSimplePagination();
                }
            }, 2000);
        }
    }, 1500);
});

// ===== FUNCIÓN DE DEBUG =====
window.debugPagination = function () {
    console.log('DEBUG PAGINACIÓN:', {
        isInitialized: paginationSystem.isInitialized,
        currentPage: paginationSystem.currentPage,
        itemsPerPage: paginationSystem.itemsPerPage,
        totalItems: paginationSystem.totalItems,
        totalPages: paginationSystem.totalPages,
        availableRowsCount: paginationSystem.allAvailableRows.length,
        isUpdating: paginationSystem.isUpdating
    });

    // Debug de filas DOM
    const tableBody = document.getElementById('imagesTableBody');
    if (tableBody) {
        const allRows = tableBody.querySelectorAll('tr[data-image-id]');
        const visibleRows = Array.from(allRows).filter(row =>
            window.getComputedStyle(row).display !== 'none'
        );

        console.log('DOM ACTUAL:', {
            totalRowsInDOM: allRows.length,
            visibleRowsInDOM: visibleRows.length,
            paginationHiddenRows: tableBody.querySelectorAll('tr.pagination-hidden').length
        });
    }
};

// ===== FUNCIÓN DE VERIFICACIÓN COMPLETA =====
window.verifyPagination = function () {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) return;

    console.log('VERIFICACIÓN COMPLETA DE PAGINACIÓN:');

    const allRows = Array.from(tableBody.querySelectorAll('tr[data-image-id]'));

    // Análisis detallado de cada fila
    console.log('ANÁLISIS POR FILA:');
    allRows.forEach((row, index) => {
        const ordenSit = row.querySelector('[data-column="orden-sit"]')?.textContent || `Fila-${index}`;
        const classes = Array.from(row.classList);
        const computedDisplay = window.getComputedStyle(row).display;
        const inlineDisplay = row.style.display;

        console.log(`Fila ${index + 1} (${ordenSit}):`, {
            clases: classes,
            displayComputed: computedDisplay,
            displayInline: inlineDisplay,
            visible: computedDisplay !== 'none'
        });
    });

    // Estado del sistema
    console.log('ESTADO DEL SISTEMA:');
    console.log({
        totalFilas: allRows.length,
        filasDisponibles: paginationSystem.allAvailableRows.length,
        paginaActual: paginationSystem.currentPage,
        elementosPorPagina: paginationSystem.itemsPerPage,
        totalPaginas: paginationSystem.totalPages,
        totalElementos: paginationSystem.totalItems
    });

    // Recalcular manualmente
    const manualAvailable = allRows.filter(row => {
        return !row.classList.contains('search-hidden') &&
            !row.classList.contains('type-filtered-out') &&
            !row.classList.contains('filtered-out');
    });

    console.log('CÁLCULO MANUAL:');
    console.log({
        filasDisponiblesManual: manualAvailable.length,
        coincideConSistema: manualAvailable.length === paginationSystem.totalItems
    });

    // Si no coincide, forzar corrección
    if (manualAvailable.length !== paginationSystem.totalItems) {
        console.log('INCONSISTENCIA DETECTADA - Corrigiendo...');
        paginationSystem.allAvailableRows = manualAvailable;
        paginationSystem.totalItems = manualAvailable.length;
        paginationSystem.totalPages = Math.ceil(paginationSystem.totalItems / paginationSystem.itemsPerPage);

        // Actualizar UI
        updatePaginationInfo();
        updatePaginationNavigation();
        showCurrentPageRows();

        console.log('CORRECCIÓN APLICADA');
    }
};


// ===== VERIFICAR CONSISTENCIA DEL SELECTOR =====
function verifySelectConsistency() {
    const recordsSelect = document.getElementById('recordsPerPageSelect');
    if (!recordsSelect) return false;

    const selectValue = parseInt(recordsSelect.value);
    const systemValue = paginationSystem.itemsPerPage;

    if (selectValue !== systemValue) {
        console.warn(`INCONSISTENCIA: Selector=${selectValue}, Sistema=${systemValue}`);

        // Corregir automáticamente
        recordsSelect.value = systemValue.toString();
        console.log(`Selector corregido a: ${systemValue}`);

        return false;
    }

    return true;
}

// Agregar al objeto global para debug
window.verifySelectConsistency = verifySelectConsistency;


console.log('Sistema de paginación simple cargado');
