@extends('layout/plantilla')

@section('tituloPagina', 'Agregar Foto a Orden SIT')

@section('contenido')

<div class="container mt-4">
    <h3 class="mb-4">Agregar fotos de la prenda</h3>

    <!-- Buscar Orden SIT -->
    <div class="mb-3">
        <label for="ordenSitInput" class="form-label">Buscar orden SIT</label>
        <input type="text"
               id="ordenSitInput"
               class="form-control"
               placeholder="Ej: 12345678"
               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    </div>

    <!-- Resultado de búsqueda -->
    <div id="ordenSitCard" class="card p-3 mb-3" style="display:none;">
        <div class="row align-items-center">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <!-- Miniatura -->
                    <img id="prendaPreview"
                        src="https://via.placeholder.com/120x150?text=Prenda"
                        alt="Prenda"
                        class="img-thumbnail"
                        style="max-width:120px; cursor:pointer;"
                        onclick="openLightbox(this.src, 'Prenda de vestir', tipoSeleccionado)">
                </div>
                <div class="col-md-9">
                    <p class="mb-1"><strong>Orden SIT:</strong> <span id="ordenSitValue"></span></p>
                    <p class="mb-1"><strong>Tipo:</strong> <span id="tipoOrden"></span></p>
                    <p class="mb-1"><strong>Descripción:</strong> <span id="descripcion"></span></p>

                <!-- Subir imágenes -->
                    <div class="mb-3">
                        <label class="text-muted mb-3">Subir Imágenes</label>
                        <div class="upload-buttons d-flex gap-2">
                            <div class="upload-btn" id="cameraUpload" title="Tomar foto con cámara">
                                <i class="fas fa-camera"></i>
                                <span>Cámara</span>
                                <input type="file" accept="image/*" capture="camera" style="display: none;" id="cameraInput">
                            </div>
                            <div class="upload-btn" id="fileUpload" title="Seleccionar archivos">
                                <i class="fas fa-folder"></i>
                                <span>Archivo</span>
                                <input type="file" accept="image/*" multiple style="display: none;" id="fileInput">
                            </div>
                        </div>
                    </div>
                    <!-- Estado -->
                    <div class="mb-3">
                        <label class="text-muted">Tipo del último cargue</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-personalizado" onclick="setTipoFoto('Muestra')">Muestra</button>
                            <button class="btn btn-personalizado" onclick="setTipoFoto('Prenda Final')">Prenda Final</button>
                            <button class="btn btn-personalizado" onclick="setTipoFoto('Validación AC')">Validación AC</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Lightbox para visualizar imágenes -->
<div id="imageLightbox" class="lightbox-overlay" style="display: none;">
    <div class="lightbox-content">
        <div class="lightbox-header">
            <h5 id="lightboxTitle">Vista Previa de Imagen</h5>
            <button onclick="closeLightbox()" class="btn-close-lightbox">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="lightbox-body">
            <img id="lightboxImage" src="" alt="" class="lightbox-image">
            <div class="lightbox-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Descripción:</strong>
                        <p id="lightboxDescription">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong>
                        <p id="lightboxType">-</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="lightbox-footer">
            <button onclick="closeLightbox()" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>
                Cerrar
            </button>
            <button onclick="downloadImage()" class="btn btn-primary">
                <i class="fas fa-download me-1"></i>
                Descargar
            </button>
        </div>
    </div>
</div>

    <!-- Botones de acción -->
    <div class="text-end">
        <a class="btn btn-secondary">Cancelar</a>
        <button type="button" class="btn btn-primary" onclick="guardarFoto()">Guardar</button>
    </div>
</div>

<!-- Modal para ingresar datos(Descripcion - Tipo fotografia) de la imagen -->
<div class="modal fade" id="imageDataModal" tabindex="-1" aria-labelledby="imageDataModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="imageDataModalLabel">Detalles de la imagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form id="imageDataForm">
          <div class="mb-3">
            <label for="descripcionInput" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcionInput" placeholder="Ej: CAMISA BLANCA" required>
          </div>
          <div class="mb-3">
            <label for="tipoFotografiaSelect" class="form-label">Tipo de Fotografía</label>
            <select class="form-select" id="tipoFotografiaSelect" required>
              <option value="" disabled selected>Seleccione un tipo</option>
              <option value="MUESTRA">MUESTRA</option>
              <option value="VALIDACION AC">VALIDACIÓN AC</option>
              <option value="PRENDA FINAL">PRENDA FINAL</option>
            </select>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="saveImageData">Guardar</button>
      </div>

    </div>
  </div>
</div>

<!-- Scripts utiles-->
<script src="{{ asset('js/fotos-index.js') }}"></script>


