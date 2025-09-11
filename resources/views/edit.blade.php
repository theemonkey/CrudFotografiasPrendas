@extends('layout/plantilla')

@section('tituloPagina', 'Editar imagen')

@section('contenido')

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border text-light mb-3" role="status"></div>
            <div>Procesando imagen...</div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <!-- Card principal -->
        <div class="card shadow-sm">
            <!-- Header -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0"><i class="fas fa-crop"></i> Editar Imagen</h1>
                <div>
                    <a href="{{ route('images.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Volver a Galería
                    </a>
                    <button type="button" class="btn btn-danger" onclick="deleteImage()">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>

            <!-- Cuerpo del card -->
            <div class="card-body">
                <!-- Panel de información -->
                <div class="info-panel">
                    <div class="row">
                        <div class="col-md-8">
                            <h6><i class="fas fa-info-circle"></i> Información de la Imagen</h6>
                            <p class="mb-1"><strong>Descripción:</strong> {{ $image->description }}</p>
                            <p class="mb-1"><strong>Etapa:</strong> {{ ucfirst(str_replace('_', ' ', $image->clothing_stage)) }}</p>
                            <p class="mb-1"><strong>Orden:</strong> {{ $image->order_position }}</p>
                            <p class="mb-0"><strong>Archivo original:</strong> {{ $image->original_name }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editInfoModal">
                                <i class="fas fa-edit"></i> Editar Información
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Herramientas de recorte -->
                <div class="crop-tools">
                    <h6><i class="fas fa-tools"></i> Herramientas de Recorte</h6>

                    <!-- Controles principales -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Relación de Aspecto:</label>
                            <div class="aspect-ratio-buttons">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAspectRatio('free')">Libre</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAspectRatio(1)">1:1 (Cuadrado)</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAspectRatio(16/9)">16:9</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAspectRatio(4/3)">4:3</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAspectRatio(3/2)">3:2</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Acciones Rápidas:</label>
                            <div>
                                <div class="btn-group me-2">
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="zoomCropper(0.1)">
                                        <i class="fas fa-search-plus"></i> Zoom +
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="zoomCropper(-0.1)">
                                        <i class="fas fa-search-minus"></i> Zoom -
                                    </button>
                                </div>
                                <div class="btn-group me-2">
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="rotateCropper(-90)">
                                        <i class="fas fa-undo"></i> ↶ 90°
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="rotateCropper(90)">
                                        <i class="fas fa-redo"></i> ↷ 90°
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="flipCropper('horizontal')">
                                        <i class="fas fa-arrows-alt-h"></i> Flip H
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="flipCropper('vertical')">
                                        <i class="fas fa-arrows-alt-v"></i> Flip V
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Controles avanzados -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success" onclick="cropAndSave()">
                                <i class="fas fa-crop"></i> Aplicar Recorte
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetCropper()">
                                <i class="fas fa-refresh"></i> Restablecer
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="crop-data-display" id="cropData">
                                Selecciona un área para ver las coordenadas de recorte
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imagen principal para recortar -->
                <div class="text-center mb-4">
                    <img src="{{ $image->full_size_url }}" id="cropperImage" class="img-fluid" style="max-width: 100%; max-height: 600px;">
                </div>

                <!-- Vista previa en tiempo real -->
                <div class="preview-container">
                    <div class="preview-box">
                        <h6><i class="fas fa-eye"></i> Vista Previa - Imagen Completa</h6>
                        <div id="fullPreview" style="width: 200px; height: 200px; overflow: hidden; margin: 0 auto; border: 1px solid #ddd;"></div>
                        <small class="text-muted">Imagen recortada completa</small>
                    </div>

                    <div class="preview-box">
                        <h6><i class="fas fa-image"></i> Vista Previa - Thumbnail</h6>
                        <div id="thumbnailPreview" style="width: 150px; height: 150px; overflow: hidden; margin: 0 auto; border: 1px solid #ddd;"></div>
                        <small class="text-muted">Thumbnail 150x150px</small>
                    </div>

                    <div class="preview-box">
                        <h6><i class="fas fa-mobile-alt"></i> Vista Previa - Móvil</h6>
                        <div id="mobilePreview" style="width: 100px; height: 100px; overflow: hidden; margin: 0 auto; border: 1px solid #ddd;"></div>
                        <small class="text-muted">Vista móvil 100x100px</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar información -->
    <div class="modal fade" id="editInfoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Información</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editInfoForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ $image->description }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="clothing_stage" class="form-label">Etapa de Prenda</label>
                            <select class="form-select" id="clothing_stage" name="clothing_stage" required>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage }}" {{ $image->clothing_stage === $stage ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $stage)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="order_position" class="form-label">Posición de Orden</label>
                            <input type="number" class="form-control" id="order_position" name="order_position" value="{{ $image->order_position }}" min="1" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveImageInfo()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        class ImageEditor {
            constructor() {
                this.cropper = null;
                this.imageId = {{ $image->id }};
                this.originalImageUrl = '{{ $image->full_size_url }}';
                this.init();
            }

            init() {
                this.initCropper();
                this.setupEventListeners();
                this.updatePreviews();
            }

            initCropper() {
                const image = document.getElementById('cropperImage');

                this.cropper = new Cropper(image, {
                    aspectRatio: NaN, // Relación libre por defecto
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    ready: () => {
                        this.updatePreviews();
                        this.updateCropData();
                    },
                    cropstart: () => {
                        this.updateCropData();
                    },
                    cropmove: () => {
                        this.updatePreviews();
                        this.updateCropData();
                    },
                    cropend: () => {
                        this.updatePreviews();
                        this.updateCropData();
                    }
                });
            }

            setupEventListeners() {
                // Event listeners ya están en las funciones onclick
            }

            updatePreviews() {
                if (!this.cropper) return;

                const canvas = this.cropper.getCroppedCanvas();
                if (!canvas) return;

                // Vista previa completa
                const fullPreview = document.getElementById('fullPreview');
                const fullCanvas = this.cropper.getCroppedCanvas({
                    width: 200,
                    height: 200
                });
                if (fullCanvas) {
                    fullPreview.innerHTML = '';
                    fullPreview.appendChild(fullCanvas);
                }

                // Vista previa thumbnail
                const thumbnailPreview = document.getElementById('thumbnailPreview');
                const thumbCanvas = this.cropper.getCroppedCanvas({
                    width: 150,
                    height: 150
                });
                if (thumbCanvas) {
                    thumbnailPreview.innerHTML = '';
                    thumbnailPreview.appendChild(thumbCanvas);
                }

                // Vista previa móvil
                const mobilePreview = document.getElementById('mobilePreview');
                const mobileCanvas = this.cropper.getCroppedCanvas({
                    width: 100,
                    height: 100
                });
                if (mobileCanvas) {
                    mobilePreview.innerHTML = '';
                    mobilePreview.appendChild(mobileCanvas);
                }
            }

            updateCropData() {
                if (!this.cropper) return;

                const data = this.cropper.getData();
                const canvasData = this.cropper.getCanvasData();
                const cropBoxData = this.cropper.getCropBoxData();

                document.getElementById('cropData').innerHTML = `
                    <strong>Coordenadas:</strong><br>
                    X: ${Math.round(data.x)}px, Y: ${Math.round(data.y)}px<br>
                    Ancho: ${Math.round(data.width)}px, Alto: ${Math.round(data.height)}px<br>
                    Rotación: ${Math.round(data.rotate)}°<br>
                    Escala: ${data.scaleX.toFixed(2)} x ${data.scaleY.toFixed(2)}
                `;
            }

            setAspectRatio(ratio) {
                if (!this.cropper) return;

                // Actualizar botones activos
                document.querySelectorAll('.aspect-ratio-buttons .btn').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                event.target.classList.remove('btn-outline-secondary');
                event.target.classList.add('btn-primary');

                this.cropper.setAspectRatio(ratio === 'free' ? NaN : ratio);
            }

            zoomCropper(ratio) {
                if (!this.cropper) return;
                this.cropper.zoom(ratio);
            }

            rotateCropper(degree) {
                if (!this.cropper) return;
                this.cropper.rotate(degree);
                this.updatePreviews();
                this.updateCropData();
            }

            flipCropper(direction) {
                if (!this.cropper) return;

                if (direction === 'horizontal') {
                    const scaleX = this.cropper.getData().scaleX;
                    this.cropper.scaleX(-scaleX);
                } else {
                    const scaleY = this.cropper.getData().scaleY;
                    this.cropper.scaleY(-scaleY);
                }

                this.updatePreviews();
                this.updateCropData();
            }

            resetCropper() {
                if (!this.cropper) return;
                this.cropper.reset();
                this.updatePreviews();
                this.updateCropData();
            }

            async cropAndSave() {
                if (!this.cropper) return;

                const data = this.cropper.getData();

                document.getElementById('loadingOverlay').style.display = 'flex';

                try {
                    const response = await fetch('{{ route("images.crop") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            image_id: this.imageId,
                            x: data.x,
                            y: data.y,
                            width: data.width,
                            height: data.height,
                            rotate: data.rotate,
                            scaleX: data.scaleX,
                            scaleY: data.scaleY
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Mostrar mensaje de éxito
                        this.showAlert('success', 'Imagen recortada correctamente');

                        // Recargar la página después de un breve delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Error al procesar la imagen');
                    }
                } catch (error) {
                    this.showAlert('danger', 'Error: ' + error.message);
                } finally {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }
            }

            async saveImageInfo() {
                const form = document.getElementById('editInfoForm');
                const formData = new FormData(form);

                try {
                    const response = await fetch('{{ route("images.update", $image) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showAlert('success', 'Información actualizada correctamente');

                        // Cerrar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editInfoModal'));
                        modal.hide();

                        // Recargar después de un breve delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Error al actualizar');
                    }
                } catch (error) {
                    this.showAlert('danger', 'Error: ' + error.message);
                }
            }

            async deleteImage() {
                if (!confirm('¿Estás seguro de que quieres eliminar esta imagen? Esta acción no se puede deshacer.')) {
                    return;
                }

                try {
                    const response = await fetch('{{ route("images.destroy", $image) }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showAlert('success', 'Imagen eliminada correctamente');

                        setTimeout(() => {
                            window.location.href = '{{ route("images.index") }}';
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Error al eliminar');
                    }
                } catch (error) {
                    this.showAlert('danger', 'Error: ' + error.message);
                }
            }

            showAlert(type, message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        }

        // Funciones globales para los botones
        let imageEditor;

        document.addEventListener('DOMContentLoaded', function() {
            imageEditor = new ImageEditor();
        });

        function setAspectRatio(ratio) {
            imageEditor.setAspectRatio(ratio);
        }

        function zoomCropper(ratio) {
            imageEditor.zoomCropper(ratio);
        }

        function rotateCropper(degree) {
            imageEditor.rotateCropper(degree);
        }

        function flipCropper(direction) {
            imageEditor.flipCropper(direction);
        }

        function resetCropper() {
            imageEditor.resetCropper();
        }

        function cropAndSave() {
            imageEditor.cropAndSave();
        }

        function saveImageInfo() {
            imageEditor.saveImageInfo();
        }

        function deleteImage() {
            imageEditor.deleteImage();
        }
    </script>
@endsection
