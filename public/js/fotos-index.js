/*!
 * Fotografías de Prendas - Sistema Completo
 * Description: Sistema completo para gestión de fotografías de prendas con comentarios
 *
 * NOTA: todo el javascript funcional es este
 */

// ================================================================================================
// VARIABLES GLOBALES Y CONFIGURACIÓN
// ================================================================================================

let currentUser = null;
let currentImageData = null;
let commentsData = new Map();
let bootstrapReady = false;
let uploadInProgress = false; //  NUEVO: Prevenir subidas múltiples
let uploadCount = 0; // Nuevo para validar carga de imagenes duplicadas (corregir, aun se suben varias imagenes)
let commentCounterInitialized = false;

const CONFIG = {
    MAX_FILE_SIZE: 10 * 1024 * 1024,
    MAX_COMMENT_LENGTH: 500,
    DEBUG_MODE: true
};

// ================================================================================================
// FUNCIÓN DE DEBUG AGRESIVO
// ================================================================================================

function debugSystem() {
    console.log(' === DEBUG SISTEMA ===');
    console.log(' Upload en progreso:', uploadInProgress);
    console.log(' Upload count:', uploadCount);
    console.log(' Bootstrap ready:', bootstrapReady);
    console.log(' Elementos upload:', {
        cameraUpload: !!document.getElementById('cameraUpload'),
        fileUpload: !!document.getElementById('fileUpload'),
        cameraInput: !!document.getElementById('cameraInput'),
        fileInput: !!document.getElementById('fileInput')
    });

    const commentButtons = document.querySelectorAll('.btn-info');
    console.log(' Botones comentarios encontrados:', commentButtons.length);
    commentButtons.forEach((btn, index) => {
        console.log(`  - Botón ${index}:`, {
            onclick: btn.getAttribute('onclick'),
            classes: btn.className,
            color: window.getComputedStyle(btn).backgroundColor
        });
    });

    console.log(' === FIN DEBUG ===');
}

// ================================================================================================
// LIMPIEZA TOTAL DE EVENTOS
// ================================================================================================

function clearAllUploadEvents() {
    console.log(' Limpiando TODOS los eventos de subida...');

    const elements = ['cameraUpload', 'fileUpload', 'cameraInput', 'fileInput'];

    elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            // Clonar elemento para eliminar TODOS los event listeners
            const newElement = element.cloneNode(true);
            element.parentNode.replaceChild(newElement, element);
            console.log(` Elemento ${id} clonado y reemplazado`);
        }
    });

    console.log(' Todos los eventos de subida limpiados');
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
            console.log(` Verificando Bootstrap - Intento ${attempts}/${maxAttempts}`);

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                console.log(' Bootstrap encontrado y funcional');
                bootstrapReady = true;
                resolve(true);
                return;
            }

            if (attempts >= maxAttempts) {
                console.error(' Bootstrap no se cargó después de múltiples intentos');
                reject(new Error('Bootstrap no disponible'));
                return;
            }

            setTimeout(checkBootstrap, 100);
        }

        checkBootstrap();
    });
}

// ================================================================================================
// INICIALIZACIÓN CON ESPERA DE BOOTSTRAP
// ================================================================================================

document.addEventListener("DOMContentLoaded", function () {
    console.log(' DOM cargado, esperando Bootstrap...');

    if (window.fotografiasSystemInitialized) {
        console.warn(' Sistema ya inicializado');
        return;
    }

    waitForBootstrap()
        .then(() => {
            console.log(' Bootstrap confirmado, iniciando sistema...');
            initializeSystem();
        })
        .catch((error) => {
            console.error(' Error esperando Bootstrap:', error);
            bootstrapReady = false;
            initializeSystem();
        });
});

function initializeSystem() {
    if (window.fotografiasSystemInitialized) {
        return;
    }

    window.fotografiasSystemInitialized = true;

    try {
        console.log(' Iniciando todos los sistemas...');

        initializeUserSystem();
        initializeAutoDateFilter();
        initializeColumnToggle();
        initializeLightbox();
        initializeNotifications();
        initializeSearch();
        initializeUploadButtons();
        initializeCommentsSystem();
        initializeCommentCounterSystem();

        initializeDateRangeUnified();
        initializeTipoFotografiaFilter(); /*Ver archivo filtro-tipo-fotografia*/

        console.log(' Sistema completo inicializado correctamente');

    } catch (error) {
        console.error(' Error durante la inicialización:', error);
        showNotification('Error durante la inicialización: ' + error.message, 'error');
    }
}

// ================================================================================================
// SISTEMA DE DETECCIÓN DE USUARIOS
// ================================================================================================

