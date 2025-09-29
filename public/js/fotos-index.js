/*!
 * Fotografías de Prendas - Sistema Completo
 * Description: Sistema completo para gestión de fotografías de prendas con comentarios
 *
 * NOTA: todo el javascript funcional es este
 */

// ================================================================================================
// VARIABLES GLOBALES Y CONFIGURACIÓN - CONSOLIDADAS
// ================================================================================================

let currentUser = null;
let currentImageData = null;
let commentsData = new Map();
let bootstrapReady = false;
let uploadCount = 0;
let commentCounterInitialized = false;

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
// FUNCIÓN DE DEBUG AGRESIVO
// ================================================================================================

function debugSystem() {
    console.log('=== DEBUG SISTEMA ===');
    console.log('Upload count:', uploadCount);
    console.log('Bootstrap ready:', bootstrapReady);
    console.log('Current image data:', currentImageData);
    console.log('Comments data size:', commentsData.size);
    console.log('Filtro tipo fotografía:', tipoFotografiaFilter);

    const commentButtons = document.querySelectorAll('.btn-info');
    console.log('Botones comentarios encontrados:', commentButtons.length);

    console.log('=== FIN DEBUG ===');
}

// ================================================================================================
// SISTEMA DE DETECCIÓN DE USUARIOS
// ================================================================================================

function initializeUserSystem() {
    console.log('👤 Inicializando sistema de usuarios...');

    const metaUser = document.querySelector('meta[name="current-user"]');
    if (metaUser && metaUser.content) {
        currentUser = {
            displayName: metaUser.content,
            username: generateUsernameFromDisplayName(metaUser.content),
            source: 'meta-tag'
        };
        console.log('Usuario detectado desde meta tag:', currentUser);
    } else {
        currentUser = {
            displayName: 'Usuario Sistema',
            username: 'usuario-sistema',
            source: 'fallback-hardcoded'
        };
        console.log('Usuario fallback configurado:', currentUser);
    }

    updateUserInterface(currentUser);
}

function generateUsernameFromDisplayName(displayName) {
    if (!displayName) return 'usuario';

    return displayName
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9-]/g, '')
        .substring(0, 20);
}

function updateUserInterface(user) {
    console.log(`👤 Usuario activo: ${user.displayName} (${user.username})`);

    const userDisplayElements = document.querySelectorAll('.current-user-display');
    userDisplayElements.forEach(element => {
        element.textContent = user.displayName;
    });
}

// ================================================================================================
// INICIALIZACIÓN PRINCIPAL - CONSOLIDADA
// ================================================================================================

document.addEventListener("DOMContentLoaded", function () {
    console.log('DOM cargado, iniciando sistema...');

    if (window.fotografiasSystemInitialized) {
        console.warn('Sistema ya inicializado');
        return;
    }

    waitForBootstrap()
        .then(() => {
            console.log('Bootstrap confirmado, iniciando sistema...');
            initializeCompleteSystem();
        })
        .catch((error) => {
            console.error('Error esperando Bootstrap:', error);
            bootstrapReady = false;
            initializeCompleteSystem();
        });
});

function initializeCompleteSystem() {
    if (window.fotografiasSystemInitialized) {
        return;
    }

    window.fotografiasSystemInitialized = true;

    try {
        console.log('Iniciando todos los sistemas...');

        // Sistemas principales
        initializeUserSystem();
        initializeLightbox();
        initializeNotifications();
        initializeSearch();
        initializeCommentsSystem();
        initializeCommentCounterSystem();
        initializeTipoFotografiaFilter();

        console.log('Sistema completo inicializado correctamente');

    } catch (error) {
        console.error('Error durante la inicialización:', error);
        showNotification('Error durante la inicialización: ' + error.message, 'error');
    }
}

// ================================================================================================
// VERIFICACIÓN ROBUSTA DE BOOTSTRAP
// ================================================================================================

