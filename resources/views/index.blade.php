@extends('layout/plantilla')

@section('tituloPagina', 'Index')

@section('contenido')

    <div class="card">
        <div class="card-body">
            <div class="col-12">
                <h1 class="mb-3">
                    <i class="fas fa-images"></i> Galería de Imágenes
                </h1>

                <!-- Filtros -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="stage" class="form-label">Etapa de Prenda</label>
                                <select name="stage" id="stage" class="form-select">
                                    <option value="">Todas las etapas</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $stage)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-custom btn-primary-custom">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-custom btn-secondary-custom" id="toggleSort">
                                        <i class="fas fa-sort"></i> Ordenar
                                    </button>
                                    <button type="button" class="btn btn-custom btn-primary-custom" onclick="window.location.href='{{ route('images.create') }}'">
                                        <i class="fas fa-plus"></i> Agregar Imágenes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Galería -->
        <div class="row" id="imageGallery">
            @forelse($images as $image)
                <div class="col-6 col-md-3 col-lg-2 mb-4" data-image-id="{{ $image->id }}">
                    <div class="image-card">
                        <div class="order-badge">
                            <span class="badge bg-primary">{{ $image->orden_posicion }}</span>
                        </div>
                        <div class="stage-badge">
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $image->etapa_prenda)) }}</span>
                        </div>

                        <!-- Imagen con datos para el modal -->
                        <img src="{{ asset('storage/' . $image->tamanio_miniatura) }}"
                            alt="{{ $image->descripcion }}"
                            class="img-fluid w-100 gallery-image"
                            style="height: 200px; object-fit: cover; cursor: pointer;"
                            data-image-id="{{ $image->id }}"
                            data-full-url="{{ asset('storage/' . $image->tamanio_completo) }}"
                            data-description="{{ $image->descripcion }}"
                            data-stage="{{ $image->etapa_prenda }}"
                            data-order="{{ $image->orden_posicion }}"
                            data-size="{{ $image->tamanio_del_archivo }}"
                            data-edit-url="{{ route('images.edit', $image->id) }}">

                        <div class="image-overlay">
                            <p class="text-center mb-2">{{ Str::limit($image->descripcion, 50) }}</p>
                            <div class="btn-group btn-group-sm">
                                <!-- Botón Ver (que abre el modal) -->
                                <button type="button" class="btn btn-info btn-sm view-image-btn"
                                        data-image-id="{{ $image->id }}"
                                        data-full-url="{{ asset('storage/' . $image->tamanio_completo) }}"
                                        data-description="{{ $image->descripcion }}"
                                        data-stage="{{ $image->etapa_prenda }}"
                                        data-order="{{ $image->orden_posicion }}"
                                        data-size="{{ $image->tamanio_del_archivo }}"
                                        data-edit-url="{{ route('images.edit', $image->id) }}"
                                        title="Ver imagen completa">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Botón Editar -->
                                <button class="btn btn-light btn-sm" onclick="editImage({{ $image->id }})" title="Editar información">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Botón Recortar -->
                                <button class="btn btn-warning btn-sm" onclick="cropImage({{ $image->id }})" title="Recortar imagen">
                                    <i class="fas fa-crop"></i>
                                </button>

                                <!-- Botón Eliminar -->
                                <button class="btn btn-danger btn-sm" onclick="deleteImage({{ $image->id }})" title="Eliminar imagen">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">No hay imágenes</h3>
                        <p class="text-muted">Comienza agregando algunas imágenes a tu galería</p>
                        <a href="{{ route('images.create') }}" class="btn btn-custom btn-primary-custom">
                            <i class="fas fa-plus"></i> Agregar Primera Imagen
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($images->hasPages())
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    {{ $images->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Botón flotante para agregar -->
    <a href="{{ route('images.create') }}" class="btn btn-primary btn-lg fab-button rounded-circle">
        <i class="fas fa-plus"></i>
    </a>

    <!-- Modal para mostrar imagen en vista completa -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="imageModalLabel">
                        <i class="fas fa-image me-2"></i>
                        <span id="modalImageTitle">Imagen</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center">
                    <div class="position-relative">
                        <img id="modalImage" src="" alt="Imagen completa" class="img-fluid" style="max-height: 80vh; width: auto;">

                        <!-- Overlay con información -->
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-3">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1" id="modalImageDescription">Descripción</h6>
                                    <small class="text-muted">
                                        <span id="modalImageStage"></span> •
                                        <span id="modalImageOrder"></span> •
                                        <span id="modalImageSize"></span>
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="#" id="modalEditButton" class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times"></i> Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
    $(document).ready(function() {
        let sortable = null;
        let sortMode = false;

        // Configurar CSRF token para AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ====Manejo de clicks para el modal====
        // Funcion para abrir el modal
        function openImageModal(element) {
                const $element = $(element);

                // Obtener datos del elemento clickeado
                const imageData = {
                    id: $element.data('image-id'),
                    fullUrl: $element.data('full-url'),
                    description: $element.data('description'),
                    stage: $element.data('stage'),
                    order: $element.data('order'),
                    size: $element.data('size'),
                    editUrl: $element.data('edit-url')
                };

                console.log('Abriendo modal con datos:', imageData);

                // Validar que se tenga los datos necesarios
                if (!imageData.id || !imageData.fullUrl) {
                    console.error('Datos de imagen incompletos: ', imageData);
                    return;
                }

                // Actualizar contenido del modal
                $('#modalImage').attr('src', imageData.fullUrl);
                $('#modalImageTitle').text(imageData.description || 'Sin descripción');
                $('#modalImageDescription').text(imageData.description || 'Sin descripción');
                $('#modalImageStage').text(formatStage(imageData.stage));
                $('#modalImageOrder').text(`Orden: ${imageData.order || 'N/A'}`);
                $('#modalImageSize').text(formatFileSize(imageData.size));
                $('#modalEditButton').attr('href', imageData.editUrl || '#');

                // Mostrar el modal
                $('#imageModal').modal('show');
            }

            // Funcion para formatear la etapa
            function formatStage(stage) {
                const stages = {
                    'diseño': 'Diseño',
                    'confeccion': 'Confección',
                    'acabado': 'Acabado',
                    'control_calidad': 'Control de Calidad',
                    'empaque': 'Empaque'
                };
                return stages[stage] || stage || 'N/A';
            }

            // Funcion para formatear el tamaño del archivo
            function formatFileSize(bytes) {
                if (!bytes || bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

        //====>> Event listeners para abrir el modal <<====
        // 1. Click en imagen directamente
        $(document).on('click', 'img.clickable-image', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openImageModal(this);
        });

        // 2. Click en boton ver
        $(document).on('click', '.view-image-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openImageModal(this);
        });

        // 3. Click en icono dentro del boton ver
        $(document).on('click', '.view-image-btn i', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const button = $(this).closest('.view-image-btn');
            console.log('Click en icono del botón ver ID:', button.data('image-id'));
            openImageModal(button[0]);
        });

        // 4. TAMBIÉN capturar clicks en elementos con clase gallery-image (por si acaso)
        $(document).on('click', '.gallery-image', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Click en gallery-image ID:', $(this).data('image-id'));
            openImageModal(this);
        });

        // Tecla ESC para cerrar modal
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('#imageModal').modal('hide');
            }
        });

        // ========== FUNCIONES GLOBALES ==========

        window.editImage = function(imageId) {
            window.location.href = `/images/${imageId}/edit`;
        };

        window.cropImage = function(imageId) {
            window.location.href = `/images/${imageId}/edit#crop`;
        };

        window.deleteImage = function(imageId) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                return;
            }

            $.ajax({
                url: `/images/${imageId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $(`[data-image-id="${imageId}"]`).fadeOut(400, function() {
                            $(this).remove();
                            if ($('[data-image-id]').length === 0) {
                                setTimeout(() => location.reload(), 1000);
                            }
                        });
                        showAlert('Imagen eliminada exitosamente', 'success');
                    } else {
                        showAlert('Error al eliminar la imagen', 'danger');
                    }
                },
                error: function() {
                    showAlert('Error al eliminar la imagen', 'danger');
                }
            });
        };

        window.showAlert = function(message, type) {
            const alertDiv = $(`
                <div class="alert alert-${type} alert-dismissible fade show position-fixed"
                    style="top: 20px; right: 20px; z-index: 9999; max-width: 350px;">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);

            $('body').append(alertDiv);
            setTimeout(() => alertDiv.alert('close'), 5000);
        };

        // ========== ORDENAMIENTO ==========

        $('#toggleSort').on('click', function() {
            sortMode = !sortMode;
            const gallery = $('#imageGallery');

            if (sortMode) {
                $(this).html('<i class="fas fa-save"></i> Guardar Orden')
                    .removeClass('btn-secondary-custom')
                    .addClass('btn-success');

                sortable = Sortable.create(gallery[0], {
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });

                $('.order-badge').show();

            } else {
                $(this).html('<i class="fas fa-sort"></i> Ordenar')
                    .removeClass('btn-success')
                    .addClass('btn-secondary-custom');

                if (sortable) {
                    saveOrder();
                    sortable.destroy();
                    sortable = null;
                }
            }
        });

        function saveOrder() {
            const orderData = [];
            $('#imageGallery > div[data-image-id]').each(function(index) {
                const imageId = $(this).data('image-id');
                if (imageId) {
                    orderData.push({
                        id: imageId,
                        orden_posicion: index + 1
                    });
                }
            });

            $.ajax({
                url: '{{ route("images.update-order") }}',
                method: 'POST',
                data: { images: orderData },
                success: function(response) {
                    if (response.success) {
                        showAlert('Orden guardado exitosamente', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('Error al guardar el orden', 'danger');
                    }
                },
                error: function() {
                    showAlert('Error al guardar el orden', 'danger');
                }
            });
        }
    });
    </script>


@endsection