function initializeUserSystem() {
    console.log(' Inicializando sistema de usuarios...');

    const metaUser = document.querySelector('meta[name="current-user"]');
    if (metaUser && metaUser.content) {
        currentUser = {
            displayName: metaUser.content,
            username: generateUsernameFromDisplayName(metaUser.content),
            source: 'meta-tag'
        };
        console.log(' Usuario detectado desde meta tag:', currentUser);
    } else {
        currentUser = {
            displayName: 'Will-AGW',
            username: 'will-agw',
            source: 'fallback-hardcoded'
        };
        console.log(' Usuario fallback configurado:', currentUser);
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
    console.log(` Usuario activo: ${user.displayName} (${user.username})`);

    const userDisplayElements = document.querySelectorAll('.current-user-display');
    userDisplayElements.forEach(element => {
        element.textContent = user.displayName;
    });
}

// ================================================================================================
// SISTEMA DE SUBIDA DE ARCHIVOS - CORREGIDO PARA EVITAR DUPLICADOS
// ================================================================================================

function initializeUploadButtons() {
    console.log(' Inicializando sistema de subida...');

    const cameraUpload = document.getElementById('cameraUpload');
    const fileUpload = document.getElementById('fileUpload');
    const cameraInput = document.getElementById('cameraInput');
    const fileInput = document.getElementById('fileInput');

    console.log(' Elementos encontrados:', {
        cameraUpload: !!cameraUpload,
        fileUpload: !!fileUpload,
        cameraInput: !!cameraInput,
        fileInput: !!fileInput
    });

    if (!cameraUpload || !fileUpload || !cameraInput || !fileInput) {
        console.error(' Elementos de subida no encontrados');
        return;
    }

    //  CORREGIDO: Limpiar TODOS los eventos previos
    cameraUpload.onclick = null;
    fileUpload.onclick = null;
    cameraInput.onchange = null;
    fileInput.onchange = null;

    // Remover todos los listeners duplicados
    cameraUpload.removeEventListener('click', handleCameraClick);
    fileUpload.removeEventListener('click', handleFileClick);
    cameraInput.removeEventListener('change', handleCameraChange);
    fileInput.removeEventListener('change', handleFileChange);

    //  CORREGIDO: Agregar eventos únicos con prevención de duplicados
    cameraUpload.addEventListener('click', handleCameraClick, { once: false });
    fileUpload.addEventListener('click', handleFileClick, { once: false });
    cameraInput.addEventListener('change', handleCameraChange, { once: false });
    fileInput.addEventListener('change', handleFileChange, { once: false });

    console.log(' Sistema de subida inicializado sin duplicados');
}

//  CORREGIDO: Funciones separadas para cada evento
function handleCameraClick(e) {
    e.preventDefault();
    e.stopPropagation();

    if (uploadInProgress) {
        console.log(' Subida en progreso, ignorando click');
        return;
    }

    console.log(' Click en botón cámara');
    const cameraInput = document.getElementById('cameraInput');
    if (cameraInput) {
        cameraInput.click();
    }
}

function handleFileClick(e) {
    e.preventDefault();
    e.stopPropagation();

    if (uploadInProgress) {
        console.log(' Subida en progreso, ignorando click');
        return;
    }

    console.log(' Click en botón archivo');
    const fileInput = document.getElementById('fileInput');
    if (fileInput) {
        fileInput.click();
    }
}

function handleCameraChange(e) {
    console.log(' Cambio en input cámara:', e.target.files.length);
    if (e.target.files.length > 0 && !uploadInProgress) {
        handleImageUpload(e.target.files, 'camera');
        e.target.value = ''; // Limpiar para permitir seleccionar la misma imagen
    }
}

function handleFileChange(e) {
    console.log(' Cambio en input archivo:', e.target.files.length);
    if (e.target.files.length > 0 && !uploadInProgress) {
        handleImageUpload(e.target.files, 'file');
        e.target.value = ''; // Limpiar para permitir seleccionar la misma imagen
    }
}

function handleImageUpload(files, source) {
    //  CORREGIDO: Prevenir múltiples subidas
    if (uploadInProgress) {
        console.log(' Subida ya en progreso, cancelando');
        return;
    }

    uploadInProgress = true;
    console.log(` handleImageUpload llamado con ${files.length} archivo(s) desde ${source}`);

    if (!files || files.length === 0) {
        uploadInProgress = false;
        showNotification('No se seleccionaron archivos', 'warning');
        return;
    }

    const file = files[0]; // Solo el primer archivo
    console.log(` Procesando archivo: ${file.name} (${file.size} bytes, tipo: ${file.type})`);

    // Validar archivo
    if (!file.type.startsWith('image/')) {
        uploadInProgress = false;
        showNotification('El archivo debe ser una imagen', 'error');
        console.error(' Archivo no es imagen:', file.type);
        return;
    }

    if (file.size > CONFIG.MAX_FILE_SIZE) {
        uploadInProgress = false;
        showNotification('El archivo es demasiado grande (máximo 10MB)', 'error');
        console.error(' Archivo muy grande:', file.size);
        return;
    }

    // Mostrar estado de subida
    const uploadBtn = source === 'camera'
        ? document.getElementById('cameraUpload')
        : document.getElementById('fileUpload');

    if (uploadBtn) {
        uploadBtn.classList.add('uploading');
        console.log(' Estado de subida activado');
    }

    // Crear datos de imagen
    try {
        const imageUrl = URL.createObjectURL(file);
        const imageData = {
            id: generateUniqueImageId(),
            url: imageUrl,
            name: file.name,
            size: file.size,
            uploadDate: new Date().toISOString(),
            ordenSit: generateOrderNumber(),
            po: generatePONumber(),
            oc: generateOCNumber(),
            descripcion: 'Imagen subida',
            tipoFotografia: 'SUBIDA MANUAL'
        };

        console.log(' Datos de imagen creados:', imageData);

        //  CORREGIDO: Simular delay y luego agregar SOLO UNA vez
        setTimeout(() => {
            addImageToTable(imageData);

            if (uploadBtn) {
                uploadBtn.classList.remove('uploading');
                uploadBtn.classList.add('active');
                setTimeout(() => {
                    uploadBtn.classList.remove('active');
                }, 2000);
            }

            //  IMPORTANTE: Liberar el flag de subida
            uploadInProgress = false;

            showNotification(`Imagen "${file.name}" subida correctamente`, 'success');
            console.log(' Imagen agregada a tabla exitosamente');
        }, 1500); // Delay para evitar duplicados

    } catch (error) {
        console.error(' Error procesando imagen:', error);
        showNotification('Error al procesar la imagen: ' + error.message, 'error');

        if (uploadBtn) {
            uploadBtn.classList.remove('uploading');
        }

        uploadInProgress = false;
    }
}

function addImageToTable(imageData) {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) {
        console.error(' Tabla no encontrada');
        return;
    }

    const row = document.createElement('tr');
    row.dataset.imageId = imageData.id;
    row.dataset.uploadDate = imageData.uploadDate;

    row.innerHTML = `
        <td data-column="imagen">
            <img src="${imageData.url}"
                 alt="${imageData.name}"
                 class="img-thumbnail preview-image"
                 style="width: 60px; height: 60px; cursor: pointer;"
                 onclick="openImageLightbox('${imageData.url}', '${imageData.name}', '${imageData.descripcion}', '${imageData.tipoFotografia}')">
            <div class="upload-user-badge" title="Subido por ${currentUser.displayName}">
                <i class="fas fa-user"></i> ${currentUser.username}
            </div>
        </td>
        <td data-column="orden-sit">${imageData.ordenSit}</td>
        <td data-column="po">${imageData.po}</td>
        <td data-column="oc">${imageData.oc}</td>
        <td data-column="descripcion">${imageData.descripcion}</td>
        <td data-column="tipo-fotografia">
            <span class="badge bg-info">${imageData.tipoFotografia}</span>
        </td>
        <td data-column="acciones">
            <button class="btn btn-danger btn-sm me-1 btn-delete" onclick="deleteImage(this)" title="Eliminar imagen">
                <i class="fas fa-trash"></i> Eliminar
            </button>
            <button class="btn btn-warning btn-sm me-1 btn-edit" onclick="editImage(this)" title="Editar información">
                <i class="fas fa-edit"></i> Editar
            </button>
            <button class="btn btn-info btn-sm comment-btn"
                    onclick="openCommentsModal(this)"
                    title="Ver/Agregar comentarios"
                    data-comment-count="0"
                    style="background-color: #17a2b8 !important; border-color: #17a2b8 !important; color: white !important; position: relative;">
                <i class="fas fa-comments"></i>
            </button>
        </td>
    `;

    // VERIFICAR que no existe ya esta imagen
    const existingRow = tableBody.querySelector(`tr[data-image-id="${imageData.id}"]`);
    if (existingRow) {
        console.log(' Imagen ya existe en tabla, no agregando duplicado');
        return;
    }

    tableBody.insertBefore(row, tableBody.firstChild);

    // Animación
    row.style.opacity = '0';
    row.style.transform = 'translateY(-10px)';
    setTimeout(() => {
        row.style.transition = 'all 0.5s ease';
        row.style.opacity = '1';
        row.style.transform = 'translateY(0)';
    }, 100);

    console.log(` Imagen agregada a tabla: ${imageData.id}`);
}