function waitForBootstrap() {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        const maxAttempts = 50;

        function checkBootstrap() {
            attempts++;
            console.log(`Verificando Bootstrap - Intento ${attempts}/${maxAttempts}`);

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                console.log('Bootstrap encontrado y funcional');
                bootstrapReady = true;
                resolve(true);
                return;
            }

            if (attempts >= maxAttempts) {
                console.error('Bootstrap no se cargó después de múltiples intentos');
                reject(new Error('Bootstrap no disponible'));
                return;
            }

            setTimeout(checkBootstrap, 100);
        }

        checkBootstrap();
    });
}

// ================================================================================================
// SISTEMA DE COMENTARIOS CON VERIFICACIÓN
// ================================================================================================

function initializeCommentsSystem() {
    console.log('Inicializando sistema de comentarios...');

    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.onsubmit = null;
        commentForm.onsubmit = function (e) {
            e.preventDefault();
            handleCommentSubmit(e);
        };
        console.log('Formulario de comentarios configurado');
    }

    const commentText = document.getElementById('commentText');
    if (commentText) {
        commentText.oninput = updateCharacterCount;
    }

    console.log('Sistema de comentarios inicializado');
}

function initializeCommentCounterSystem() {
    console.log('Inicializando sistema de contador de comentarios...');

    if (commentCounterInitialized) {
        console.log('Sistema de contador ya inicializado');
        return;
    }

    fixExistingCommentButtons();
    commentCounterInitialized = true;
    console.log('Sistema de contador inicializado');
}

function fixExistingCommentButtons() {
    console.log('Corrigiendo botones existentes...');

    const commentButtons = document.querySelectorAll('.comment-btn, .comment-btn-override, .comment-btn-fixed, button[onclick*="openCommentsModal"]');

    commentButtons.forEach((button, index) => {
        const oldBadge = button.querySelector('.comment-count');
        let currentCount = 0;

        if (oldBadge) {
            currentCount = parseInt(oldBadge.getAttribute('data-count') || '0');
            oldBadge.remove();
        }

        button.setAttribute('data-comment-count', currentCount);
        button.style.position = 'relative';
        button.innerHTML = '<i class="fas fa-comments"></i>';

        console.log(`Botón ${index} corregido con contador: ${currentCount}`);
    });
}

// ================================================================================================
// FUNCIONES DE COMENTARIOS - CONSOLIDADAS
// ================================================================================================

function openCommentsModal(button) {
    console.log('openCommentsModal llamado');

    if (!bootstrapReady || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.error('Bootstrap Modal no disponible');
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                console.log('Bootstrap apareció, reintentando...');
                bootstrapReady = true;
                openCommentsModal(button);
            } else {
                showNotification('Error: Modal no disponible. Recarga la página.', 'error');
            }
        }, 500);
        return;
    }

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

    currentImageData = imageData;
    console.log('Datos extraídos:', imageData);

    updateCommentsModalInfo(imageData);
    loadCommentsForImage(imageData.id);

    const modalElement = document.getElementById('commentsModal');
    if (!modalElement) {
        showNotification('Error: Modal no encontrado en el DOM', 'error');
        return;
    }

    try {
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true
        });
        modal.show();
        console.log('Modal abierto correctamente');
    } catch (error) {
        console.error('Error abriendo modal:', error);
        showNotification('Error abriendo modal', 'error');
    }
}

function extractImageDataFromRow(row) {
    const img = row.querySelector('img');
    const ordenSitCell = row.querySelector('[data-column="orden-sit"]');
    const poCell = row.querySelector('[data-column="po"]');
    const ocCell = row.querySelector('[data-column="oc"]');
    const descripcionCell = row.querySelector('[data-column="descripcion"]');
    const tipoCell = row.querySelector('[data-column="tipo-fotografia"]');

    let imageId = row.dataset.imageId;
    if (!imageId) {
        imageId = generateUniqueImageId();
        row.dataset.imageId = imageId;
    }

    return {
        id: imageId,
        imageUrl: img ? img.src : '',
        imageAlt: img ? img.alt : '',
        ordenSit: ordenSitCell ? ordenSitCell.textContent.trim() : '',
        po: poCell ? poCell.textContent.trim() : '',
        oc: ocCell ? ocCell.textContent.trim() : '',
        descripcion: descripcionCell ? descripcionCell.textContent.trim() : '',
        tipo: tipoCell ? tipoCell.textContent.trim() : ''
    };
}