<!-- ==========Busqueda orden sit - Botones card agregar fotos prenda  ============0-->
<script>
    let estadoSeleccionado = null;

    const ordenSitInput = document.getElementById('ordenSitInput');
    const ordenSitCard = document.getElementById('ordenSitCard');
    const ordenSitValue = document.getElementById('ordenSitValue');
    const tipoOrden = document.getElementById('tipoOrden');
    const prendaPreview = document.getElementById('prendaPreview');
    const imageLightbox = document.getElementById('imageLightbox');

    // Enter para buscar
    ordenSitInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarOrdenSit();
        }
    });

    // Click fuera limpia búsqueda
    document.addEventListener('click', function(e) {
        if (!ordenSitInput.contains(e.target) &&
        !ordenSitCard.contains(e.target) &&
        !imageLightbox.contains(e.target)) {
        ordenSitInput.value = '';
        ordenSitCard.style.display = 'none';
        }
    });

    // Validar y buscar (Simulacion resultados)
    function buscarOrdenSit() {
        const value = ordenSitInput.value.trim();

        if (value === "") {
            alert("Ingrese un número de orden");
            return;
        }

        // Simular orden encontrada(ELiminar luego)
        ordenSitValue.textContent = value;
        prendaPreview.src = "https://via.placeholder.com/200";

        // limpia texto Estado la primera vez de cargue de pag
        if (!tipoOrden.textContent) {
            tipoOrden.textContent = ''; //limpiar estado
            tipoOrden.className = ''; //limpiar estilos
            tipoSeleccionado = null;
        }

        ordenSitCard.style.display = 'block';
        alert(`Orden SIT ${value} encontrada`);
    }

    // Cambiar estado
    // Cambiar estado
    function setTipoFoto(tipo) {
        tipoSeleccionado = tipo;
        tipoOrden.textContent = tipo;

        if (tipo === "Muestra") { tipoOrden.className = "badge badge-color-personalizado"; }
        if (tipo === "Prenda Final") { tipoOrden.className = "badge badge-color-personalizado"; }
        if (tipo === "Validación AC") { tipoOrden.className = "badge badge-color-personalizado"; }
    }

   // Guardar
   function guardarFoto() {
    if (!tipoSeleccionado) {
        alert("Debe seleccionar un tipo de fotografia antes de guardar");
        return;
    }
    alert(`Foto guardada con tipo: ${tipoSeleccionado}`);
    // Espacio para agregar luego logica de guardado

    window.location.href = "{{ route('fotos-index') }}";

   }

    // Lightbox
    function openLightbox(imageUrl, description, type) {
        document.getElementById('lightboxImage').src = imageUrl;
        document.getElementById('lightboxDescription').textContent = description;
        document.getElementById('lightboxType').textContent = type;
        document.getElementById('imageLightbox').style.display = 'flex';
    }
    function closeLightbox() {
        document.getElementById('imageLightbox').style.display = 'none';
    }
    function downloadImage() {
        const imgSrc = document.getElementById('lightboxImage').src;
        const link = document.createElement('a');
        link.href = imgSrc;
        link.download = "prenda.jpg";
        link.click();
    }
</script>

