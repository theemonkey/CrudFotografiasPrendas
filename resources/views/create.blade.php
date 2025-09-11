@extends('layout/plantilla')

@section('tituloPagina', 'Agregar Imagenes - Galeria')

@section('contenido')

<div class="container-fluid py-4">
    <!-- Card principal que contiene todo -->
    <div class="card shadow-sm">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0"><i class="fas fa-camera"></i> Agregar Imágenes</h1>
            <a href="{{ route('images.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Galería
            </a>
        </div>

        <!-- Cuerpo del card con todo el contenido -->
        <div class="card-body">
            <!-- Indicador de pasos -->
            <div class="step-indicator mb-4">
                <div class="step active" id="step1">
                    <div class="step-number">1</div>
                    <span>Seleccionar Imágenes</span>
                </div>
                <div class="step" id="step2">
                    <div class="step-number">2</div>
                    <span>Configurar Datos</span>
                </div>
                <div class="step" id="step3">
                    <div class="step-number">3</div>
                    <span>Guardar</span>
                </div>
            </div>

            <!-- Formulario principal -->
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf

                <!-- Paso 1: Selección de imágenes -->
                <div id="stepContent1">
                    <!-- Título del paso -->
                    <h5 class="mb-4"><i class="fas fa-images"></i> Paso 1: Seleccionar Imágenes</h5>

                    <!-- Área de carga de archivos -->
                    <div class="upload-area mb-4" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h4>Arrastra y suelta tus imágenes aquí</h4>
                        <p class="text-muted mb-3">o haz clic para seleccionar archivos</p>
                        <input type="file" id="fileInput" name="images[]" multiple accept="image/*" style="display: none;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-folder-open"></i> Seleccionar Archivos
                            </button>
                            <button type="button" class="btn btn-camera" id="cameraBtn">
                                <i class="fas fa-camera"></i> Usar Cámara
                            </button>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                Formatos soportados: JPG, PNG, GIF | Tamaño máximo: 10MB por imagen
                            </small>
                        </div>
                    </div>

                    <!-- Sección de cámara -->
                    <div class="camera-section mb-4" id="cameraSection">
                        <h5><i class="fas fa-video"></i> Capturar desde Cámara</h5>
                        <video id="cameraPreview" class="camera-preview" autoplay playsinline></video>
                        <canvas id="cameraCanvas" style="display: none;"></canvas>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success" id="captureBtn">
                                <i class="fas fa-camera"></i> Capturar Foto
                            </button>
                            <button type="button" class="btn btn-secondary" id="stopCameraBtn">
                                <i class="fas fa-stop"></i> Cerrar Cámara
                            </button>
                        </div>
                    </div>

                    <!-- Preview de imágenes seleccionadas -->
                    <div class="preview-container" id="previewContainer" style="display: none;">
                        <h5><i class="fas fa-eye"></i> Imágenes Seleccionadas (<span id="imageCount">0</span>)</h5>
                        <div id="imagePreviewArea" class="d-flex flex-wrap gap-2 mt-3"></div>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Puedes eliminar imagenes haciendo clic en la X
                            </small>
                            <button type="button" class="btn btn-success" id="nextStep1">
                                Continuar <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Configuración de datos -->
                <div id="stepContent2" style="display: none;">
                    <!-- Título del paso -->
                    <h5 class="mb-4"><i class="fas fa-edit"></i> Paso 2: Configurar Información</h5>

                    <!-- Configuración global -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="globalStage" class="form-label">Etapa de Prenda (Global)</label>
                            <select class="form-select" id="globalStage" name="clothing_stage" required>
                                <option value="">Selecciona una etapa</option>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage }}">{{ ucfirst(str_replace('_', ' ', $stage)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="globalOrder" class="form-label">Orden inicial</label>
                            <input type="number" class="form-control" id="globalOrder" value="1" min="1">
                            <small class="text-muted">Las imágenes se numerarán secuencialmente desde este valor</small>
                        </div>
                    </div>

                    <hr>

                    <!-- Configuración individual por imagen -->
                    <h6><i class="fas fa-list"></i> Configuración Individual</h6>
                    <div id="imageConfigArea"></div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-secondary" id="prevStep2">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-success" id="nextStep2">
                            Continuar <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Paso 3: Confirmación y guardado -->
                <div id="stepContent3" style="display: none;">
                    <!-- Título del paso -->
                    <h5 class="mb-4"><i class="fas fa-save"></i> Paso 3: Confirmar y Guardar</h5>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Resumen:</strong> Se van a guardar <span id="totalImages">0</span> imágenes.
                    </div>

                    <div id="finalPreview"></div>

                    <!-- Barra de progreso -->
                    <div class="progress-container" id="progressContainer">
                        <h6>Guardando imágenes...</h6>
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                 id="uploadProgress" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div id="progressText">Iniciando...</div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-secondary" id="prevStep3">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <i class="fas fa-save"></i> Guardar Imágenes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        class ImageUploader {
            constructor() {
                this.selectedFiles = [];
                this.currentStep = 1;
                this.cameraStream = null;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.setupDragAndDrop();
                this.updateStepIndicator();
            }

            setupEventListeners() {
                // File input
                document.getElementById('fileInput').addEventListener('change', (e) => {
                    this.handleFileSelect(e.target.files);
                });

                // Camera controls
                document.getElementById('cameraBtn').addEventListener('click', () => this.startCamera());
                document.getElementById('captureBtn').addEventListener('click', () => this.capturePhoto());
                document.getElementById('stopCameraBtn').addEventListener('click', () => this.stopCamera());

                // Step navigation
                document.getElementById('nextStep1').addEventListener('click', () => this.goToStep(2));
                document.getElementById('prevStep2').addEventListener('click', () => this.goToStep(1));
                document.getElementById('nextStep2').addEventListener('click', () => this.goToStep(3));
                document.getElementById('prevStep3').addEventListener('click', () => this.goToStep(2));

                // Form submission
                document.getElementById('uploadForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.submitForm();
                });

                // Global settings
                document.getElementById('globalStage').addEventListener('change', () => this.updateGlobalStage());
                document.getElementById('globalOrder').addEventListener('input', () => this.updateGlobalOrder());
            }

            setupDragAndDrop() {
                const uploadArea = document.getElementById('uploadArea');

                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.classList.remove('dragover');
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    this.handleFileSelect(e.dataTransfer.files);
                });

                uploadArea.addEventListener('click', () => {
                    document.getElementById('fileInput').click();
                });
            }

            /*=====>>>>>>>CONTADOR DE IMAGENES<<<<<<<===========*/
            updateImageCount() {
                const count = this.selectedFiles.length;
                document.getElementById('imageCount').textContent = count;
                //document.getElementById('continueCount').textContent = count;

                const nextBtn = document.getElementById('nextStep1');
                if (count === 0) {
                    nextBtn.disabled = true;
                } else {
                    nextBtn.disabled = false;
                }
            }

            handleFileSelect(files) {
                for (let file of files) {
                    if (file.type.startsWith('image/')) {
                        const fileObj = {
                            file: file,
                            id: 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                            description: '',
                            order: this.selectedFiles.length + 1,
                            stage: ''
                        };
                        this.selectedFiles.push(fileObj);
                    }
                }
                this.updatePreview();
                this.updateImageCount();
                //Solo mostrar si hay imagenes
                if (this.selectedFiles.length > 0){
                    this.showPreviewContainer();
                }
            }

            async startCamera() {
                try {
                    this.cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment' // Usar cámara trasera en móviles
                        }
                    });
                    const video = document.getElementById('cameraPreview');
                    video.srcObject = this.cameraStream;
                    document.getElementById('cameraSection').style.display = 'block';
                } catch (error) {
                    alert('Error al acceder a la cámara: ' + error.message);
                }
            }

            capturePhoto() {
                const video = document.getElementById('cameraPreview');
                const canvas = document.getElementById('cameraCanvas');
                const ctx = canvas.getContext('2d');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0);

                canvas.toBlob((blob) => {
                    const file = new File([blob], `camera_${Date.now()}.jpg`, { type: 'image/jpeg' });
                    const fileObj = {
                        file: file,
                        id: 'cam_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                        description: '',
                        order: this.selectedFiles.length + 1,
                        stage: ''
                    };
                    this.selectedFiles.push(fileObj);
                    this.updatePreview();
                    this.updateImageCount();

                    //Mostrar la seccion despues de capturar imagen
                    if(this.selectedFiles.length > 0) {
                        this.showPreviewContainer();
                    }
                }, 'image/jpeg', 0.8);
            }

            stopCamera() {
                if (this.cameraStream) {
                    this.cameraStream.getTracks().forEach(track => track.stop());
                    document.getElementById('cameraSection').style.display = 'none';
                }
            }

            updatePreview() {
                const previewArea = document.getElementById('imagePreviewArea');
                previewArea.innerHTML = '';

                this.selectedFiles.forEach((fileObj, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'image-preview';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}">
                            <button type="button" class="remove-btn" onclick="imageUploader.removeImage('${fileObj.id}')">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="overlay">
                                <small>Imagen ${index + 1}</small>
                                <small>${(fileObj.file.size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                        `;
                        previewArea.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(fileObj.file);
                });
            }

            removeImage(id) {
                this.selectedFiles = this.selectedFiles.filter(f => f.id !== id);
                this.updatePreview();
                this.updateImageCount();
                // Oculta la seccion si no hay imagenes
                if (this.selectedFiles.length === 0) {
                    this.hidePreviewContainer();
                }
                this.reorderImages();
            }

            reorderImages() {
                this.selectedFiles.forEach((fileObj, index) => {
                    fileObj.order = index + 1;
                });
            }

            showPreviewContainer() {
                document.getElementById('previewContainer').style.display = 'block';
                document.getElementById('uploadArea').classList.add('active');
            }

            hidePreviewContainer() {
                document.getElementById('previewContainer').style.display = 'none';
                document.getElementById('uploadArea').classList.remove('active');
            }

            goToStep(step) {
                if (step === 2 && this.selectedFiles.length === 0) {
                    alert('Debes seleccionar al menos una imagen');
                    return;
                }

                if (step === 3 && !this.validateStep2()) {
                    return;
                }

                // Ocultar todos los steps
                for (let i = 1; i <= 3; i++) {
                    document.getElementById(`stepContent${i}`).style.display = 'none';
                }

                // Mostrar step actual
                document.getElementById(`stepContent${step}`).style.display = 'block';
                this.currentStep = step;
                this.updateStepIndicator();

                if (step === 2) {
                    this.setupStep2();
                } else if (step === 3) {
                    this.setupStep3();
                }
            }

            setupStep2() {
                const configArea = document.getElementById('imageConfigArea');
                configArea.innerHTML = '';

                this.selectedFiles.forEach((fileObj, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const configDiv = document.createElement('div');
                        configDiv.className = 'image-info mb-3';
                        configDiv.innerHTML = `
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 80px;">
                                </div>
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Descripción</label>
                                            <input type="text" class="form-control" data-field="description" data-id="${fileObj.id}"
                                                   placeholder="Descripción de la imagen ${index + 1}" value="${fileObj.description}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Orden</label>
                                            <input type="number" class="form-control" data-field="order" data-id="${fileObj.id}"
                                                   value="${fileObj.order}" min="1">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Etapa</label>
                                            <select class="form-select" data-field="stage" data-id="${fileObj.id}">
                                                <option value="">Usar global</option>
                                                @foreach($stages as $stage)
                                                    <option value="{{ $stage }}" ${fileObj.stage === '{{ $stage }}' ? 'selected' : ''}>
                                                        {{ ucfirst(str_replace('_', ' ', $stage)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        configArea.appendChild(configDiv);

                        // Agregar event listeners
                        configDiv.querySelectorAll('input, select').forEach(input => {
                            input.addEventListener('change', (e) => {
                                const id = e.target.dataset.id;
                                const field = e.target.dataset.field;
                                const fileObj = this.selectedFiles.find(f => f.id === id);
                                if (fileObj) {
                                    fileObj[field] = e.target.value;
                                }
                            });
                        });
                    };
                    reader.readAsDataURL(fileObj.file);
                });
            }

            setupStep3() {
                const finalPreview = document.getElementById('finalPreview');
                document.getElementById('totalImages').textContent = this.selectedFiles.length;

                finalPreview.innerHTML = `
                    <div class="row">
                        ${this.selectedFiles.map((fileObj, index) => `
                            <div class="col-6 col-md-3 mb-3">
                                <div class="card">
                                    <div class="card-body p-2 text-center">
                                        <small><strong>Imagen ${fileObj.order}</strong></small><br>
                                        <small class="text-muted">${fileObj.description || 'Sin descripción'}</small><br>
                                        <small class="badge bg-secondary">${fileObj.stage || document.getElementById('globalStage').value}</small>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            updateGlobalStage() {
                const globalStage = document.getElementById('globalStage').value;
                this.selectedFiles.forEach(fileObj => {
                    if (!fileObj.stage) {
                        fileObj.stage = globalStage;
                    }
                });
            }

            updateGlobalOrder() {
                const startOrder = parseInt(document.getElementById('globalOrder').value) || 1;
                this.selectedFiles.forEach((fileObj, index) => {
                    fileObj.order = startOrder + index;
                });
                // Actualizar inputs si estamos en el paso 2
                if (this.currentStep === 2) {
                    document.querySelectorAll('[data-field="order"]').forEach((input, index) => {
                        input.value = startOrder + index;
                    });
                }
            }

            validateStep2() {
                const globalStage = document.getElementById('globalStage').value;
                if (!globalStage) {
                    alert('Debes seleccionar una etapa global');
                    return false;
                }

                for (let fileObj of this.selectedFiles) {
                    if (!fileObj.description.trim()) {
                        alert('Todas las imágenes deben tener una descripción');
                        return false;
                    }
                }

                return true;
            }

            async submitForm() {
                const formData = new FormData();
                const globalStage = document.getElementById('globalStage').value;

                // Agregar archivos con nombres segun controlador
                this.selectedFiles.forEach((fileObj, index) => {
                    formData.append('images[]', fileObj.file);
                    formData.append('descripciones[]', fileObj.description);
                    formData.append('orden_posiciones[]', fileObj.order);
                });

                formData.append('etapa_prenda', globalStage);
                formData.append('orden_posicion', 1);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                // Mostrar progreso
                document.getElementById('progressContainer').style.display = 'block';
                document.getElementById('saveBtn').disabled = true;

                try {
                    const response = await fetch('{{ route("images.store") }}', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.updateProgress(100, 'Completado!');
                        setTimeout(() => {
                            window.location.href = '{{ route("images.index") }}';
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Error al guardar');
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                    document.getElementById('saveBtn').disabled = false;
                    document.getElementById('progressContainer').style.display = 'none';
                }
            }

            updateProgress(percent, text) {
                document.getElementById('uploadProgress').style.width = percent + '%';
                document.getElementById('progressText').textContent = text;
            }

            updateStepIndicator() {
                for (let i = 1; i <= 3; i++) {
                    const step = document.getElementById(`step${i}`);
                    step.classList.remove('active', 'completed');

                    if (i < this.currentStep) {
                        step.classList.add('completed');
                    } else if (i === this.currentStep) {
                        step.classList.add('active');
                    }
                }
            }
        }

        // Inicializar la aplicación
        const imageUploader = new ImageUploader();

        // Configurar CSRF para todas las peticiones AJAX
        /*const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (token) {
            window.axios = axios;
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        }*/
    </script>
@endsection