function updateCommentsModalInfo(imageData) {
    const elements = {
        commentImagePreview: document.getElementById('commentImagePreview'),
        commentOrdenSit: document.getElementById('commentOrdenSit'),
        commentPO: document.getElementById('commentPO'),
        commentOC: document.getElementById('commentOC'),
        commentTipo: document.getElementById('commentTipo'),
        commentDescripcion: document.getElementById('commentDescripcion')
    };

    if (elements.commentImagePreview) {
        elements.commentImagePreview.src = imageData.imageUrl;
        elements.commentImagePreview.alt = imageData.imageAlt;
    }

    if (elements.commentOrdenSit) elements.commentOrdenSit.textContent = imageData.ordenSit;
    if (elements.commentPO) elements.commentPO.textContent = imageData.po;
    if (elements.commentOC) elements.commentOC.textContent = imageData.oc;
    if (elements.commentTipo) elements.commentTipo.textContent = imageData.tipo;
    if (elements.commentDescripcion) elements.commentDescripcion.textContent = imageData.descripcion;

    console.log('Información del modal actualizada');
}

function handleCommentSubmit(e) {
    e.preventDefault();
    console.log('handleCommentSubmit llamado');

    if (!currentImageData) {
        showNotification('Error: No hay imagen seleccionada', 'error');
        return;
    }

    const typeElement = document.getElementById('commentType');
    const priorityElement = document.getElementById('commentPriority');
    const textElement = document.getElementById('commentText');

    if (!typeElement || !priorityElement || !textElement) {
        showNotification('Error: Elementos del formulario no encontrados', 'error');
        return;
    }

    const formData = {
        type: typeElement.value,
        priority: priorityElement.value,
        text: textElement.value.trim()
    };

    if (!formData.type || !formData.priority || !formData.text) {
        showNotification('Por favor completa todos los campos', 'warning');
        return;
    }

    const comment = {
        id: generateCommentId(),
        imageId: currentImageData.id,
        type: formData.type,
        priority: formData.priority,
        text: formData.text,
        author: currentUser.displayName,
        authorUsername: currentUser.username,
        timestamp: new Date().toISOString()
    };

    addCommentToStorage(comment);
    renderComment(comment, true);
    updateCommentsCount();
    updateCommentButtonBadge();
    clearCommentForm();

    showNotification(`Comentario agregado por ${currentUser.displayName}`, 'success');
}

function addCommentToStorage(comment) {
    if (!commentsData.has(comment.imageId)) {
        commentsData.set(comment.imageId, []);
    }
    commentsData.get(comment.imageId).push(comment);
    console.log('Comentario guardado:', comment);
}

function loadCommentsForImage(imageId) {
    const comments = commentsData.get(imageId) || [];
    const commentsList = document.getElementById('commentsList');

    if (!commentsList) return;

    commentsList.innerHTML = '';

    if (comments.length === 0) {
        commentsList.innerHTML = `
            <div class="text-center text-muted p-4">
                <i class="fas fa-comment-slash fa-2x mb-2"></i>
                <p>No hay comentarios para esta imagen</p>
            </div>
        `;
    } else {
        comments.forEach(comment => renderComment(comment, false));
    }

    const totalCount = document.getElementById('totalCommentsCount');
    if (totalCount) {
        totalCount.textContent = comments.length;
    }
}