function generateUniqueImageId() {
    return `img_${Date.now()}_${Math.random().toString(36).substring(2, 9)}`;
}

function generateOrderNumber() {
    return '100' + Math.floor(Math.random() * 90000 + 10000);
}

function generatePONumber() {
    return '6000' + Math.floor(Math.random() * 900000 + 100000);
}

function generateOCNumber() {
    return '4200' + Math.floor(Math.random() * 9000000 + 1000000);
}

// ================================================================================================
// SISTEMA DE COMENTARIOS CON VERIFICACIÓN BOOTSTRAP
// ================================================================================================

function initializeCommentsSystem() {
    console.log(' Inicializando sistema de comentarios...');

    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.onsubmit = null;
        commentForm.onsubmit = function (e) {
            e.preventDefault();
            handleCommentSubmit(e);
        };
        console.log(' Formulario de comentarios configurado');
    }

    const commentText = document.getElementById('commentText');
    if (commentText) {
        commentText.oninput = updateCharacterCount;
    }

    console.log(' Sistema de comentarios inicializado');
}

//  AGREGAR esta función después de initializeCommentsSystem():

function initializeCommentCounterSystem() {
    console.log(' Inicializando sistema de contador de comentarios...');

    if (commentCounterInitialized) {
        console.log(' Sistema de contador ya inicializado');
        return;
    }

    // Corregir botones existentes
    fixExistingCommentButtons();

    commentCounterInitialized = true;
    console.log(' Sistema de contador inicializado');
}

function fixExistingCommentButtons() {
    console.log(' Corrigiendo botones existentes...');

    const commentButtons = document.querySelectorAll('.comment-btn, .comment-btn-override, .comment-btn-fixed, button[onclick*="openCommentsModal"]');

    commentButtons.forEach((button, index) => {
        // Obtener contador actual del span viejo
        const oldBadge = button.querySelector('.comment-count');
        let currentCount = 0;

        if (oldBadge) {
            currentCount = parseInt(oldBadge.getAttribute('data-count') || '0');
            oldBadge.remove(); // Eliminar el span rojo
        }

        // Establecer el contador en el atributo data
        button.setAttribute('data-comment-count', currentCount);
        button.style.position = 'relative';

        // Limpiar contenido y dejar solo el ícono
        button.innerHTML = '<i class="fas fa-comments"></i>';

        console.log(` Botón ${index} corregido con contador: ${currentCount}`);
    });
}


//=====================================================//

