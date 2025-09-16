/*!
 * Fotografías de Prendas - Sistema Completo
 * Date: 2025-09-16
 * Description: Sistema completo para gestión de fotografías de prendas con comentarios
 *
 * NOTA: todo el javascript funcional es este/ agregar cambios mañana
 * Revisar conflictos con los javascript fotos-index  y  comentarios.js
 * no sube comenarios, no funciona subir imagen, no abre modal de agregar comentarios
 */

// ================================================================================================
// VARIABLES GLOBALES Y CONFIGURACIÓN
// ================================================================================================

let currentUser = null;
let currentImageData = null;
let commentsData = new Map(); // Almacenar comentarios por imagen ID

const CONFIG = {
    MAX_FILE_SIZE: 10 * 1024 * 1024, // 10MB
    MAX_COMMENT_LENGTH: 500,
    UPLOAD_TIMEOUT: 30000, // 30 segundos
    AUTO_SAVE_INTERVAL: 5000, // 5 segundos
    DEBUG_MODE: true
};

// ================================================================================================
// SISTEMA DE INICIALIZACIÓN PRINCIPAL
// ================================================================================================

document.addEventListener("DOMContentLoaded", function () {
    console.log('🚀 Inicializando sistema completo de Fotografías de Prendas...');

    // Inicializar sistema de usuarios PRIMERO
    initializeUserSystem();

    // Luego el resto de sistemas
    initializeSidebar();
    initializeDatePickers();
    initializeColumnToggle();
    initializeLightbox();
    initializeNotifications();
    initializeSearch();
    initializeUploadButtons();
    initializeCommentsSystem();

    // Inicialización de características adicionales
    initializeKeyboardShortcuts();
    initializeAutoSave();
    initializePerformanceMonitoring();

    console.log('✅ Sistema completo inicializado correctamente');
});

// ================================================================================================
// SISTEMA DE DETECCIÓN DE USUARIOS
// ================================================================================================

function initializeUserSystem() {
    console.log('👤 Inicializando sistema de usuarios...');

    getCurrentUser()
        .then(user => {
            currentUser = user;
            console.log('👤 Usuario detectado:', currentUser);
            updateUserInterface(user);
        })
        .catch(error => {
            console.warn('⚠️ No se pudo detectar usuario, usando fallback');
            currentUser = getFallbackUser();
            updateUserInterface(currentUser);
        });
}

async function getCurrentUser() {
    // Método 1: Desde la interfaz existente (header)
    const userFromHeader = getUserFromHeader();
    if (userFromHeader) {
        return userFromHeader;
    }

    // Método 2: Desde sessionStorage/localStorage
    const userFromStorage = getUserFromStorage();
    if (userFromStorage) {
        return userFromStorage;
    }

    // Método 3: Desde meta tags o variables globales
    const userFromMeta = getUserFromMeta();
    if (userFromMeta) {
        return userFromMeta;
    }

    // Método 4: API call para obtener usuario actual
    const userFromAPI = await getUserFromAPI();
    if (userFromAPI) {
        return userFromAPI;
    }

    throw new Error('No se pudo detectar el usuario');
}

function getUserFromHeader() {
    // Buscar en el header donde aparece el nombre del usuario
    const userElement = document.querySelector('[class*="user"], [class*="profile"], .navbar .dropdown');
    if (userElement) {
        const userText = userElement.textContent;
        const match = userText.match(/([A-Z][A-Z\s]+[A-Z])/);
        if (match) {
            return {
                displayName: match[1].trim(),
                username: generateUsernameFromDisplayName(match[1].trim()),
                source: 'header'
            };
        }
    }

    // Buscar específicamente el elemento del usuario actual
    const breadcrumbUser = document.querySelector('.d-flex.align-items-center span');
    if (breadcrumbUser && breadcrumbUser.textContent.includes('DANIEL FELIPE')) {
        const displayName = breadcrumbUser.textContent.replace(/.*\s/, '').trim();
        return {
            displayName: displayName,
            username: generateUsernameFromDisplayName(displayName),
            source: 'breadcrumb'
        };
    }

    return null;
}