function renderComment(comment, isNew = false) {
    const commentsList = document.getElementById('commentsList');
    if (!commentsList) return;

    const noCommentsMsg = commentsList.querySelector('.text-center.text-muted');
    if (noCommentsMsg) {
        noCommentsMsg.remove();
    }

    const commentDiv = document.createElement('div');
    commentDiv.className = `comment-item ${isNew ? 'new-comment' : ''}`;
    commentDiv.innerHTML = `
        <div class="comment-meta">
            <span class="comment-author">${comment.author}</span>
            <span class="comment-type-badge type-${comment.type}">${getTypeDisplayName(comment.type)}</span>
            <span class="comment-priority priority-${comment.priority}">${getPriorityDisplayName(comment.priority)}</span>
            <span class="comment-timestamp">
                <i class="fas fa-clock"></i>
                ${formatTimestamp(comment.timestamp)}
            </span>
        </div>
        <div class="comment-text">${escapeHtml(comment.text)}</div>
        <div class="comment-actions">
            <button class="btn btn-sm btn-outline-danger" onclick="deleteComment('${comment.id}')">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </div>
    `;

    commentsList.insertBefore(commentDiv, commentsList.firstChild);

    if (isNew) {
        setTimeout(() => {
            commentDiv.classList.remove('new-comment');
        }, 500);
    }
}

function updateCharacterCount() {
    const textElement = document.getElementById('commentText');
    const countElement = document.getElementById('charCount');

    if (!textElement || !countElement) return;

    const length = textElement.value.length;
    countElement.textContent = length;

    countElement.className = '';
    if (length > CONFIG.MAX_COMMENT_LENGTH * 0.8) {
        countElement.classList.add('warning');
    }
    if (length > CONFIG.MAX_COMMENT_LENGTH * 0.9) {
        countElement.classList.add('danger');
    }
}

function clearCommentForm() {
    const elements = ['commentType', 'commentPriority', 'commentText'];
    elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            if (id === 'commentPriority') {
                element.value = 'medium';
            } else {
                element.value = '';
            }
        }
    });
    updateCharacterCount();
}

function updateCommentsCount() {
    if (!currentImageData) return;

    const comments = commentsData.get(currentImageData.id) || [];
    const totalCount = document.getElementById('totalCommentsCount');
    if (totalCount) {
        totalCount.textContent = comments.length;
    }
}

function updateCommentButtonBadge() {
    if (!currentImageData) return;

    const comments = commentsData.get(currentImageData.id) || [];
    const commentCount = comments.length;

    console.log(`Actualizando contador para imagen ${currentImageData.id}: ${commentCount} comentarios`);

    // Buscar el botón de comentarios para esta imagen
    const row = document.querySelector(`tr[data-image-id="${currentImageData.id}"]`);
    if (!row) {
        console.warn(`No se encontró la fila para imagen ${currentImageData.id}`);
        return;
    }

    const commentButton = row.querySelector('.comment-btn, .comment-btn-override, .comment-btn-fixed, button[onclick*="openCommentsModal"]');
    if (!commentButton) {
        console.warn(`No se encontró el botón de comentarios en la fila`);
        return;
    }

    // Usar data-comment-count
    commentButton.setAttribute('data-comment-count', commentCount);

    // Remover el span rojo viejo si existe
    const oldBadge = commentButton.querySelector('.comment-count');
    if (oldBadge) {
        oldBadge.remove();
    }

    // Posición relativa para el contador
    commentButton.style.position = 'relative';

    //  ANIMACIÓN: Pulso cuando se actualiza
    if (commentCount > 0) {
        commentButton.classList.add('comment-added');
        setTimeout(() => {
            commentButton.classList.remove('comment-added');
        }, 600);
    }

    console.log(`Contador actualizado: ${commentCount}`);
}