function openCommentsModal(button) {
    console.log(' openCommentsModal llamado');
    console.log(' Bootstrap disponible:', bootstrapReady, typeof bootstrap);

    //  CORREGIDO: Verificación más robusta
    if (!bootstrapReady || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.error('❌ Bootstrap Modal no disponible');

        // Intentar esperar un poco más
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                console.log(' Bootstrap apareció, reintentando...');
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
    console.log(' Datos extraídos:', imageData);

    updateCommentsModalInfo(imageData);
    loadCommentsForImage(imageData.id);

    const modalElement = document.getElementById('commentsModal');
    if (!modalElement) {
        showNotification('Error: Modal no encontrado en el DOM', 'error');
        return;
    }

    try {
        console.log(' Creando instancia de Bootstrap Modal...');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true
        });

        console.log(' Mostrando modal...');
        modal.show();
        console.log('✅ Modal abierto correctamente');

    } catch (error) {
        console.error(' Error abriendo modal:', error);

        // Fallback manual
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.style.backgroundColor = 'rgba(0,0,0,0.5)';
        document.body.classList.add('modal-open');

        showNotification('Modal abierto en modo de compatibilidad', 'warning');

        modalElement.onclick = function (e) {
            if (e.target === modalElement) {
                closeCommentsModalManually();
            }
        };
    }
}

function closeCommentsModalManually() {
    const modalElement = document.getElementById('commentsModal');
    if (modalElement) {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
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

    console.log(' Información del modal actualizada');
}

function handleCommentSubmit(e) {
    e.preventDefault();
    console.log(' handleCommentSubmit llamado');

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

function generateCommentId() {
    return 'comment_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
}

function addCommentToStorage(comment) {
    if (!commentsData.has(comment.imageId)) {
        commentsData.set(comment.imageId, []);
    }
    commentsData.get(comment.imageId).push(comment);
    console.log(' Comentario guardado:', comment);
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

//  REEMPLAZAR ESTA FUNCIÓN COMPLETA:
function updateCommentButtonBadge() {
    if (!currentImageData) return;

    const comments = commentsData.get(currentImageData.id) || [];
    const commentCount = comments.length;

    console.log(` Actualizando contador para imagen ${currentImageData.id}: ${commentCount} comentarios`);

    // Buscar el botón de comentarios para esta imagen
    const row = document.querySelector(`tr[data-image-id="${currentImageData.id}"]`);
    if (!row) {
        console.warn(` No se encontró la fila para imagen ${currentImageData.id}`);
        return;
    }

    const commentButton = row.querySelector('.comment-btn, .comment-btn-override, .comment-btn-fixed, button[onclick*="openCommentsModal"]');
    if (!commentButton) {
        console.warn(` No se encontró el botón de comentarios en la fila`);
        return;
    }

    //  NUEVO: Usar data-comment-count en lugar del span rojo
    commentButton.setAttribute('data-comment-count', commentCount);

    //  LIMPIAR: Remover el span rojo viejo si existe
    const oldBadge = commentButton.querySelector('.comment-count');
    if (oldBadge) {
        oldBadge.remove();
    }

    //  ASEGURAR: Posición relativa para el contador
    commentButton.style.position = 'relative';

    //  ANIMACIÓN: Pulso cuando se actualiza
    if (commentCount > 0) {
        commentButton.classList.add('comment-added');
        setTimeout(() => {
            commentButton.classList.remove('comment-added');
        }, 600);
    }

    console.log(` Contador actualizado: ${commentCount}`);
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
// SISTEMA DE COLUMNAS - FILAS TIPO FOTOGRAFIA
// ================================================================================================

function initializeColumnToggle() {
    console.log(' Inicializando control de columnas...');

    const dropdown = document.getElementById('columnsDropdown');
    if (!dropdown) {
        console.warn(' Dropdown de columnas no encontrado');
        return;
    }

    dropdown.onclick = function (e) {
        if (e.target.type === 'checkbox') {
            e.stopPropagation();

            const columnName = e.target.dataset.column;
            const isVisible = e.target.checked;

            if (columnName) {
                toggleColumn(columnName, isVisible);
                showNotification(
                    `Columna "${getColumnDisplayName(columnName)}" ${isVisible ? 'mostrada' : 'ocultada'}`,
                    'info'
                );
            }
        }
    };

    console.log(' Control de columnas inicializado');
}

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

    console.log(` Columna ${columnName} ${isVisible ? 'mostrada' : 'ocultada'}`);
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

// ================================================================
// VARIABLES GLOBALES DEL FILTRO TIPO FOTOGRAFIA
// ================================================================

let tipoFotografiaFilter = {
    active: false,
    selectedTypes: [],
    totalCounts: {
        'MUESTRA': 0,
        'PRENDA FINAL': 0,
        'VALIDACION AC': 0
    }
};

// ================================================================
// FUNCIÓN PRINCIPAL DE FILTRADO
// ================================================================

function filterByTipoFotografia() {
    console.log(' Aplicando filtro por tipo de fotografía...');

    //  OBTENER checkboxes seleccionados
    const muestraCheck = document.getElementById('filtroMuestra');
    const prendaFinalCheck = document.getElementById('filtroPrendaFinal');
    const validacionACCheck = document.getElementById('filtroValidacionAC');

    if (!muestraCheck || !prendaFinalCheck || !validacionACCheck) {
        console.error(' No se encontraron los checkboxes');
        return;
    }

    //  ACTUALIZAR estado del filtro
    tipoFotografiaFilter.selectedTypes = [];

    if (muestraCheck.checked) {
        tipoFotografiaFilter.selectedTypes.push('MUESTRA');
    }

    if (prendaFinalCheck.checked) {
        tipoFotografiaFilter.selectedTypes.push('PRENDA FINAL');
    }

    if (validacionACCheck.checked) {
        tipoFotografiaFilter.selectedTypes.push('VALIDACION AC');
    }

    // DETERMINAR si el filtro está activo
    tipoFotografiaFilter.active = tipoFotografiaFilter.selectedTypes.length > 0;

    console.log(' Tipos seleccionados:', tipoFotografiaFilter.selectedTypes);

    //  APLICAR filtro a las filas
    applyTipoFotografiaFilter();

    //  ACTUALIZAR interfaz
    updateTipoFotografiaUI();
}

// ================================================================
// APLICAR FILTRO A LAS FILAS
// ================================================================

function applyTipoFotografiaFilter() {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) {
        console.error(' No se encontró el tbody de la tabla');
        return;
    }

    const rows = tableBody.querySelectorAll('tr');
    let visibleCount = 0;
    let hiddenCount = 0;

    rows.forEach(row => {
        const tipoCell = row.querySelector('td[data-column="tipo-fotografia"]');

        if (!tipoCell) {
            console.warn(' Fila sin columna de tipo fotografía');
            return;
        }

        const tipoText = tipoCell.textContent.trim().toUpperCase();
        let shouldShow = true;

        // SI el filtro está activo, verificar si coincide
        if (tipoFotografiaFilter.active) {
            shouldShow = tipoFotografiaFilter.selectedTypes.some(selectedType =>
                tipoText.includes(selectedType)
            );
        }

        //  MOSTRAR u ocultar fila
        if (shouldShow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
            hiddenCount++;
        }
    });

    //  ACTUALIZAR cards móviles si existen
    if (window.mobileCardsStable) {
        setTimeout(() => {
            window.mobileCardsStable.generateCardsOnce();
        }, 100);
    }

    console.log(` Filtro aplicado: ${visibleCount} visibles, ${hiddenCount} ocultas`);

    //  MOSTRAR notificación
    if (tipoFotografiaFilter.active) {
        const tipos = tipoFotografiaFilter.selectedTypes.join(', ');
        showNotification(`Filtro aplicado: ${tipos} (${visibleCount} registros)`, 'success');
    }
}

// ================================================================
// ACTUALIZAR INTERFAZ DEL FILTRO
// ================================================================

function updateTipoFotografiaUI() {
    const label = document.getElementById('tipoFotografiaLabel');
    const button = document.getElementById('tipoFotografiaDropdown');

    if (!label || !button) return;

    //  ACTUALIZAR texto del botón
    if (tipoFotografiaFilter.active) {
        const count = tipoFotografiaFilter.selectedTypes.length;
        label.textContent = `Filtrado (${count})`;
        button.classList.add('btn-primary');
        button.classList.remove('btn-buscar');
    } else {
        label.textContent = 'Buscar';
        button.classList.remove('btn-primary');
        button.classList.add('btn-buscar');
    }

    //  ACTUALIZAR contadores
    updateTipoFotografiaCounts();
}

// ================================================================
// ACTUALIZAR CONTADORES
// ================================================================

function updateTipoFotografiaCounts() {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) return;

    const counts = {
        'MUESTRA': 0,
        'PRENDA FINAL': 0,
        'VALIDACION AC': 0
    };

    //  CONTAR tipos en filas visibles
    const rows = tableBody.querySelectorAll('tr');
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const tipoCell = row.querySelector('td[data-column="tipo-fotografia"]');
            if (tipoCell) {
                const tipoText = tipoCell.textContent.trim().toUpperCase();

                if (tipoText.includes('MUESTRA')) {
                    counts['MUESTRA']++;
                } else if (tipoText.includes('PRENDA FINAL')) {
                    counts['PRENDA FINAL']++;
                } else if (tipoText.includes('VALIDACION AC')) {
                    counts['VALIDACION AC']++;
                }
            }
        }
    });

    //  ACTUALIZAR elementos de contador
    const countMuestra = document.getElementById('countMuestra');
    const countPrendaFinal = document.getElementById('countPrendaFinal');
    const countValidacionAC = document.getElementById('countValidacionAC');

    if (countMuestra) countMuestra.textContent = counts['MUESTRA'];
    if (countPrendaFinal) countPrendaFinal.textContent = counts['PRENDA FINAL'];
    if (countValidacionAC) countValidacionAC.textContent = counts['VALIDACION AC'];

    //  GUARDAR contadores globales
    tipoFotografiaFilter.totalCounts = counts;
}