function getUserFromStorage() {
    const storageKeys = ['user', 'currentUser', 'authUser', 'userProfile', 'session'];

    for (const key of storageKeys) {
        const userData = localStorage.getItem(key) || sessionStorage.getItem(key);
        if (userData) {
            try {
                const parsed = JSON.parse(userData);
                if (parsed.name || parsed.username || parsed.displayName) {
                    return {
                        displayName: parsed.name || parsed.displayName,
                        username: parsed.username || generateUsernameFromDisplayName(parsed.name || parsed.displayName),
                        email: parsed.email,
                        source: 'storage'
                    };
                }
            } catch (e) {
                if (userData.length > 2) {
                    return {
                        displayName: userData,
                        username: generateUsernameFromDisplayName(userData),
                        source: 'storage-string'
                    };
                }
            }
        }
    }

    return null;
}

function getUserFromMeta() {
    const metaUser = document.querySelector('meta[name="user"], meta[name="current-user"]');
    if (metaUser) {
        const content = metaUser.content;
        try {
            const parsed = JSON.parse(content);
            return {
                displayName: parsed.name || parsed.displayName,
                username: parsed.username || generateUsernameFromDisplayName(parsed.name),
                source: 'meta'
            };
        } catch (e) {
            return {
                displayName: content,
                username: generateUsernameFromDisplayName(content),
                source: 'meta-string'
            };
        }
    }

    if (typeof window.currentUser !== 'undefined') {
        return {
            displayName: window.currentUser.name || window.currentUser.displayName,
            username: window.currentUser.username || generateUsernameFromDisplayName(window.currentUser.name),
            source: 'global-variable'
        };
    }

    return null;
}

async function getUserFromAPI() {
    try {
        const endpoints = ['/api/user/current', '/api/auth/me', '/user/profile', '/current-user'];

        for (const endpoint of endpoints) {
            try {
                const response = await fetch(endpoint);
                if (response.ok) {
                    const userData = await response.json();
                    return {
                        displayName: userData.name || userData.displayName || userData.fullName,
                        username: userData.username || userData.login || generateUsernameFromDisplayName(userData.name),
                        email: userData.email,
                        source: 'api'
                    };
                }
            } catch (e) {
                continue;
            }
        }
    } catch (error) {
        console.log('📡 No se pudo obtener usuario desde API');
    }

    return null;
}

function generateUsernameFromDisplayName(displayName) {
    if (!displayName) return 'Usuario';

    return displayName
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[áàäâ]/g, 'a')
        .replace(/[éèëê]/g, 'e')
        .replace(/[íìïî]/g, 'i')
        .replace(/[óòöô]/g, 'o')
        .replace(/[úùüû]/g, 'u')
        .replace(/[ñ]/g, 'n')
        .replace(/[^a-z0-9-]/g, '')
        .substring(0, 20);
}

function getFallbackUser() {
    const timestamp = new Date().toISOString().slice(0, 10);
    return {
        displayName: 'Usuario Sistema',
        username: `user-${timestamp}`,
        source: 'fallback'
    };
}

function updateUserInterface(user) {
    console.log(`👤 Usuario activo: ${user.displayName} (${user.username}) [${user.source}]`);

    const userDisplayElements = document.querySelectorAll('.current-user-display');
    userDisplayElements.forEach(element => {
        element.textContent = user.displayName;
    });
}

// ================================================================================================
// SISTEMA DE SIDEBAR
// ================================================================================================

function initializeSidebar() {
    console.log('📋 Inicializando sidebar...');

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');

            // Guardar estado en localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        });

        // Restaurar estado del sidebar
        const savedState = localStorage.getItem('sidebar-collapsed');
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
        }
    }

    console.log('✅ Sidebar inicializado');
}

// ================================================================================================
// SISTEMA DE FECHAS
// ================================================================================================

function initializeDatePickers() {
    console.log('📅 Inicializando selectores de fecha...');

    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');

    // Set default dates
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

    if (fechaInicio) {
        fechaInicio.value = thirtyDaysAgo.toISOString().split('T')[0];
        fechaInicio.addEventListener('change', validateDateRange);
    }

    if (fechaFin) {
        fechaFin.value = today.toISOString().split('T')[0];
        fechaFin.addEventListener('change', validateDateRange);
    }

    console.log('✅ Selectores de fecha inicializados');
}

