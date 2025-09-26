/*!
 * Sistema Responsive Completo
 * Convierte tabla en cards para móviles de forma eficiente
 */

// ================================================================
// CLASE PRINCIPAL RESPONSIVE SYSTEM
// ================================================================

class ResponsiveSystem {
    constructor() {
        this.initialized = false;
        this.currentView = null; // 'desktop' o 'mobile'
        this.breakpoint = 768; // Punto de cambio tablet/móvil
        this.debounceTimer = null;
        this.observer = null;

        console.log('ResponsiveSystem: Inicializando...');
    }

    init() {
        if (this.initialized) {
            console.log('Ya inicializado');
            return;
        }

        this.setupMediaQuery();
        this.setupTableObserver();
        this.handleResize();

        // Event listeners
        window.addEventListener('resize', () => this.debounceResize());
        window.addEventListener('orientationchange', () => {
            setTimeout(() => this.handleResize(), 100);
        });

        this.initialized = true;
        console.log('ResponsiveSystem: Inicializado correctamente');
    }

    setupMediaQuery() {
        // Media query listener más eficiente
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia(`(max-width: ${this.breakpoint - 1}px)`);
            mediaQuery.addListener(() => this.handleResize());
        }
    }

    debounceResize() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => this.handleResize(), 150);
    }

    handleResize() {
        const isMobile = window.innerWidth < this.breakpoint;
        const newView = isMobile ? 'mobile' : 'desktop';

        if (this.currentView !== newView) {
            console.log(`Cambiando vista: ${this.currentView} → ${newView}`);
            this.currentView = newView;
            this.switchView();
        }
    }

    switchView() {
        if (this.currentView === 'mobile') {
            this.showMobileView();
        } else {
            this.showDesktopView();
        }
    }

    showMobileView() {
        console.log('Activando vista móvil');

        // Ocultar tabla
        const table = document.querySelector('.table-responsive');
        if (table) {
            table.style.display = 'none';
        }

        // Mostrar cards
        this.generateMobileCards();
    }

    showDesktopView() {
        console.log('Activando vista desktop');

        // Mostrar tabla
        const table = document.querySelector('.table-responsive');
        if (table) {
            table.style.display = 'block';
        }

        // Ocultar cards
        const mobileContainer = document.querySelector('.mobile-cards-container');
        if (mobileContainer) {
            mobileContainer.style.display = 'none';
        }
    }

    generateMobileCards() {
        console.log('Generando cards móviles...');

        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) {
            console.error('No se encontró tabla');
            return;
        }

        let mobileContainer = document.querySelector('.mobile-cards-container');
        if (!mobileContainer) {
            mobileContainer = this.createMobileContainer();
        }

        mobileContainer.style.display = 'block';

        const rows = Array.from(tableBody.querySelectorAll('tr'));
        console.log(`Procesando ${rows.length} filas`);

        if (rows.length === 0) {
            mobileContainer.innerHTML = this.getEmptyStateHTML();
            return;
        }

        const cardsHTML = rows.map((row, index) => this.createMobileCard(row, index))
            .filter(Boolean)
            .join('');

        mobileContainer.innerHTML = cardsHTML;
        console.log(`${rows.length} cards generados exitosamente`);
    }

    createMobileContainer() {
        console.log('Creando contenedor móvil');

        const cardBody = document.querySelector('.card-body');
        if (!cardBody) {
            console.error('No se encontró .card-body');
            return null;
        }

        const container = document.createElement('div');
        container.className = 'mobile-cards-container';
        container.style.cssText = `
            display: none;
            width: 100%;
            padding: 10px 0;
        `;

        // Insertar después de la tabla
        const table = cardBody.querySelector('.table-responsive');
        if (table) {
            cardBody.insertBefore(container, table.nextSibling);
        } else {
            cardBody.appendChild(container);
        }

        return container;
    }

    createMobileCard(row, index) {
        try {
            const cells = row.querySelectorAll('td');
            if (cells.length < 6) {
                console.warn(`Fila ${index} incompleta (${cells.length} celdas)`);
                return '';
            }

            // Extraer datos de la fila
            const data = this.extractRowData(cells);
            const imageData = this.extractImageData(cells[0], index);
            const actionsHTML = this.extractActionsHTML(cells[6]);

            return this.generateCardHTML(data, imageData, actionsHTML, index);

        } catch (error) {
            console.error(`Error creando card ${index}:`, error);
            return this.getErrorCardHTML(index, error.message);
        }
    }

    extractRowData(cells) {
        return {
            ordenSit: this.getTextContent(cells[1]) || 'Sin orden',
            po: this.getTextContent(cells[2]) || 'N/A',
            oc: this.getTextContent(cells[3]) || 'N/A',
            descripcion: this.getTextContent(cells[4]) || 'Sin descripción',
            tipo: this.getTextContent(cells[5]) || 'Sin tipo'
        };
    }

    extractImageData(imageCell, index) {
        const img = imageCell?.querySelector('img');
        if (!img) {
            return {
                src: this.getPlaceholderImage(),
                alt: `Imagen ${index + 1}`,
                onclick: ''
            };
        }

        return {
            src: img.src || this.getPlaceholderImage(),
            alt: img.alt || `Imagen ${index + 1}`,
            onclick: img.getAttribute('onclick') || ''
        };
    }

    extractActionsHTML(actionsCell) {
        if (!actionsCell) return '';

        const buttons = Array.from(actionsCell.querySelectorAll('button'));
        return buttons.map(btn => this.convertButtonForMobile(btn)).join('');
    }

    convertButtonForMobile(button) {
        const icon = button.querySelector('i')?.className || 'fas fa-cog';
        const onclick = button.getAttribute('onclick') || '';
        const title = button.getAttribute('title') || '';
        const classes = this.getMobileButtonClasses(button);

        return `
            <button class="${classes}" onclick="${onclick}" title="${title}">
                <i class="${icon}"></i>
            </button>
        `;
    }

    getMobileButtonClasses(button) {
        let baseClasses = 'btn btn-sm';

        if (button.classList.contains('btn-danger')) {
            baseClasses += ' btn-danger';
        } else if (button.classList.contains('btn-warning')) {
            baseClasses += ' btn-warning';
        } else if (button.classList.contains('btn-info')) {
            baseClasses += ' btn-info';
        } else if (button.classList.contains('btn-success')) {
            baseClasses += ' btn-success';
        } else {
            baseClasses += ' btn-secondary';
        }

        return baseClasses;
    }

    generateCardHTML(data, imageData, actionsHTML, index) {
        const tipoBadge = this.getTipoBadge(data.tipo);
        const imageOnclick = imageData.onclick ? `onclick="${imageData.onclick}"` : '';

        return `
            <div class="mobile-card" data-row-index="${index}">
                <div class="mobile-card-header">
                    <img src="${imageData.src}"
                         alt="${imageData.alt}"
                         class="mobile-card-image"
                         ${imageOnclick}
                         onerror="this.src='${this.getPlaceholderImage()}';"
                         loading="lazy">
                    <div class="mobile-card-info">
                        <div class="mobile-card-title">
                            <i class="fas fa-hashtag me-1"></i>
                            ${data.ordenSit}
                        </div>
                        <div class="mobile-card-subtitle">
                            ${tipoBadge}
                        </div>
                    </div>
                </div>

                <div class="mobile-card-body">
                    <div class="mobile-card-field">
                        <span class="mobile-card-label">
                            <i class="fas fa-file-alt me-1"></i>
                            P.O:
                        </span>
                        <span class="mobile-card-value">${data.po}</span>
                    </div>
                    <div class="mobile-card-field">
                        <span class="mobile-card-label">
                            <i class="fas fa-clipboard me-1"></i>
                            O.C:
                        </span>
                        <span class="mobile-card-value">${data.oc}</span>
                    </div>
                    <div class="mobile-card-field">
                        <span class="mobile-card-label">
                            <i class="fas fa-align-left me-1"></i>
                            Descripción:
                        </span>
                        <span class="mobile-card-value">${data.descripcion}</span>
                    </div>
                </div>

                <div class="mobile-card-actions">
                    ${actionsHTML}
                </div>
            </div>
        `;
    }

    getTipoBadge(tipo) {
        const tipoUpper = tipo.toUpperCase();
        let badgeClass = 'bg-secondary';
        let icon = 'fas fa-tag';

        if (tipoUpper.includes('PRENDA FINAL')) {
            badgeClass = 'bg-success';
            icon = 'fas fa-check';
        } else if (tipoUpper.includes('MUESTRA')) {
            badgeClass = 'bg-info';
            icon = 'fas fa-camera';
        } else if (tipoUpper.includes('VALIDACION')) {
            badgeClass = 'bg-warning';
            icon = 'fas fa-search';
        }

        return `
            <span class="badge ${badgeClass}">
                <i class="${icon} me-1"></i>
                ${tipo}
            </span>
        `;
    }

    getTextContent(cell) {
        return cell?.textContent?.trim() || '';
    }

    getPlaceholderImage() {
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjRjhGOUZBIi8+CjxwYXRoIGQ9Ik0yMCAyMEg0MFY0MEgyMFYyMFoiIGZpbGw9IiNERUUyRTYiLz4KPHN2Zz4K';
    }

    getEmptyStateHTML() {
        return `
            <div class="text-center p-4" style="color: #6c757d;">
                <i class="fas fa-inbox fa-2x mb-3" style="color: #dee2e6;"></i>
                <p class="mb-0">No hay registros para mostrar</p>
            </div>
        `;
    }

    getErrorCardHTML(index, error) {
        return `
            <div class="mobile-card" style="border-left: 3px solid #dc3545;">
                <div class="mobile-card-body text-center text-danger p-3">
                    <i class="fas fa-exclamation-triangle mb-2"></i>
                    <small>Error cargando registro ${index + 1}</small>
                </div>
            </div>
        `;
    }

    setupTableObserver() {
        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) return;

        this.observer = new MutationObserver((mutations) => {
            let shouldUpdate = false;

            mutations.forEach(mutation => {
                if (mutation.type === 'childList' &&
                    !mutation.target.closest('.mobile-cards-container')) {
                    shouldUpdate = true;
                }
            });

            if (shouldUpdate && this.currentView === 'mobile') {
                console.log('Actualizando cards por cambio en tabla');
                setTimeout(() => this.generateMobileCards(), 100);
            }
        });

        this.observer.observe(tableBody, {
            childList: true,
            subtree: true
        });
    }

    // Método público para actualizar manualmente
    refresh() {
        if (this.currentView === 'mobile') {
            this.generateMobileCards();
        }
    }

    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        window.removeEventListener('resize', () => this.debounceResize());
        this.initialized = false;
        console.log('ResponsiveSystem destruido');
    }
}

// ================================================================
// INICIALIZACIÓN GLOBAL
// ================================================================

console.log('Cargando ResponsiveSystem...');

// Crear instancia única global
window.responsiveSystem = new ResponsiveSystem();

// Inicializar cuando esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.responsiveSystem.init();
    });
} else {
    window.responsiveSystem.init();
}

// Funciones globales para compatibilidad
window.refreshMobileCards = () => {
    if (window.responsiveSystem) {
        window.responsiveSystem.refresh();
    }
};

window.mobileCardsStable = window.responsiveSystem;

console.log('ResponsiveSystem cargado');