// ================================================================
// FUNCIONES DE CONTROL
// ================================================================

// SELECCIONAR TODOS
function selectAllTipoFotografia() {
    const muestraCheck = document.getElementById('filtroMuestra');
    const prendaFinalCheck = document.getElementById('filtroPrendaFinal');
    const validacionACCheck = document.getElementById('filtroValidacionAC');

    if (muestraCheck) muestraCheck.checked = true;
    if (prendaFinalCheck) prendaFinalCheck.checked = true;
    if (validacionACCheck) validacionACCheck.checked = true;

    filterByTipoFotografia();

    console.log(' Todos los tipos seleccionados');
}

//  LIMPIAR FILTRO
function clearTipoFotografiaFilter() {
    const muestraCheck = document.getElementById('filtroMuestra');
    const prendaFinalCheck = document.getElementById('filtroPrendaFinal');
    const validacionACCheck = document.getElementById('filtroValidacionAC');

    if (muestraCheck) muestraCheck.checked = false;
    if (prendaFinalCheck) prendaFinalCheck.checked = false;
    if (validacionACCheck) validacionACCheck.checked = false;

    //  RESETEAR estado
    tipoFotografiaFilter.active = false;
    tipoFotografiaFilter.selectedTypes = [];

    //  MOSTRAR todas las filas
    const tableBody = document.getElementById('imagesTableBody');
    if (tableBody) {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            row.style.display = '';
        });
    }

    //  ACTUALIZAR interfaz
    updateTipoFotografiaUI();

    //  ACTUALIZAR cards móviles
    if (window.mobileCardsStable) {
        setTimeout(() => {
            window.mobileCardsStable.generateCardsOnce();
        }, 100);
    }

    showNotification('Filtro de tipo fotografía eliminado', 'info');
    console.log(' Filtro limpiado');
}