function deleteComment(commentId) {
    if (confirm('¿Eliminar este comentario?')) {
        for (let [imageId, comments] of commentsData) {
            const index = comments.findIndex(c => c.id === commentId);
            if (index !== -1) {
                comments.splice(index, 1);
                break;
            }
        }

        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (commentElement) {
            commentElement.remove();
        }

        updateCommentsCount();
        updateCommentButtonBadge();
        showNotification('Comentario eliminado', 'success');
    }
}

// ================================================================================================
// FUNCIONES AUXILIARES
// ================================================================================================

function generateUniqueImageId() {
    return 'img_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
}

function generateCommentId() {
    return 'comment_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
}

// ================================================================================================
// SISTEMAS DE COLUMNAS
// ================================================================================================

function toggleColumn(columnName, isVisible) {
    const display = isVisible ? '' : 'none';
    const table = document.querySelector('.images-table');

    if (!table) return;

    const headerCell = table.querySelector(`th[data-column="${columnName}"]`);
    if (headerCell) {
        headerCell.style.display = display;
    }

    const filterCell = table.querySelector(`tr.bg-light td[data-column="${columnName}"]`);
    if (filterCell) {
        filterCell.style.display = display;
    }

    const dataCells = table.querySelectorAll(`tbody td[data-column="${columnName}"]`);
    dataCells.forEach(cell => {
        cell.style.display = display;
    });

    console.log(`Columna ${columnName} ${isVisible ? 'mostrada' : 'ocultada'}`);
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

// ================================================================================================
// FILTRO TIPO FOTOGRAFÍA - CONSOLIDADO
// ================================================================================================

function filterByTipoFotografia() {
    console.log('🏷️ Aplicando filtro por tipo de fotografía...');

    const muestraCheck = document.getElementById('filtroMuestra');
    const prendaFinalCheck = document.getElementById('filtroPrendaFinal');
    const validacionACCheck = document.getElementById('filtroValidacionAC');

    if (!muestraCheck || !prendaFinalCheck || !validacionACCheck) {
        console.error('❌ No se encontraron los checkboxes');
        return;
    }

    tipoFotografiaFilter.selectedTypes = [];

    if (muestraCheck.checked) tipoFotografiaFilter.selectedTypes.push('MUESTRA');
    if (prendaFinalCheck.checked) tipoFotografiaFilter.selectedTypes.push('PRENDA FINAL');
    if (validacionACCheck.checked) tipoFotografiaFilter.selectedTypes.push('VALIDACION AC');

    tipoFotografiaFilter.active = tipoFotografiaFilter.selectedTypes.length > 0;

    console.log('🏷️ Tipos seleccionados:', tipoFotografiaFilter.selectedTypes);

    applyTipoFotografiaFilter();
    updateTipoFotografiaUI();

    // NUEVO: Actualizar indicador visual
    updateFilterStatusIndicator();
}

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

    if (tipoFotografiaFilter.active) {
        const tipos = tipoFotografiaFilter.selectedTypes.join(', ');
        showNotification(`Filtro aplicado: ${tipos} (${visibleCount} registros)`, 'success');
    }
}

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

    // updateTipoFotografiaCounts();
}

// NUEVA FUNCIÓN: Actualizar indicador de estado del filtro
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
    console.log('✅ Seleccionando todos los tipos...');

    const muestraCheck = document.getElementById('filtroMuestra');
    const prendaFinalCheck = document.getElementById('filtroPrendaFinal');
    const validacionACCheck = document.getElementById('filtroValidacionAC');

    if (muestraCheck) muestraCheck.checked = true;
    if (prendaFinalCheck) prendaFinalCheck.checked = true;
    if (validacionACCheck) validacionACCheck.checked = true;

    filterByTipoFotografia();

    showNotification('Todos los tipos seleccionados', 'success');
    console.log('✅ Todos los tipos seleccionados');
}

function clearTipoFotografiaFilter() {
    console.log('🧹 Limpiando filtro de tipo fotografía...');

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

    showNotification('Filtro de tipo fotografía eliminado', 'info');
    console.log('✅ Filtro limpiado');
}