<!--/=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=/ -->
<script>
    // ===== SCRIPT FUNCIONALIDAD SUBIDA DE IMAGENES =====
    function initializeUploadButtons() {
        const cameraUpload = document.getElementById('cameraUpload');
        const fileUpload = document.getElementById('fileUpload');
        const cameraInput = document.getElementById('cameraInput');
        const fileInput = document.getElementById('fileInput');

        // Camera upload click
        if (cameraUpload && cameraInput) {
            cameraUpload.addEventListener('click', function() {
                console.log(' Activando cámara...');
                cameraInput.click();
            });

            cameraInput.addEventListener('change', function(e) {
                handleImageUpload(e.target.files, 'camera');
            });
        }

        // File upload click
        if (fileUpload && fileInput) {
            fileUpload.addEventListener('click', function() {
                console.log(' Abriendo selector de archivos...');
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                handleImageUpload(e.target.files, 'file');
            });
        }

        // Drag and drop functionality
        initializeDragAndDrop();

        console.log(' Sistema de subida inicializado');
    }

    function handleImageUpload(files, source) {
        if (!files || files.length === 0) {
            showNotification('No se seleccionaron archivos', 'warning');
            return;
        }

        console.log(` Subiendo ${files.length} archivo(s) desde ${source}`);

        // Validar archivos
        const validFiles = Array.from(files).filter(file => {
            if (!file.type.startsWith('image/')) {
                showNotification(`Archivo "${file.name}" no es una imagen válida`, 'error');
                return false;
            }

            // Validar tamaño (máximo 10MB)
            if (file.size > 10 * 1024 * 1024) {
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
                console.log(' Todas las imágenes subidas correctamente');
                showNotification(`${results.length} imagen(es) subida(s) correctamente`, 'success');

                // Agregar imágenes a la tabla
                results.forEach(imageData => {
                    addImageToTable(imageData);
                });

                setUploadState(uploadBtn, 'success');

                // Reset después de 2 segundos
                setTimeout(() => {
                    setUploadState(uploadBtn, 'normal');
                }, 2000);
            })
            .catch(error => {
                console.error(' Error subiendo imágenes:', error);
                showNotification('Error al subir las imágenes', 'error');
                setUploadState(uploadBtn, 'normal');
            });
    }

    function uploadSingleImage(file) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('timestamp', new Date().toISOString());

        setTimeout(() => {
            const imageUrl = URL.createObjectURL(file);

            // Datos base de la imagen
            const tempData = {
                id: Date.now() + Math.random(),
                url: imageUrl,
                name: file.name,
                size: file.size,
                uploadDate: new Date().toISOString(),
                ordenSit: generateOrderNumber(),
                po: generatePONumber(),
                oc: generateOCNumber()
            };

            // Abrir modal y esperar datos del usuario
            const modalEl = document.getElementById('imageDataModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Limpiar formulario
            document.getElementById('descripcionInput').value = '';
            document.getElementById('tipoFotografiaSelect').selectedIndex = 0;

            // Evento al guardar
            const saveBtn = document.getElementById('saveImageData');

            const handleSave = () => {
                const descripcion = document.getElementById('descripcionInput').value.trim();
                const tipoFotografia = document.getElementById('tipoFotografiaSelect').value;

                if (!descripcion || !tipoFotografia) {
                    alert("Por favor ingrese todos los campos.");
                    return;
                }

                modal.hide();

                // Resolver con los datos completos
                resolve({
                    ...tempData,
                    descripcion,
                    tipoFotografia
                });

                // Eliminar listener para evitar duplicados
                saveBtn.removeEventListener('click', handleSave);
            };

            saveBtn.addEventListener('click', handleSave);
        }, 1000 + Math.random() * 2000);
    });
}

    function addImageToTable(imageData) {
        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) return;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td data-column="imagen">
                <img src="${imageData.url}"
                    alt="${imageData.name}"
                    class="img-thumbnail preview-image"
                    style="width: 60px; height: 60px; cursor: pointer;"
                    onclick="openImageLightbox('${imageData.url}', '${imageData.name}', '${imageData.descripcion}', '${imageData.tipoFotografia}')">
            </td>
            <td data-column="orden-sit">${imageData.ordenSit}</td>
            <td data-column="po">${imageData.po}</td>
            <td data-column="oc">${imageData.oc}</td>
            <td data-column="descripcion">${imageData.descripcion}</td>
            <td data-column="tipo-fotografia">${imageData.tipoFotografia}</td>
            <td data-column="acciones">
                <button class="btn btn-danger btn-sm me-1 btn-delete" onclick="deleteImage(this)" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="btn btn-warning btn-sm me-1 btn-edit" onclick="editImage(this)" title="Editar información">
                    <i class="fas fa-edit"></i>
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

        // Agregar al inicio de la tabla
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

        // Remover clases anteriores
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
                // Estado normal, sin clases adicionales
                break;
        }
    }

    function initializeDragAndDrop() {
        const uploadBtns = document.querySelectorAll('.upload-btn');

        uploadBtns.forEach(btn => {
            // Prevenir comportamiento por defecto
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                btn.addEventListener(eventName, preventDefaults, false);
            });

            // Highlight en drag over
            ['dragenter', 'dragover'].forEach(eventName => {
                btn.addEventListener(eventName, () => btn.classList.add('active'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                btn.addEventListener(eventName, () => btn.classList.remove('active'), false);
            });

            // Handle drop
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

    // Utility functions para generar números
    function generateOrderNumber() {
        return '100' + Math.floor(Math.random() * 90000 + 10000);
    }

    function generatePONumber() {
        return '6000' + Math.floor(Math.random() * 900000 + 100000);
    }

    function generateOCNumber() {
        return '4200' + Math.floor(Math.random() * 9000000 + 1000000);
    }

    // En caso de querer descargar la imagen
    function downloadImageFromRow(button) {
        const row = button.closest('tr');
        const img = row.querySelector('img');
        if (img) {
            const link = document.createElement('a');
            link.href = img.src;
            link.download = img.alt || 'imagen';
            link.click();
            showNotification('Descarga iniciada', 'success');
        }
    }

    // Agregar a la inicialización principal
    document.addEventListener("DOMContentLoaded", function() {
        // ... otras inicializaciones
        initializeUploadButtons();
    });
</script>


@endsection