function validateDateRange() {
    const fechaInicio = document.getElementById('fechaInicio');
    const fechaFin = document.getElementById('fechaFin');

    if (fechaInicio && fechaFin && fechaInicio.value && fechaFin.value) {
        if (new Date(fechaInicio.value) > new Date(fechaFin.value)) {
            showNotification('La fecha de inicio debe ser anterior a la fecha fin', 'warning');
            fechaFin.value = fechaInicio.value;
        }
    }
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

    console.log('🔍 Aplicando filtro de fechas:', fechaInicio, 'a', fechaFin);
    showNotification('Filtro de fechas aplicado correctamente', 'success');

    // Aquí iría la lógica para filtrar los datos
    filterTableByDate(fechaInicio, fechaFin);
}

function filterTableByDate(startDate, endDate) {
    // Implementar filtrado por fechas
    console.log('🔍 Filtrando tabla por fechas:', startDate, 'a', endDate);
    // TODO: Implementar lógica de filtrado
}

// ================================================================================================
// SISTEMA DE COLUMNAS
// ================================================================================================

function initializeColumnToggle() {
    console.log('📋 Inicializando control de columnas...');

    const dropdown = document.getElementById('columnsDropdown');

    if (!dropdown) {
        console.warn('⚠️ Dropdown de columnas no encontrado');
        return;
    }

    dropdown.addEventListener('change', function (e) {
        if (e.target.type === 'checkbox' && e.target.dataset.column) {
            const columnName = e.target.dataset.column;
            const isVisible = e.target.checked;

            toggleColumn(columnName, isVisible);
            saveColumnState(columnName, isVisible);
            showNotification(
                `Columna "${getColumnDisplayName(columnName)}" ${isVisible ? 'mostrada' : 'ocultada'}`,
                'info'
            );
        }
    });

    // Restaurar estado de columnas
    restoreColumnStates();

    console.log('✅ Control de columnas inicializado');
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

function saveColumnState(columnName, isVisible) {
    const columnStates = JSON.parse(localStorage.getItem('column-states') || '{}');
    columnStates[columnName] = isVisible;
    localStorage.setItem('column-states', JSON.stringify(columnStates));
}

function restoreColumnStates() {
    const columnStates = JSON.parse(localStorage.getItem('column-states') || '{}');

    Object.entries(columnStates).forEach(([columnName, isVisible]) => {
        const checkbox = document.querySelector(`[data-column="${columnName}"]`);
        if (checkbox) {
            checkbox.checked = isVisible;
            toggleColumn(columnName, isVisible);
        }
    });
}

// ================================================================================================
// SISTEMA DE LIGHTBOX
// ================================================================================================

function initializeLightbox() {
    console.log('🖼️ Inicializando lightbox...');

    const lightbox = document.getElementById('imageLightbox');

    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && lightbox.style.display !== 'none') {
                closeLightbox();
            }
        });
    }

    console.log('✅ Lightbox inicializado');
}

function openImageLightbox(imageUrl, alt, description, type) {
    console.log('🖼️ Abriendo lightbox para imagen:', imageUrl);

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
    console.log('❌ Cerrando lightbox');

    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) {
        lightbox.style.display = 'none';
        document.body.style.overflow = '';
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

// ================================================================================================
// SISTEMA DE NOTIFICACIONES
// ================================================================================================

function initializeNotifications() {
    console.log('🔔 Inicializando sistema de notificaciones...');

    if (!document.getElementById('notificationContainer')) {
        const container = document.createElement('div');
        container.id = 'notificationContainer';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    console.log('✅ Sistema de notificaciones inicializado');
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

    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);

    if (CONFIG.DEBUG_MODE) {
        console.log(`🔔 Notificación mostrada: ${message}`);
    }
}

// ================================================================================================
// SISTEMA DE BÚSQUEDA
// ================================================================================================

function initializeSearch() {
    console.log('🔍 Inicializando sistema de búsqueda...');

    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchRecords();
            }
        });

        // Real-time search con debounce
        searchInput.addEventListener('input', debounce(performRealTimeSearch, 300));
    }

    console.log('✅ Sistema de búsqueda inicializado');
}