// MEJORAR: Inicialización del filtro con indicadores
function initializeTipoFotografiaFilter() {
    console.log('🏷️ Inicializando filtro de tipo fotografía...');

    // updateTipoFotografiaCounts();
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
                //  updateTipoFotografiaCounts();
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

    console.log('✅ Filtro de tipo fotografía inicializado');
}

// ================================================================================================
// FUNCIONES DE UTILIDAD
// ================================================================================================

function getTypeDisplayName(type) {
    const types = {
        'quality': 'Calidad',
        'technical': 'Técnico',
        'production': 'Producción',
        'design': 'Diseño',
        'general': 'General',
        'urgent': 'Urgente'
    };
    return types[type] || type;
}

function getPriorityDisplayName(priority) {
    const priorities = {
        'low': 'Baja',
        'medium': 'Media',
        'high': 'Alta',
        'critical': 'Crítica'
    };
    return priorities[priority] || priority;
}

function formatTimestamp(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now - date;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
        return 'Hoy ' + date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    } else if (diffDays === 1) {
        return 'Ayer ' + date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    } else {
        return date.toLocaleDateString('es-ES');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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

function showNotification(message, type = 'info', duration = 5000) {
    console.log(`Notificación: [${type.toUpperCase()}] ${message}`);

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
    notification.className = `alert ${alertTypes[type]} notification alert-dismissible fade show`;
    notification.innerHTML = `
        <i class="${icons[type]} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    container.appendChild(notification);

    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
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
        showNotification('Error al abrir la imagen', 'error');
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
        showNotification('Descarga iniciada', 'success');
    } else {
        showNotification('No hay imagen para descargar', 'warning');
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
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    const searchTerm = searchInput.value.trim();
    if (!searchTerm) {
        showNotification('Ingresa un término de búsqueda', 'warning');
        return;
    }

    const tableRows = document.querySelectorAll('#imagesTableBody tr');
    let visibleCount = 0;

    tableRows.forEach(row => {
        const searchableText = row.textContent.toLowerCase();
        const isVisible = searchableText.includes(searchTerm.toLowerCase());
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });

    showNotification(`${visibleCount} resultado(s) encontrado(s)`, 'info');
}

// ================================================================================================
// ACCIONES
// ================================================================================================

function deleteImage(button) {
    if (confirm('¿Eliminar esta imagen?')) {
        const row = button.closest('tr');
        if (row) {
            const imageId = row.dataset.imageId;
            if (imageId && commentsData.has(imageId)) {
                commentsData.delete(imageId);
            }
            row.remove();
            showNotification('Imagen eliminada', 'success');
        }
    }
}

function exportAll() {
    showNotification('Exportando todos los registros...', 'info');
}

function exportSelected() {
    showNotification('Exportando registros seleccionados...', 'info');
}

function showFilters() {
    showNotification('Mostrando filtros avanzados', 'info');
}

// ================================================================================================
// FUNCIONALIDAD BTN EDITAR INFORMACION - fotos-index
// ================================================================================================

// Variables globales para el editor
let editCropper = null;
let originalImageSrc = null;
let currentEditingRow = null;
let hasImageBeenCropped = false;

// Función editImage actualizada
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

    // Llenar el modal con los datos
    populateEditModal(imageData);

    // Reset estado de recorte
    hasImageBeenCropped = false;
    updateResetButtonState();

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('editImageModal'));
    modal.show();
}

// Extraer datos de la imagen desde la fila
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

// Llenar el modal con los datos
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

    // Limpiar preview de nueva foto
    document.getElementById('newPhotoPreview').innerHTML = '';
    document.getElementById('newPhotoInput').value = '';

    console.log('Modal populado con datos:', imageData);
}

// Inicializar funcionalidad de recorte
function initializeCropTool() {
    const cropBtn = document.getElementById('cropImageBtn');
    const applyCropBtn = document.getElementById('applyCropBtn');
    const cancelCropBtn = document.getElementById('cancelCropBtn');
    const resetBtn = document.getElementById('resetImageBtn');
    const cropControls = document.getElementById('cropControls');
    const imageTools = document.querySelector('.image-tools .btn-group');

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
        imageTools.classList.add('d-none');
        cropControls.classList.remove('d-none');
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

            // Actualizar imagen con la versión recortada
            const croppedImageUrl = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('editModalImage').src = croppedImageUrl;

            // Marcar que la imagen ha sido recortada
            hasImageBeenCropped = true;
            updateResetButtonState();

            // Destruir cropper
            editCropper.destroy();
            editCropper = null;

            // Ocultar controles de recorte
            cropControls.classList.add('d-none');
            imageTools.classList.remove('d-none');

            showNotification('Imagen recortada correctamente', 'success');
        }
    });

    cancelCropBtn.addEventListener('click', function () {
        if (editCropper) {
            editCropper.destroy();
            editCropper = null;
        }

        // Ocultar controles de recorte
        cropControls.classList.add('d-none');
        imageTools.classList.remove('d-none');
    });

    // Boton de restablecer
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

                    // Limpiar preview de nueva foto
                    document.getElementById('newPhotoPreview').innerHTML = '';
                    document.getElementById('newPhotoInput').value = '';

                    showNotification('Imagen restablecida correctamente', 'success');
                }
            });
        } else {
            showNotification('No hay cambios que restablecer', 'info');
        }
    });
}

// Actualizar estado del boton restablecer
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

// Manejar subida de nueva foto
function initializePhotoUpload() {
    const uploadBtn = document.getElementById('uploadNewPhotoBtn');
    const fileInput = document.getElementById('newPhotoInput');
    const preview = document.getElementById('newPhotoPreview');

    uploadBtn.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            showNotification('Por favor seleccione un archivo de imagen válido', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = `
                <div class="border rounded p-2 bg-light">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 100%; max-height: 150px;">
                    <div class="mt-1">
                        <small class="text-success">
                            <i class="fas fa-check me-1"></i>
                            Nueva imagen seleccionada: ${file.name}
                        </small>
                    </div>
                </div>
            `;

            // Actualizar la imagen principal del modal
            document.getElementById('editModalImage').src = e.target.result;

            // Marcar que se ha cambiado la imagen
            hasImageBeenCropped = true;
            updateResetButtonState();
        };
        reader.readAsDataURL(file);
    });
}

// Manejar eliminación de foto
function initializePhotoDelete() {
    const deleteBtn = document.getElementById('deletePhotoBtn');

    deleteBtn.addEventListener('click', function () {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará permanentemente la fotografía',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteCurrentPhoto();
            }
        });
    });
}

// Eliminar foto actual
function deleteCurrentPhoto() {
    if (currentEditingRow) {
        // Animacion de eliminacion
        currentEditingRow.style.transition = 'all 0.5s ease';
        currentEditingRow.style.opacity = '0';
        currentEditingRow.style.transform = 'translateX(-100%)';

        setTimeout(() => {
            currentEditingRow.remove();
        }, 500);

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editImageModal'));
        modal.hide();

        showNotification('Fotografía eliminada correctamente', 'success');

        // Reset variables
        resetEditVariables();
    }
}

// Guardar cambios
function saveImageChanges() {
    const newTipo = document.getElementById('editTipoFotografia').value;
    const newDescripcion = document.getElementById('editDescripcion').value;
    const newImageSrc = document.getElementById('editModalImage').src;

    // Validar campos requeridos
    if (!newTipo || !newDescripcion.trim()) {
        showNotification('Por favor complete todos los campos requeridos', 'error');
        return;
    }

    // Mostrar loading
    const saveBtn = document.getElementById('saveChangesBtn');
    saveBtn.classList.add('loading');
    saveBtn.disabled = true;

    // Simular guardado (en producción enviar a servidor)
    setTimeout(() => {
        // Actualizar la fila en la tabla
        updateTableRow(currentEditingRow, { tipo_fotografia: newTipo, descripcion: newDescripcion, nueva_imagen: newImageSrc != originalImageSrc, imagen_src: newImageSrc });

        // Actualizar datos para el lightbox
        updateLightboxData(newTipo, newDescripcion, newImageSrc);

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editImageModal'));
        modal.hide();

        showNotification('Cambios guardados correctamente', 'success');

        // Reset variables
        resetEditVariables();

        // Reset boton
        saveBtn.classList.remove('loading');
        saveBtn.disabled = false;
    }, 1000);
}

// Actualizar datos para el lightbox
function updateLightboxData(newTipo, newDescripcion, newImageSrc) {
    if (currentImageData) {
        // Actualizar datos globales
        currentImageData.tipo = newTipo;
        currentImageData.descripcion = newDescripcion;
        currentImageData.imageUrl = newImageSrc;

        // Si el lightbox esta abierto, actualizarlo
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
        // Tambien el onclick del lightbox
        const newOnclick = `openImageLightbox('${formData.imagen_src}', '${formData.descripcion}', '${formData.tipo_fotografia}')`;
        img.setAttribute('onclick', newOnclick);
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
    row.style.transition = 'background-color 0.5s ease';
    setTimeout(() => {
        row.style.backgroundColor = '';
    }, 2000);
}

// Reset variables del editor
function resetEditVariables() {
    currentEditingRow = null;
    currentImageData = null;
    hasImageBeenCropped = false;
    originalImageSrc = null;

    if (editCropper) {
        editCropper.destroy();
        editCropper = null;
    }
}

// Event listener para cerrar modal
document.addEventListener('DOMContentLoaded', function () {
    // Event listener para guardar cambios
    document.getElementById('saveChangesBtn').addEventListener('click', saveImageChanges);

    // Event listener para limpiar variables al cerrar modal
    const editModal = document.getElementById('editImageModal');
    editModal.addEventListener('hidden.bs.modal', function () {
        resetEditVariables();
    });

    // Inicializar herramientas
    initializeCropTool();
    initializePhotoUpload();
    initializePhotoDelete();

    console.log('Editor de imágenes inicializado');
});

// Hacer función global para ser llamada desde HTML
window.editImage = editImage;

// ================================================================================================

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
// FUNCIONES GLOBALES - CONSOLIDADAS
// ================================================================================================

window.openImageLightbox = openImageLightbox || function () { console.warn('openImageLightbox no definida'); };
window.closeLightbox = closeLightbox || function () { console.warn('closeLightbox no definida'); };
window.downloadImageFromLightbox = downloadImageFromLightbox || function () { console.warn('downloadImageFromLightbox no definida'); };
window.searchRecords = searchRecords || function () { console.warn('searchRecords no definida'); };
window.exportAll = exportAll || function () { console.warn('exportAll no definida'); };
window.exportSelected = exportSelected || function () { console.warn('exportSelected no definida'); };
window.showFilters = showFilters || function () { console.warn('showFilters no definida'); };
window.deleteImage = deleteImage || function () { console.warn('deleteImage no definida'); };
window.editImage = editImage || function () { console.warn('editImage no definida'); };
window.openCommentsModal = openCommentsModal;
window.deleteComment = deleteComment || function () { console.warn('deleteComment no definida'); };
window.debugSystem = debugSystem;
window.openHistorialModal = openHistorialModal;
window.filterByTipoFotografia = filterByTipoFotografia;
window.selectAllTipoFotografia = selectAllTipoFotografia || function () { console.warn('selectAllTipoFotografia no definida'); };
window.clearTipoFotografiaFilter = clearTipoFotografiaFilter || function () { console.warn('clearTipoFotografiaFilter no definida'); };

console.log('Sistema JS completo cargado correctamente');