// ================================================================
// INTEGRACIÓN CON OTROS FILTROS
// ================================================================

//  FUNCIÓN para integrar con filtros existentes
function integrateTipoFotografiaWithOtherFilters() {
    // Esta función se puede llamar desde otros filtros
    // para asegurar que se respeten todos los filtros activos

    if (tipoFotografiaFilter.active) {
        console.log(' Reintegrando filtro de tipo fotografía con otros filtros');
        applyTipoFotografiaFilter();
    }
}

// ================================================================
// INICIALIZACIÓN
// ================================================================

function initializeTipoFotografiaFilter() {
    console.log(' Inicializando filtro de tipo fotografía...');

    //  ACTUALIZAR contadores iniciales
    updateTipoFotografiaCounts();

    //  PREVENIR cierre del dropdown al hacer click en checkboxes
    const dropdownMenu = document.getElementById('tipoFotografiaMenu');
    if (dropdownMenu) {
        dropdownMenu.addEventListener('click', function (e) {
            //  NO cerrar si se hace click en checkbox o label
            if (e.target.type === 'checkbox' || e.target.closest('label')) {
                e.stopPropagation();
            }
        });
    }

    //  ACTUALIZAR contadores cuando cambien las filas
    const tableBody = document.getElementById('imagesTableBody');
    if (tableBody) {
        const observer = new MutationObserver(() => {
            setTimeout(() => {
                updateTipoFotografiaCounts();
                //  REAPLICAR filtro si está activo
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

    console.log(' Filtro de tipo fotografía inicializado');
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
    console.log(` Notificación: [${type.toUpperCase()}] ${message}`);

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
}

function openImageLightbox(imageUrl, alt, description, type) {
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
        document.body.style.overflow = 'hidden';
    }
}

function closeLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) {
        lightbox.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.onkeypress = function (e) {
            if (e.key === 'Enter') {
                searchRecords();
            }
        };
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

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        const tableRows = document.querySelectorAll('#imagesTableBody tr');
        tableRows.forEach(row => {
            row.style.display = '';
        });
        showNotification('Búsqueda limpiada', 'info');
    }
}

/*!
 * ===========  Sistema de Filtrado de Fechas Automático ==============
 */

// Variables globales para el filtro de fechas
let dateFilterActive = false;
let currentDateRange = {
    start: null,
    end: null
};

// Inicializar el sistema de filtrado automático
function initializeAutoDateFilter() {
    console.log(' Inicializando filtro de fechas automático...');

    // SOLO inicializar variables vacías
    currentDateRange = {
        start: null,
        end: null
    };

    const dateRangeDisplay = document.getElementById('dateRangeDisplay');
    const dateRangeInputs = document.getElementById('dateRangeInputs');
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');

    if (!dateRangeDisplay || !dateRangeInputs || !fechaInicio || !fechaFin) {
        console.error('❌ Elementos del filtro de fechas no encontrados');
        return;
    }

    // Configurar fechas por defecto (últimos 30 días)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

    fechaInicio.value = formatDateForInput(thirtyDaysAgo);
    fechaFin.value = formatDateForInput(today);

    // Establecer rango inicial
    currentDateRange.start = fechaInicio.value;
    currentDateRange.end = fechaFin.value;
    updateDateRangeDisplay();

    // Event listeners
    dateRangeDisplay.addEventListener('click', toggleDateRangeInputs);
    fechaInicio.addEventListener('change', handleDateChange);
    fechaFin.addEventListener('change', handleDateChange);

    // Cerrar al hacer click fuera
    document.addEventListener('click', function (e) {
        if (!dateRangeDisplay.contains(e.target) && !dateRangeInputs.contains(e.target)) {
            closeDateRangeInputs();
        }
    });

    console.log(' Filtro de fechas automático inicializado');
}

function toggleDateRangeInputs() {
    const dateRangeDisplay = document.getElementById('dateRangeDisplay');
    const dateRangeInputs = document.getElementById('dateRangeInputs');

    if (dateRangeInputs.style.display === 'none' || !dateRangeInputs.style.display) {
        dateRangeInputs.style.display = 'block';
        dateRangeInputs.classList.add('show');
        dateRangeDisplay.classList.add('active');
        console.log(' Selector de fechas abierto');
    } else {
        closeDateRangeInputs();
    }
}

function closeDateRangeInputs() {
    const dateRangeDisplay = document.getElementById('dateRangeDisplay');
    const dateRangeInputs = document.getElementById('dateRangeInputs');

    dateRangeInputs.style.display = 'none';
    dateRangeInputs.classList.remove('show');
    dateRangeDisplay.classList.remove('active');
}

function handleDateChange() {
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');

    console.log(' Cambio en fechas detectado:', {
        inicio: fechaInicio.value,
        fin: fechaFin.value
    });

    // Validar que ambas fechas estén seleccionadas
    if (fechaInicio.value && fechaFin.value) {
        const startDate = new Date(fechaInicio.value);
        const endDate = new Date(fechaFin.value);

        // Validar que la fecha de inicio no sea mayor que la de fin
        if (startDate > endDate) {
            showNotification('La fecha de inicio no puede ser mayor que la fecha de fin', 'warning');
            return;
        }

        // Actualizar rango actual
        currentDateRange.start = fechaInicio.value;
        currentDateRange.end = fechaFin.value;

        // Actualizar display
        updateDateRangeDisplay();

        // Aplicar filtro automáticamente
        applyDateFilterAuto();

        // Cerrar selector después de un momento
        setTimeout(closeDateRangeInputs, 1000);
    }
}

function updateDateRangeDisplay() {
    const dateRangeText = document.getElementById('dateRangeText');
    const dateRangeDisplay = document.getElementById('dateRangeDisplay');

    if (currentDateRange.start && currentDateRange.end) {
        const startFormatted = formatDateForDisplay(currentDateRange.start);
        const endFormatted = formatDateForDisplay(currentDateRange.end);

        dateRangeText.textContent = `${startFormatted} - ${endFormatted}`;
        dateRangeDisplay.classList.add('has-dates');
        dateFilterActive = true;

        console.log(` Display actualizado: ${startFormatted} - ${endFormatted}`);
    } else {
        dateRangeText.textContent = 'Seleccionar rango';
        dateRangeDisplay.classList.remove('has-dates');
        dateFilterActive = false;
    }
}

function applyDateFilterAuto() {
    if (!currentDateRange.start || !currentDateRange.end) {
        console.log(' Rango de fechas incompleto, no aplicando filtro');
        return;
    }

    console.log(' Aplicando filtro automático de fechas:', currentDateRange);

    const startDate = new Date(currentDateRange.start);
    const endDate = new Date(currentDateRange.end);

    // Ajustar hora para incluir todo el día
    startDate.setHours(0, 0, 0, 0);
    endDate.setHours(23, 59, 59, 999);

    const tableRows = document.querySelectorAll('#imagesTableBody tr');
    let visibleCount = 0;
    let filteredCount = 0;

    tableRows.forEach(row => {
        // Aquí debería adaptar según cómo tiene almacenada la fecha en las filas
        // Por ejemplo, si tiene un data-attribute con la fecha:
        const rowDateStr = row.dataset.uploadDate || row.dataset.createdDate;

        if (rowDateStr) {
            const rowDate = new Date(rowDateStr);

            if (rowDate >= startDate && rowDate <= endDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
                filteredCount++;
            }
        } else {
            // Si no hay fecha, mostrar la fila (para compatibilidad)
            row.style.display = '';
            visibleCount++;
        }
    });

    // Notificación del resultado
    const totalRows = tableRows.length;
    console.log(` Filtro aplicado: ${visibleCount} visibles de ${totalRows} total`);
}

// Funciones de utilidad
function formatDateForInput(date) {
    return date.toISOString().split('T')[0];
}

function formatDateForDisplay(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Función para limpiar filtro
function clearDateFilter() {
    currentDateRange.start = null;
    currentDateRange.end = null;

    document.getElementById('fechaInicio').value = '';
    document.getElementById('fechaFin').value = '';

    updateDateRangeDisplay();

    // Mostrar todas las filas
    document.querySelectorAll('#imagesTableBody tr').forEach(row => {
        row.style.display = '';
    });

    showNotification('Filtro de fechas eliminado', 'info');
}

// Integrar con el sistema existente
document.addEventListener('DOMContentLoaded', function () {
    // Reemplazar la función existente de filtrado
    setTimeout(() => {
        initializeAutoDateFilter();
    }, 1000);
});

// Funciones globales
window.clearDateFilter = clearDateFilter;
window.applyDateFilterAuto = applyDateFilterAuto;

console.log(' Sistema de filtrado automático de fechas cargado');

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

function editImage(button) {
    const row = button.closest('tr');
    if (row) {
        const ordenSit = row.querySelector('[data-column="orden-sit"]')?.textContent || 'Sin orden';
        showNotification(`Editando imagen: ${ordenSit}`, 'info');
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
// FUNCIONES GLOBALES
// ================================================================================================

window.openImageLightbox = openImageLightbox;
window.closeLightbox = closeLightbox;
window.searchRecords = searchRecords;
window.clearSearch = clearSearch;
window.applyDateFilter = applyDateFilter;
window.exportAll = exportAll;
window.exportSelected = exportSelected;
window.showFilters = showFilters;
window.deleteImage = deleteImage;
window.editImage = editImage;
window.openCommentsModal = openCommentsModal;
window.deleteComment = deleteComment;
window.debugSystem = debugSystem;
window.initializeDateRangeSelector = initializeDateRangeSelector;



//  INICIALIZAR cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTipoFotografiaFilter);
} else {
    initializeTipoFotografiaFilter();
}

window.filterByTipoFotografia = filterByTipoFotografia;
window.selectAllTipoFotografia = selectAllTipoFotografia;
window.clearTipoFotografiaFilter = clearTipoFotografiaFilter;
window.integrateTipoFotografiaWithOtherFilters = integrateTipoFotografiaWithOtherFilters;


// ================================================================================================
// DESPLEGABLE DE FECHAS -> APLICAR SEGUN DOS RANGOS DE FECHAS  A ELEGIR
// ================================================================================================

//  FUNCIÓN CORREGIDA - SIN FECHAS POR DEFECTO
function initializeDateRangeUnified() {
    console.log(' Inicializando selector unificado de fechas...');

    setTimeout(() => {
        const dateRangeDisplayUnified = document.getElementById('dateRangeDisplayUnified');
        const dateCalendarsPanel = document.getElementById('dateCalendarsPanel');
        const fechaInicioUnified = document.getElementById('fechaInicioUnified');
        const fechaFinUnified = document.getElementById('fechaFinUnified');
        const calendarStatus = document.getElementById('calendarStatus');
        const dateRangeTextUnified = document.getElementById('dateRangeTextUnified');

        if (!dateRangeDisplayUnified || !dateCalendarsPanel) {
            console.warn(' Elementos unificados no encontrados');
            return;
        }

        // Variables de estado
        let panelOpen = false;
        let fechaInicioSelected = false;
        let fechaFinSelected = false;
        let selectedStartDate = null;
        let selectedEndDate = null;

        //  ESTADO INICIAL LIMPIO - SIN FECHAS
        if (dateRangeTextUnified) {
            dateRangeTextUnified.textContent = 'Seleccione fechas';
        }

        //  NO configurar fechas por defecto
        // Comentar/eliminar estas líneas:
        // fechaInicioUnified.value = formatDateForInput(thirtyDaysAgo);
        // fechaFinUnified.value = formatDateForInput(today);

        //  PASO 1: Click en contenedor principal
        dateRangeDisplayUnified.onclick = function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!panelOpen) {
                dateCalendarsPanel.style.display = 'block';
                dateRangeDisplayUnified.classList.add('active');
                panelOpen = true;

                setTimeout(() => {
                    fechaInicioUnified.focus();
                }, 100);

                updateCalendarStatus('Selecciona la fecha de inicio');
                console.log(' PASO 1: Panel abierto - selecciona fechas');
            }
        };

        //  PASO 2: Selección de fecha inicio
        fechaInicioUnified.addEventListener('change', function () {
            selectedStartDate = this.value;
            fechaInicioSelected = true;

            this.classList.add('selected');
            updateCalendarStatus('Ahora selecciona la fecha final');

            setTimeout(() => {
                fechaFinUnified.focus();
            }, 200);

            console.log(' PASO 2: Fecha inicio seleccionada -', selectedStartDate);
        });

        //  PASO 3: Selección de fecha final
        fechaFinUnified.addEventListener('change', function () {
            selectedEndDate = this.value;
            fechaFinSelected = true;

            this.classList.add('selected');

            if (selectedStartDate && selectedEndDate) {
                const startDate = new Date(selectedStartDate);
                const endDate = new Date(selectedEndDate);

                if (startDate > endDate) {
                    showNotification('La fecha de inicio no puede ser mayor que la fecha final', 'warning');
                    return;
                }

                updateCalendarStatus('✓ Aplicando filtro...');

                //  ACTUALIZAR display con fechas seleccionadas
                const startFormatted = selectedStartDate.split('-').reverse().join('/');
                const endFormatted = selectedEndDate.split('-').reverse().join('/');
                dateRangeTextUnified.textContent = `${startFormatted} - ${endFormatted}`;

                // Actualizar variables globales
                currentDateRange.start = selectedStartDate;
                currentDateRange.end = selectedEndDate;

                setTimeout(() => {
                    dateCalendarsPanel.style.display = 'none';
                    dateRangeDisplayUnified.classList.remove('active');
                    panelOpen = false;

                    applyUnifiedDateFilter(selectedStartDate, selectedEndDate);
                    showNotification(`Filtro aplicado: ${startFormatted} - ${endFormatted}`, 'success');

                }, 1000);

                console.log(' PASO 3: Filtro aplicado');
            }
        });

        //  FUNCIÓN para actualizar estado visual
        function updateCalendarStatus(message) {
            if (calendarStatus) {
                calendarStatus.innerHTML = `<small>${message}</small>`;

                if (message.includes('inicio')) {
                    calendarStatus.className = 'calendar-status step-1';
                } else if (message.includes('final')) {
                    calendarStatus.className = 'calendar-status step-2';
                } else if (message.includes('Aplicando')) {
                    calendarStatus.className = 'calendar-status applying';
                }
            }
        }

        //  CERRAR al hacer click fuera
        document.addEventListener('click', function (e) {
            if (!dateRangeDisplayUnified.contains(e.target) && !dateCalendarsPanel.contains(e.target)) {
                if (panelOpen) {
                    dateCalendarsPanel.style.display = 'none';
                    dateRangeDisplayUnified.classList.remove('active');
                    panelOpen = false;
                }
            }
        });

        //  CERRAR con ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && panelOpen) {
                dateCalendarsPanel.style.display = 'none';
                dateRangeDisplayUnified.classList.remove('active');
                panelOpen = false;
            }
        });

        console.log(' Selector unificado inicializado - estado inicial limpio');

    }, 1000);
}

// Hacer función global
window.initializeDateRangeUnified = initializeDateRangeUnified;


//  FUNCIÓN para resetear selección de fechas
function resetDateSelection() {
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');
    const dateRangeText = document.getElementById('dateRangeText');
    const dateRangeDisplay = document.getElementById('dateRangeDisplay');

    if (fechaInicio) fechaInicio.value = '';
    if (fechaFin) fechaFin.value = '';
    if (dateRangeText) dateRangeText.textContent = 'Seleccionar rango';
    if (dateRangeDisplay) dateRangeDisplay.classList.remove('has-dates');

    // Mostrar todas las filas
    document.querySelectorAll('#imagesTableBody tr').forEach(row => {
        row.style.display = '';
    });

    showNotification('Filtro de fechas eliminado', 'info');
}

// Hacer función global
window.resetDateSelection = resetDateSelection;
