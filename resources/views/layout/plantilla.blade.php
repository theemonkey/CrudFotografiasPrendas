<!doctype html>
<html lang="es">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Agregar Flatpickr para el calendario -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

        <!-- Font Awesome version reciente-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">

        <!-- Declaramos dos secciones yield para llamarlas luego -->
        <title>@yield('tituloPagina')</title>

        <!-- Aquí se carga CSS específico de cada vista -->
        <link rel="stylesheet" href="{{ asset('css/index.css') }}">
        <link rel="stylesheet" href="{{ asset('css/create.css') }}">
        <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
        <link rel="stylesheet" href="{{ asset('css/fotos-index.css') }}">

        <!-- Meta para usuario actual -->
        <meta name="current-user" content="{{ auth()->user()->name ?? 'Usuario Sistema' }}">
        @stack('styles')

    </head>
    <body>

        <div class="container">
            @yield('contenido')
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

        <!-- CropperJS (para funcionalidad de recorte) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

        <!-- SortableJS (para ordenamiento) -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

        <!-- JS principales de pagina -->
        <script src="{{ asset('js/fotos-index.js') }}"></script>

        <!-- Archivo JS para responsive en dispositivos moviles -->
        <script src="{{ asset('js/mobile-cards.js') }}"></script>


        @stack('scripts')
    </body>
</html>
