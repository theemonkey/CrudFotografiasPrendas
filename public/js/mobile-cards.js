/*!
 * Mobile Cards Generator
 * Convierte tabla de imágenes en cards responsive para móviles
 */

// ================================================================
// GENERADOR DE CARDS RESPONSIVE PARA MÓVILES
// ================================================================

class MobileCardsStable {
    constructor() {
        this.initialized = false;
        this.isGenerating = false; // ✅ Flag para evitar bucles
        this.lastGenerationTime = 0;
        this.generationCooldown = 1000; // 1 segundo de cooldown
        this.observer = null;
    }

    // ✅ INIT controlado sin bucles
    init() {
        if (this.initialized) {
            console.log('📱 [STABLE] Ya inicializado, saltando...');
            return;
        }

        console.log('📱 [STABLE] Iniciando Mobile Cards Generator Estable...');

        // ✅ SOLO una ejecución inicial
        this.generateCardsOnce();

        // ✅ Observer inteligente (sin bucles)
        this.setupSmartObserver();

        this.initialized = true;
        console.log('✅ [STABLE] Mobile Cards Generator inicializado');
    }

    // ✅ GENERACIÓN única con protección anti-bucle
    generateCardsOnce() {
        const now = Date.now();

        // ✅ PROTECCIÓN: Si se está generando o es muy pronto, salir
        if (this.isGenerating || (now - this.lastGenerationTime) < this.generationCooldown) {
            console.log('📱 [STABLE] Generación bloqueada (anti-bucle)');
            return;
        }

        this.isGenerating = true;
        this.lastGenerationTime = now;

        console.log('📱 [STABLE] Generando cards (protegido)...');

        try {
            this.createMobileCardsSafe();
        } catch (error) {
            console.error('❌ [STABLE] Error en generación:', error);
        } finally {
            // ✅ LIBERAR flag después de un tiempo
            setTimeout(() => {
                this.isGenerating = false;
            }, 500);
        }
    }

    // ✅ CREACIÓN segura de cards
    createMobileCardsS安e() {
        const tableBody = document.getElementById('imagesTableBody');
        let mobileContainer = document.querySelector('.mobile-cards-container');

        if (!tableBody) {
            console.error('❌ [STABLE] No se encontró tabla');
            return;
        }

        // ✅ CREAR contenedor si no existe
        if (!mobileContainer) {
            mobileContainer = this.createContainerSafely();
            if (!mobileContainer) return;
        }

        // ✅ OBTENER filas de forma segura
        const rows = this.getTableRowsSafely(tableBody);
        console.log(`📱 [STABLE] Procesando ${rows.length} filas`);

        if (rows.length === 0) {
            this.showEmptyState(mobileContainer);
            return;
        }

        // ✅ GENERAR cards de forma segura
        this.generateCardsHTML(mobileContainer, rows);
    }

    // ✅ CREAR contenedor de forma segura
    createContainerSafely() {
        const cardBody = document.querySelector('.card-body');
        if (!cardBody) {
            console.error('❌ [STABLE] No se encontró card-body');
            return null;
        }

        const container = document.createElement('div');
        container.className = 'mobile-cards-container mobile-only';
        container.style.display = 'block';

        // ✅ INSERTAR antes de la tabla para evitar conflictos
        const table = cardBody.querySelector('.table-responsive');
        if (table) {
            cardBody.insertBefore(container, table);
        } else {
            cardBody.appendChild(container);
        }

        console.log('✅ [STABLE] Contenedor móvil creado seguramente');
        return container;
    }

    // ✅ OBTENER filas de forma segura
    getTableRowsSafely(tableBody) {
        try {
            const allRows = Array.from(tableBody.querySelectorAll('tr'));
            // ✅ FILTRAR filas válidas (que tengan al menos una imagen o datos)
            return allRows.filter(row => {
                const cells = row.querySelectorAll('td');
                return cells.length >= 3; // Al menos 3 celdas para ser válida
            });
        } catch (error) {
            console.error('❌ [STABLE] Error obteniendo filas:', error);
            return [];
        }
    }

    // ✅ MOSTRAR estado vacío
    showEmptyState(container) {
        container.innerHTML = `
            <div class="text-center text-muted p-4">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <p>No hay registros para mostrar</p>
            </div>
        `;
    }