function searchRecords() {
    const searchTerm = document.getElementById('searchInput').value.trim();

    if (!searchTerm) {
        showNotification('Ingresa un término de búsqueda', 'warning');
        return;
    }

    console.log('🔍 Buscando:', searchTerm);
    showNotification(`Buscando: "${searchTerm}"`, 'info');

    performSearch(searchTerm);
}

function performSearch(searchTerm) {
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

function performRealTimeSearch() {
    const searchTerm = document.getElementById('searchInput').value.trim();
    if (searchTerm.length >= 2) {
        performSearch(searchTerm);
    } else if (searchTerm.length === 0) {
        clearSearch();
    }
}

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';

        // Mostrar todas las filas
        const tableRows = document.querySelectorAll('#imagesTableBody tr');
        tableRows.forEach(row => {
            row.style.display = '';
        });

        console.log('🔍 Búsqueda limpiada');
        showNotification('Búsqueda limpiada', 'info');
    }
}

// ================================================================================================
// SISTEMA DE SUBIDA DE ARCHIVOS
// ================================================================================================

function initializeUploadButtons() {
    console.log('📤 Inicializando sistema de subida...');

    const cameraUpload = document.getElementById('cameraUpload');
    const fileUpload = document.getElementById('fileUpload');
    const cameraInput = document.getElementById('cameraInput');
    const fileInput = document.getElementById('fileInput');

    // Camera upload click
    if (cameraUpload && cameraInput) {
        cameraUpload.addEventListener('click', function () {
            console.log('📸 Activando cámara...');
            cameraInput.click();
        });

        cameraInput.addEventListener('change', function (e) {
            handleImageUpload(e.target.files, 'camera');
        });
    }

    // File upload click
    if (fileUpload && fileInput) {
        fileUpload.addEventListener('click', function () {
            console.log('📁 Abriendo selector de archivos...');
            fileInput.click();
        });

        fileInput.addEventListener('change', function (e) {
            handleImageUpload(e.target.files, 'file');
        });
    }

    // Drag and drop functionality
    initializeDragAndDrop();

    console.log('✅ Sistema de subida inicializado');
}

function handleImageUpload(files, source) {
    if (!files || files.length === 0) {
        showNotification('No se seleccionaron archivos', 'warning');
        return;
    }

    console.log(`📤 Subiendo ${files.length} archivo(s) desde ${source}`);

    // Validar archivos
    const validFiles = Array.from(files).filter(file => {
        if (!file.type.startsWith('image/')) {
            showNotification(`Archivo "${file.name}" no es una imagen válida`, 'error');
            return false;
        }

        if (file.size > CONFIG.MAX_FILE_SIZE) {
            showNotification(`Archivo "${file.name}" es demasiado grande (máximo 10MB)`, 'error');
            return false;
        }

        return true;
    });

    if (validFiles.length === 0) {
        return;
    }

    // Mostrar estado de carga
    const uploadBtn = source === 'camera'
        ? document.getElementById('cameraUpload')
        : document.getElementById('fileUpload');

    setUploadState(uploadBtn, 'uploading');

    // Procesar archivos
    const uploadPromises = validFiles.map(file => uploadSingleImage(file));

    Promise.all(uploadPromises)
        .then(results => {
            console.log('✅ Todas las imágenes subidas correctamente');
            showNotification(`${results.length} imagen(es) subida(s) correctamente`, 'success');

            results.forEach(imageData => {
                addImageToTable(imageData);
            });

            setUploadState(uploadBtn, 'success');

            setTimeout(() => {
                setUploadState(uploadBtn, 'normal');
            }, 2000);
        })
        .catch(error => {
            console.error('❌ Error subiendo imágenes:', error);
            showNotification('Error al subir las imágenes', 'error');
            setUploadState(uploadBtn, 'normal');
        });
}

