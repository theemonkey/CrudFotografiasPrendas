/* ===== SISTEMA DE PAGINACIÓN EN TABLA ===== */
class PaginationSystem {
    constructor() {
        this.currentPage = 1; // página actual
        this.recordsPerPage = 10; // Número de elementos a mostrar en página
        this.totalRecords = 0;   // Total de registros
        this.allRows = [];
        this.filteredRows = [];
        this.isInitialized = false;
    }

    init() {
        if (this.isInitialized) {
            console.log('Paginación ya inicializada');
            return;
        }

        // Verificar que estamos en la página correcta
        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) {
            console.warn('Tabla no encontrada - No inicializar paginación');
            return;
        }

        // Event listener para cambio de registros por página
        const recordsSelector = document.getElementById('recordsPerPage');
        if (recordsSelector) {
            recordsSelector.addEventListener('change', (e) => {
                this.recordsPerPage = parseInt(e.target.value);
                this.currentPage = 1;
                this.updatePagination();
            });
        }

        // Inicialización inicial
        this.refreshData();
        this.isInitialized = true;

        console.log('Sistema de paginación inicializado correctamente');
    }

    refreshData() {
        console.log('Actualizando datos de paginación...');

        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) {
            console.warn('Tabla no encontrada');
            return;
        }

        // Obtener TODAS las filas, no filtrar por display
        const allTableRows = Array.from(tableBody.querySelectorAll('tr[data-image-id]'));
        console.log(`Filas encontradas en tabla: ${allTableRows.length}`);

        // Separar filas que están realmente en el DOM de las que están ocultas por filtros
        this.allRows = allTableRows;

        // Considerar búsquedas y filtros de tipo
        this.filteredRows = allTableRows.filter(row => {
            const isHiddenBySearch = row.classList.contains('search-hidden');
            const isHiddenByTypeFilter = row.classList.contains('type-filtered-out');
            const isHiddenByOtherFilters = row.classList.contains('filtered-out');

            // solo incluir filas visibles
            const isVisible = !isHiddenBySearch && !isHiddenByTypeFilter && !isHiddenByOtherFilters;

            if (isVisible) { 
                console.log(`Fila visible incluida`);
            }

            return isVisible;;
        });

        this.totalRecords = this.filteredRows.length;

        console.log(`Total filas: ${this.allRows.length}, Visibles: ${this.totalRecords}`);

        this.updatePagination();
    }

    updatePagination() {
        console.log(`Actualizando paginación - Página ${this.currentPage}, ${this.recordsPerPage} por página`);

        if (this.totalRecords === 0) {
            this.showEmptyState();
            return;
        }

        // Calcular página máxima
        const maxPages = Math.ceil(this.totalRecords / this.recordsPerPage);

        // Validar página actual
        if (this.currentPage > maxPages && maxPages > 0) {
            this.currentPage = maxPages;
        }

        // Mostrar/ocultar filas según la página actual
        this.showCurrentPageRows();

        // Actualizar información de registros
        this.updateRecordsInfo();

        // Actualizar controles de paginación
        this.updatePaginationControls(maxPages);
    }

    showCurrentPageRows() {
        const startIndex = (this.currentPage - 1) * this.recordsPerPage;
        const endIndex = startIndex + this.recordsPerPage;

        console.log(`Mostrando filas del ${startIndex} al ${endIndex - 1}`);

        // CORECCIÓN: Primero mostrar todas las filas válidas
        this.allRows.forEach(row => {
            // Solo ocultar si no está filtrada externamente
            if (!row.classList.contains('filtered-out')) {
                row.style.display = 'none';
            }
        });

        // Mostrar solo las filas de la página actual
        const currentPageRows = this.filteredRows.slice(startIndex, endIndex);

        currentPageRows.forEach((row, index) => {
            row.style.display = '';
            row.classList.remove('pagination-hidden');

            // Animación suave SOLO si la fila está completamente cargada
            if (row.querySelector('img')) {
                row.style.opacity = '0';
                row.style.transform = 'translateY(5px)';

                setTimeout(() => {
                    row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 30);
            }
        });

        console.log(`Mostrando ${currentPageRows.length} filas en página ${this.currentPage}`);
    }

    updateRecordsInfo() {
        const startRecord = this.totalRecords > 0 ? ((this.currentPage - 1) * this.recordsPerPage) + 1 : 0;
        const endRecord = Math.min(this.currentPage * this.recordsPerPage, this.totalRecords);

        const startSpan = document.getElementById('startRecord');
        const endSpan = document.getElementById('endRecord');
        const totalSpan = document.getElementById('totalRecords');

        if (startSpan) startSpan.textContent = startRecord;
        if (endSpan) endSpan.textContent = endRecord;
        if (totalSpan) totalSpan.textContent = this.totalRecords;

        console.log(`Info: ${startRecord}-${endRecord} de ${this.totalRecords}`);
    }

    updatePaginationControls(maxPages) {
        const paginationList = document.getElementById('paginationList');
        if (!paginationList) {
            console.log('Controles de paginación no encontrados');
            return;
        }

        paginationList.innerHTML = '';

        if (maxPages <= 1) {
            paginationList.style.display = 'none';
            return;
        }

        paginationList.style.display = 'flex';

        // Botón Anterior
        const prevBtn = this.createPageButton('Anterior', this.currentPage - 1, this.currentPage === 1);
        paginationList.appendChild(prevBtn);

        // Calcular rango de páginas a mostrar
        const { startPage, endPage } = this.calculatePageRange(maxPages);

        // Primera página si no está en el rango
        if (startPage > 1) {
            paginationList.appendChild(this.createPageButton('1', 1));
            if (startPage > 2) {
                paginationList.appendChild(this.createEllipsis());
            }
        }

        // Páginas del rango
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === this.currentPage;
            paginationList.appendChild(this.createPageButton(i.toString(), i, false, isActive));
        }

        // Última página si no está en el rango
        if (endPage < maxPages) {
            if (endPage < maxPages - 1) {
                paginationList.appendChild(this.createEllipsis());
            }
            paginationList.appendChild(this.createPageButton(maxPages.toString(), maxPages));
        }

        // Botón Siguiente
        const nextBtn = this.createPageButton('Siguiente', this.currentPage + 1, this.currentPage === maxPages);
        paginationList.appendChild(nextBtn);
    }

    calculatePageRange(maxPages) {
        const delta = 2;
        let startPage = Math.max(1, this.currentPage - delta);
        let endPage = Math.min(maxPages, this.currentPage + delta);

        if (endPage - startPage < delta * 2) {
            if (startPage === 1) {
                endPage = Math.min(maxPages, startPage + delta * 2);
            } else if (endPage === maxPages) {
                startPage = Math.max(1, endPage - delta * 2);
            }
        }

        return { startPage, endPage };
    }

    createPageButton(text, page, disabled = false, active = false) {
        const li = document.createElement('li');
        li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;

        if (disabled) {
            li.innerHTML = `<span class="page-link">${text}</span>`;
        } else {
            const link = document.createElement('a');
            link.className = 'page-link';
            link.href = '#';
            link.textContent = text;
            link.onclick = (e) => {
                e.preventDefault();
                this.goToPage(page);
            };
            li.appendChild(link);
        }

        return li;
    }

    createEllipsis() {
        const li = document.createElement('li');
        li.className = 'page-item disabled';
        li.innerHTML = '<span class="page-link">...</span>';
        return li;
    }

    goToPage(page) {
        const maxPages = Math.ceil(this.totalRecords / this.recordsPerPage);

        if (page >= 1 && page <= maxPages && page !== this.currentPage) {
            console.log(`Navegando a página ${page}`);
            this.currentPage = page;
            this.updatePagination();

            // Scroll suave hacia la tabla
            const table = document.querySelector('.images-table');
            if (table) {
                table.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    showEmptyState() {
        const paginationList = document.getElementById('paginationList');
        const startSpan = document.getElementById('startRecord');
        const endSpan = document.getElementById('endRecord');
        const totalSpan = document.getElementById('totalRecords');

        if (paginationList) paginationList.style.display = 'none';
        if (startSpan) startSpan.textContent = '0';
        if (endSpan) endSpan.textContent = '0';
        if (totalSpan) totalSpan.textContent = '0';

        console.log('Estado vacío mostrado');
    }

    // MÉTODO MEJORADO para refrescar cuando se agreguen/eliminen filas
    refresh() {
        console.log('Refresh manual de paginación');

        // Delay para asegurar que el DOM esté actualizado
        setTimeout(() => {
            this.refreshData();
        }, 100);
    }

    // Nuevo método para ir a la última página (útil cuando se agrega contenido)
    goToLastPage() {
        const maxPages = Math.ceil(this.totalRecords / this.recordsPerPage);
        if (maxPages > 0) {
            this.goToPage(maxPages);
        }
    }

    // Método para manejar filtros externos
    applyExternalFilter(filterFunction) {
        console.log('Aplicando filtro externo');

        // Aplicar filtro a las filas
        this.allRows.forEach(row => {
            if (filterFunction(row)) {
                row.classList.remove('filtered-out');
            } else {
                row.classList.add('filtered-out');
                row.style.display = 'none';
            }
        });

        // Refrescar datos después del filtro
        this.refreshData();
        this.currentPage = 1; // Volver a primera página
        this.updatePagination();
    }

    // Método para limpiar filtros externos
    clearExternalFilters() {
        console.log('Limpiando filtros externos');

        this.allRows.forEach(row => {
            row.classList.remove('filtered-out');
        });

        this.refreshData();
        this.updatePagination();
    }
}

// INICIALIZACIÓN MEJORADA
let paginationSystem = null;

// Solo crear la clase, NO instanciarla automáticamente
function createPaginationSystem() {
    if (paginationSystem) {
        console.log('Sistema de paginación ya existe');
        return paginationSystem;
    }

    paginationSystem = new PaginationSystem();
    window.paginationSystem = paginationSystem;
    console.log('Sistema de paginación creado(sin inicializar)');
    return paginationSystem;
}

// Función de inicialización solo cuando se requiera
function initializePaginationSystem() {
    const isPhotosIndex = document.getElementById('imagesTableBody') !== null;
    if (!isPhotosIndex) {
        console.log('No se requiere inicializar el sistema de paginación');
        return;
    }

    const system = createPaginationSystem();

    // Delay para asegurar que la tabla este lista
    setTimeout(() => {
        system.init();
    }, 400);
}

// FUNCIONES GLOBALES MEJORADAS para integración

// Función para refrescar paginación (llamar después de agregar imágenes)
window.refreshPagination = function () {
    console.log('Refresh global de paginación');
    if (window.paginationSystem && window.paginationSystem.isInitialized) {
        window.paginationSystem.refresh();
    } else {
        console.warn('Sistema de paginación no inicializado - Ignorando refresh');
    }
};

// Función para ir a la última página (útil después de subir imágenes)
window.goToLastPage = function () {
    if (window.paginationSystem && window.paginationSystem.isInitialized) {
        window.paginationSystem.goToLastPage();
    }
};

// Función para ir a una página específica
window.goToPage = function (page) {
    if (window.paginationSystem && window.paginationSystem.isInitialized) {
        window.paginationSystem.goToPage(page);
    }
};

// EXPONER función de inicialización para uso manual
window.initializePaginationSystem = initializePaginationSystem;
window.createPaginationSystem = createPaginationSystem;