    // ✅ GENERAR HTML de cards de forma segura
    generateCardsHTML(container, rows) {
        try {
            const cardsHTML = rows.map((row, index) => {
                return this.createSingleCard(row, index);
            }).filter(Boolean).join('');

            if (cardsHTML) {
                container.innerHTML = cardsHTML;
                console.log(`✅ [STABLE] ${rows.length} cards generados`);
            } else {
                this.showEmptyState(container);
            }

        } catch (error) {
            console.error('❌ [STABLE] Error generando HTML:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error cargando contenido
                </div>
            `;
        }
    }

    // ✅ CREAR card individual de forma segura
    createSingleCard(row, index) {
        try {
            const cells = row.querySelectorAll('td');
            if (cells.length < 6) {
                console.warn(`⚠️ [STABLE] Fila ${index} incompleta`);
                return '';
            }

            // ✅ EXTRAER datos con fallbacks seguros
            const data = {
                ordenSit: this.getTextSafely(cells[1]) || `Registro ${index + 1}`,
                po: this.getTextSafely(cells[2]) || 'N/A',
                oc: this.getTextSafely(cells[3]) || 'N/A',
                descripcion: this.getTextSafely(cells[4]) || 'Sin descripción',
                tipo: this.getTextSafely(cells[5]) || 'Sin tipo'
            };

            // ✅ MANEJAR imagen de forma segura
            const img = cells[0]?.querySelector('img');
            const imgData = this.getImageDataSafely(img, index);

            return this.generateCardHTML(data, imgData, index);

        } catch (error) {
            console.error(`❌ [STABLE] Error en card ${index}:`, error);
            return '';
        }
    }

    // ✅ EXTRAER texto de forma segura
    getTextSafely(cell) {
        try {
            return cell?.textContent?.trim() || '';
        } catch {
            return '';
        }
    }

    // ✅ EXTRAER datos de imagen de forma segura
    getImageDataSafely(img, index) {
        if (!img) {
            return {
                src: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60"><rect width="60" height="60" fill="%23f8f9fa"/><text x="30" y="35" text-anchor="middle" fill="%23666">No img</text></svg>',
                alt: `Imagen ${index + 1}`,
                onclick: ''
            };
        }

        return {
            src: img.src || 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60"><rect width="60" height="60" fill="%23f8f9fa"/><text x="30" y="35" text-anchor="middle" fill="%23666">Error</text></svg>',
            alt: img.alt || `Imagen ${index + 1}`,
            onclick: img.getAttribute('onclick') || ''
        };
    }

    // ✅ GENERAR HTML del card
    generateCardHTML(data, imgData, index) {
        const tipoBadge = this.getTipoBadge(data.tipo);

        return `
            <div class="mobile-card" data-card-id="${index}">
                <div class="mobile-card-header">
                    <img src="${imgData.src}"
                         alt="${imgData.alt}"
                         class="mobile-card-image"
                         ${imgData.onclick ? `onclick="${imgData.onclick}"` : ''}
                         onerror="this.src='data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;60&quot; height=&quot;60&quot;><rect width=&quot;60&quot; height=&quot;60&quot; fill=&quot;%23f8f9fa&quot;/><text x=&quot;30&quot; y=&quot;35&quot; text-anchor=&quot;middle&quot; fill=&quot;%23666&quot;>Error</text></svg>'">
                    <div class="mobile-card-info">
                        <div class="mobile-card-title">${data.ordenSit}</div>
                        <div class="mobile-card-subtitle">${tipoBadge}</div>
                    </div>
                </div>
                <div class="mobile-card-body">
                    <div class="mobile-card-field">
                        <span class="mobile-card-field-label">P.O:</span>
                        <span class="mobile-card-field-value">${data.po}</span>
                    </div>
                    <div class="mobile-card-field">
                        <span class="mobile-card-field-label">O.C:</span>
                        <span class="mobile-card-field-value">${data.oc}</span>
                    </div>
                    <div class="mobile-card-field">
                        <span class="mobile-card-field-label">Descripción:</span>
                        <span class="mobile-card-field-value">${data.descripcion}</span>
                    </div>
                </div>
                <div class="mobile-card-actions">
                    <button class="btn btn-danger btn-sm" onclick="deleteImage(this)" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="editImage(this)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-info btn-sm" onclick="openCommentsModal(this)" title="Comentarios">
                        <i class="fas fa-comments"></i>
                    </button>
                </div>
            </div>
        `;
    }

    // ✅ BADGE de tipo
    getTipoBadge(tipo) {
        const tipoClean = tipo.toUpperCase();
        let badgeClass = 'bg-secondary';

        if (tipoClean.includes('PRENDA FINAL')) {
            badgeClass = 'bg-success';
        } else if (tipoClean.includes('MUESTRA')) {
            badgeClass = 'bg-info';
        }

        return `<span class="badge ${badgeClass}">${tipo}</span>`;
    }

    // ✅ OBSERVER inteligente sin bucles
    setupSmartObserver() {
        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) return;

        // ✅ OBSERVER que NO se activa por sus propios cambios
        this.observer = new MutationObserver((mutations) => {
            let shouldUpdate = false;

            // ✅ FILTRAR solo cambios relevantes
            mutations.forEach(mutation => {
                // ✅ IGNORAR cambios en el contenedor móvil
                if (mutation.target.closest('.mobile-cards-container')) {
                    return;
                }

                // ✅ SOLO reaccionar a cambios en tbody
                if (mutation.type === 'childList' &&
                    mutation.target === tableBody) {
                    shouldUpdate = true;
                }
            });

            if (shouldUpdate && !this.isGenerating) {
                console.log('👀 [STABLE] Cambio relevante detectado');
                setTimeout(() => this.generateCardsOnce(), 200);
            }
        });

        // ✅ OBSERVAR solo el tbody, no todo el árbol
        this.observer.observe(tableBody, {
            childList: true,
            subtree: false // ✅ NO observar cambios internos
        });

        console.log('👀 [STABLE] Observer inteligente configurado');
    }

    // ✅ DESTRUIR observer
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }
        this.initialized = false;
        console.log('🗑️ [STABLE] Mobile Cards destruido');
    }
}

// ================================================================
// INICIALIZACIÓN CONTROLADA
// ================================================================

console.log('📱 [STABLE] Cargando Mobile Cards Estable...');

// ✅ UNA SOLA instancia global
if (!window.mobileCardsStable) {
    window.mobileCardsStable = new MobileCardsStable();

    // ✅ INICIALIZAR solo una vez
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.mobileCardsStable.init();
        });
    } else {
        window.mobileCardsStable.init();
    }

    // ✅ FUNCIÓN global para llamada manual
    window.refreshMobileCards = () => {
        if (window.mobileCardsStable && !window.mobileCardsStable.isGenerating) {
            window.mobileCardsStable.generateCardsOnce();
        }
    };
}