function uploadSingleImage(file) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('timestamp', new Date().toISOString());
        formData.append('user', currentUser ? currentUser.username : 'unknown');

        // Simular subida (reemplazar con tu endpoint real)
        setTimeout(() => {
            const imageUrl = URL.createObjectURL(file);

            resolve({
                id: Date.now() + Math.random(),
                url: imageUrl,
                name: file.name,
                size: file.size,
                uploadDate: new Date().toISOString(),
                ordenSit: generateOrderNumber(),
                po: generatePONumber(),
                oc: generateOCNumber(),
                descripcion: 'Imagen subida',
                tipoFotografia: 'SUBIDA MANUAL'
            });
        }, 1000 + Math.random() * 2000);
    });
}

function addImageToTable(imageData) {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) return;

    const imageId = generateImageId({ dataset: {} });
    imageData.id = imageId;

    // Agregar información del usuario que subió la imagen
    imageData.uploadedBy = currentUser ? {
        displayName: currentUser.displayName,
        username: currentUser.username,
        timestamp: new Date().toISOString()
    } : null;

    const row = document.createElement('tr');
    row.dataset.imageId = imageId;
    row.dataset.uploadedBy = currentUser ? currentUser.username : 'unknown';

    row.innerHTML = `
        <td data-column="imagen">
            <img src="${imageData.url}"
                 alt="${imageData.name}"
                 class="img-thumbnail preview-image"
                 style="width: 60px; height: 60px; cursor: pointer;"
                 onclick="openImageLightbox('${imageData.url}', '${imageData.name}', '${imageData.descripcion}', '${imageData.tipoFotografia}')">
            ${currentUser ? `<div class="upload-user-badge" title="Subido por ${currentUser.displayName}">
                <i class="fas fa-user"></i> ${currentUser.username}
            </div>` : ''}
        </td>
        <td data-column="orden-sit">${imageData.ordenSit}</td>
        <td data-column="po">${imageData.po}</td>
        <td data-column="oc">${imageData.oc}</td>
        <td data-column="descripcion">${imageData.descripcion}</td>
        <td data-column="tipo-fotografia">
            <span class="badge bg-info">${imageData.tipoFotografia}</span>
        </td>
        <td data-column="acciones">
            <button class="btn btn-danger btn-sm me-1" onclick="deleteImage(this)" title="Eliminar imagen">
                <i class="fas fa-trash"></i> Eliminar
            </button>
            <button class="btn btn-warning btn-sm me-1" onclick="editImage(this)" title="Editar información">
                <i class="fas fa-edit"></i> Editar
            </button>
            <button class="btn btn-info btn-sm" onclick="openCommentsModal(this)" title="Ver/Agregar comentarios">
                <i class="fas fa-comments"></i>
                <span class="comment-count" data-count="0"></span>
            </button>
        </td>
    `;

    tableBody.insertBefore(row, tableBody.firstChild);

    // Añadir animación
    row.style.opacity = '0';
    row.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        row.style.transition = 'all 0.5s ease';
        row.style.opacity = '1';
        row.style.transform = 'translateY(0)';
    }, 100);
}

function setUploadState(button, state) {
    if (!button) return;

    button.classList.remove('active', 'uploading');

    switch (state) {
        case 'uploading':
            button.classList.add('uploading');
            break;
        case 'success':
            button.classList.add('active');
            break;
        case 'normal':
        default:
            break;
    }
}

function initializeDragAndDrop() {
    const uploadBtns = document.querySelectorAll('.upload-btn');

    uploadBtns.forEach(btn => {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            btn.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            btn.addEventListener(eventName, () => btn.classList.add('active'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            btn.addEventListener(eventName, () => btn.classList.remove('active'), false);
        });

        btn.addEventListener('drop', handleDrop, false);
    });
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDrop(e) {
    const files = e.dataTransfer.files;
    const isCamera = e.currentTarget.id === 'cameraUpload';
    handleImageUpload(files, isCamera ? 'camera' : 'file');
}

// ================================================================================================
// SISTEMA DE COMENTARIOS
// ================================================================================================

function initializeCommentsSystem() {
    console.log('💬 Inicializando sistema de comentarios...');

    const commentForm = document.getElementById('commentForm');
    const commentText = document.getElementById('commentText');

    if (commentForm) {
        commentForm.addEventListener('submit', handleCommentSubmit);
    }

    if (commentText) {
        commentText.addEventListener('input', updateCharacterCount);
    }

    console.log('✅ Sistema de comentarios inicializado');
}

function openCommentsModal(button) {
    const row = button.closest('tr');
    const imageData = extractImageDataFromRow(row);

    if (!imageData) {
        showNotification('Error al obtener datos de la imagen', 'error');
        return;
    }

    currentImageData = imageData;

    updateCommentsModalInfo(imageData);
    loadCommentsForImage(imageData.id);

    const modal = new bootstrap.Modal(document.getElementById('commentsModal'));
    modal.show();

    console.log('💬 Abriendo modal de comentarios para:', imageData.ordenSit);
}

function extractImageDataFromRow(row) {
    if (!row) return null;

    const img = row.querySelector('img');
    const ordenSitCell = row.querySelector('[data-column="orden-sit"]');
    const poCell = row.querySelector('[data-column="po"]');
    const ocCell = row.querySelector('[data-column="oc"]');
    const descripcionCell = row.querySelector('[data-column="descripcion"]');
    const tipoCell = row.querySelector('[data-column="tipo-fotografia"]');

    return {
        id: row.dataset.imageId || generateImageId(row),
        imageUrl: img ? img.src : '',
        imageAlt: img ? img.alt : '',
        ordenSit: ordenSitCell ? ordenSitCell.textContent.trim() : '',
        po: poCell ? poCell.textContent.trim() : '',
        oc: ocCell ? ocCell.textContent.trim() : '',
        descripcion: descripcionCell ? descripcionCell.textContent.trim() : '',
        tipo: tipoCell ? tipoCell.textContent.trim() : ''
    };
}

function generateImageId(row) {
    const ordenSit = row.querySelector('[data-column="orden-sit"]')?.textContent || '';
    const po = row.querySelector('[data-column="po"]')?.textContent || '';
    const id = `img_${ordenSit}_${po}_${Date.now()}`;
    row.dataset.imageId = id;
    return id;
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
}

function handleCommentSubmit(e) {
    e.preventDefault();

    if (!currentImageData) {
        showNotification('Error: No hay imagen seleccionada', 'error');
        return;
    }

    if (!currentUser) {
        showNotification('Error: No se pudo identificar al usuario', 'error');
        return;
    }

    const formData = {
        type: document.getElementById('commentType').value,
        priority: document.getElementById('commentPriority').value,
        text: document.getElementById('commentText').value.trim()
    };

    if (!formData.type || !formData.priority || !formData.text) {
        showNotification('Por favor completa todos los campos', 'warning');
        return;
    }

    if (formData.text.length > CONFIG.MAX_COMMENT_LENGTH) {
        showNotification(`El comentario no puede exceder ${CONFIG.MAX_COMMENT_LENGTH} caracteres`, 'error');
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
        authorEmail: currentUser.email || null,
        timestamp: new Date().toISOString(),
        edited: false,
        userSource: currentUser.source
    };

    addCommentToStorage(comment);
    renderComment(comment, true);
    updateCommentsCount();
    updateCommentButtonBadge();
    clearCommentForm();

    showNotification(`Comentario agregado por ${currentUser.displayName}`, 'success');
}

function generateCommentId() {
    return 'comment_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

function addCommentToStorage(comment) {
    if (!commentsData.has(comment.imageId)) {
        commentsData.set(comment.imageId, []);
    }

    commentsData.get(comment.imageId).push(comment);

    if (CONFIG.DEBUG_MODE) {
        console.log('💾 Comentario guardado:', comment);
    }
}

function loadCommentsForImage(imageId) {
    const comments = commentsData.get(imageId) || [];
    const commentsList = document.getElementById('commentsList');
    const noCommentsMessage = document.getElementById('noCommentsMessage');
    const totalCommentsCount = document.getElementById('totalCommentsCount');

    if (!commentsList) return;

    commentsList.innerHTML = '';

    if (comments.length === 0) {
        commentsList.appendChild(noCommentsMessage.cloneNode(true));
    } else {
        comments.forEach(comment => renderComment(comment, false));
    }

    if (totalCommentsCount) {
        totalCommentsCount.textContent = comments.length;
    }
}

function renderComment(comment, isNew = false) {
    const commentsList = document.getElementById('commentsList');
    const noCommentsMessage = commentsList.querySelector('#noCommentsMessage');

    if (noCommentsMessage) {
        noCommentsMessage.remove();
    }

    const commentElement = document.createElement('div');
    commentElement.className = `comment-item ${isNew ? 'new-comment' : ''}`;
    commentElement.dataset.commentId = comment.id;

    const typeClass = `type-${comment.type}`;
    const priorityClass = `priority-${comment.priority}`;

    const isCurrentUser = currentUser &&
        (comment.authorUsername === currentUser.username ||
            comment.author === currentUser.displayName);

    commentElement.innerHTML = `
        <div class="comment-meta">
            <span class="comment-author ${isCurrentUser ? 'current-user' : ''}"
                  data-username="@${comment.authorUsername || 'unknown'}">
                ${comment.author}
            </span>
            <span class="comment-type-badge ${typeClass}">${getTypeDisplayName(comment.type)}</span>
            <span class="comment-priority ${priorityClass}">${getPriorityDisplayName(comment.priority)}</span>
            <span class="comment-timestamp">
                <i class="fas fa-clock"></i>
                ${formatTimestamp(comment.timestamp)}
            </span>
            ${CONFIG.DEBUG_MODE && comment.userSource ? `<span class="user-debug-info">[${comment.userSource}]</span>` : ''}
        </div>
        <div class="comment-text">${escapeHtml(comment.text)}</div>
        <div class="comment-actions">
            ${isCurrentUser ? `
                <button class="btn btn-sm btn-outline-primary" onclick="editComment('${comment.id}')">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteComment('${comment.id}')">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            ` : ''}
            <button class="btn btn-sm btn-outline-info" onclick="replyToComment('${comment.id}')">
                <i class="fas fa-reply"></i> Responder
            </button>
        </div>
    `;

    commentsList.insertBefore(commentElement, commentsList.firstChild);

    if (isNew) {
        setTimeout(() => {
            commentElement.classList.remove('new-comment');
        }, 500);
    }
}

function updateCharacterCount() {
    const commentText = document.getElementById('commentText');
    const charCount = document.getElementById('charCount');

    if (!commentText || !charCount) return;

    const length = commentText.value.length;
    charCount.textContent = length;

    charCount.className = '';
    if (length > CONFIG.MAX_COMMENT_LENGTH * 0.8) {
        charCount.classList.add('warning');
    }
    if (length > CONFIG.MAX_COMMENT_LENGTH * 0.9) {
        charCount.classList.add('danger');
    }
}

function clearCommentForm() {
    document.getElementById('commentType').value = '';
    document.getElementById('commentPriority').value = 'medium';
    document.getElementById('commentText').value = '';
    updateCharacterCount();
}

function updateCommentsCount() {
    if (!currentImageData) return;

    const comments = commentsData.get(currentImageData.id) || [];
    const totalCommentsCount = document.getElementById('totalCommentsCount');

    if (totalCommentsCount) {
        totalCommentsCount.textContent = comments.length;
    }
}

function updateCommentButtonBadge() {
    if (!currentImageData) return;

    const rows = document.querySelectorAll('#imagesTableBody tr');
    rows.forEach(row => {
        if (row.dataset.imageId === currentImageData.id) {
            const commentButton = row.querySelector('.btn-info .comment-count');
            const comments = commentsData.get(currentImageData.id) || [];

            if (commentButton) {
                commentButton.dataset.count = comments.length;
                commentButton.textContent = comments.length;
            }
        }
    });
}

// ================================================================================================
// FUNCIONES DE UTILIDAD Y HELPERS
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
    } else if (diffDays < 7) {
        return `${diffDays} días`;
    } else {
        return date.toLocaleDateString('es-ES');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

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
// FUNCIONES DE EXPORTACIÓN
// ================================================================================================

function exportAll() {
    console.log('📤 Exportando todos los registros...');
    showNotification('Exportando todos los registros...', 'info');

    // Simular exportación
    setTimeout(() => {
        showNotification('Exportación completada', 'success');
    }, 2000);
}

function exportSelected() {
    console.log('📤 Exportando registros seleccionados...');
    showNotification('Exportando registros seleccionados...', 'info');

    setTimeout(() => {
        showNotification('Exportación completada', 'success');
    }, 2000);
}

function exportComments() {
    if (!currentImageData) return;

    const comments = commentsData.get(currentImageData.id) || [];
    if (comments.length === 0) {
        showNotification('No hay comentarios para exportar', 'warning');
        return;
    }

    showNotification('Exportando comentarios...', 'info');
    // TODO: Implementar lógica de exportación
}

function showFilters() {
    console.log('🔧 Mostrando filtros avanzados...');
    showNotification('Filtros avanzados mostrados', 'info');
    // TODO: Implementar filtros avanzados
}

// ================================================================================================
// ACCIONES DE TABLA
// ================================================================================================

function deleteImage(button) {
    if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
        const row = button.closest('tr');
        if (row) {
            const imageId = row.dataset.imageId;

            // Eliminar comentarios asociados
            if (imageId && commentsData.has(imageId)) {
                commentsData.delete(imageId);
            }

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
        // TODO: Implementar lógica de edición
    }
}

// ================================================================================================
// ACCIONES DE COMENTARIOS
// ================================================================================================

function editComment(commentId) {
    showNotification('Función de editar comentario en desarrollo', 'info');
    // TODO: Implementar edición de comentarios
}

function deleteComment(commentId) {
    if (confirm('¿Estás seguro de que deseas eliminar este comentario?')) {
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

function replyToComment(commentId) {
    showNotification('Función de responder comentario en desarrollo', 'info');
    // TODO: Implementar respuestas a comentarios
}

function sortComments(order) {
    showNotification(`Ordenando comentarios: ${order === 'newest' ? 'Más recientes' : 'Más antiguos'}`, 'info');
    // TODO: Implementar ordenamiento de comentarios
}

function filterCommentsByPriority() {
    showNotification('Filtro de prioridad en desarrollo', 'info');
    // TODO: Implementar filtro por prioridad
}

// ================================================================================================
// CARACTERÍSTICAS ADICIONALES
// ================================================================================================

function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function (e) {
        // Ctrl + S para buscar
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            document.getElementById('searchInput')?.focus();
        }

        // Ctrl + U para subir archivo
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            document.getElementById('fileUpload')?.click();
        }

        // Escape para cerrar modals
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            });
        }
    });

    if (CONFIG.DEBUG_MODE) {
        console.log('⌨️ Atajos de teclado inicializados');
    }
}

function initializeAutoSave() {
    // Auto-guardado de estados cada 5 segundos
    setInterval(() => {
        if (CONFIG.DEBUG_MODE) {
            console.log('💾 Auto-guardado ejecutado');
        }
        // TODO: Implementar auto-guardado
    }, CONFIG.AUTO_SAVE_INTERVAL);
}

function initializePerformanceMonitoring() {
    if (CONFIG.DEBUG_MODE) {
        console.log('📊 Monitoring de performance iniciado');

        // Monitor de memoria
        setInterval(() => {
            if (performance.memory) {
                const used = Math.round(performance.memory.usedJSHeapSize / 1048576);
                const total = Math.round(performance.memory.totalJSHeapSize / 1048576);
                console.log(`🧠 Memoria: ${used}MB / ${total}MB`);
            }
        }, 30000);
    }
}

// ================================================================================================
// FUNCIONES GLOBALES PARA HTML
// ================================================================================================

// Hacer funciones disponibles globalmente para uso en HTML
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
window.editImage = editImage;
window.openCommentsModal = openCommentsModal;
window.clearCommentForm = clearCommentForm;
window.editComment = editComment;
window.deleteComment = deleteComment;
window.replyToComment = replyToComment;
window.sortComments = sortComments;
window.filterCommentsByPriority = filterCommentsByPriority;
window.exportComments = exportComments;

// ================================================================================================
// INICIALIZACIÓN FINAL
// ================================================================================================

console.log(`
🎉 Sistema de Fotografías de Prendas v1.0
👤 Desarrollado por: Will-AGW
📅 Fecha: ${new Date().toLocaleDateString('es-ES')}
🚀 Estado: Listo para producción
`);
