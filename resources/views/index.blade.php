<!DOCTYPE html>
<html lang="en" class="overflow-x-hidden scroll-smooth group" data-mode="light" dir="ltr">

<head>

    <meta charset="utf-8">
    <title>UATF | Personal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('uatf/AUTF.png') }}">
    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script> <!-- Icons CSS -->

    <!-- StarCode CSS -->


    <link rel="stylesheet" href="assets/css/starcode2.css">
</head>

<body class="text-base bg-white text-body font-public dark:text-zink-50 dark:bg-zink-800">

    <nav class="fixed inset-x-0 top-0 z-50 flex items-center justify-center h-20 py-3 [&.is-sticky]:bg-white dark:[&.is-sticky]:bg-zink-700 border-b border-slate-200 dark:border-zink-500 [&.is-sticky]:shadow-lg [&.is-sticky]:shadow-slate-200/25 dark:[&.is-sticky]:shadow-zink-500/30 navbar"
        id="navbar" style="background: #fff" >
        <div class="container 2xl:max-w-[87.5rem] px-4 mx-auto flex items-center self-center w-full">
            <div class="shrink-0">
                <span class="hidden group-data-[sidebar-size=sm]:block">
                    <img src="{{ asset('uatf/personal.jpg') }}" alt="" class="h-6 mx-auto">
                </span>
                <span class="flex items-center group-data-[sidebar-size=sm]:hidden">
                    <img src="{{ asset('uatf/personal.jpg') }}" alt="" class="h-6 mr-2">
                    <strong>Personal</strong>
                </span>
            </div>
            <div class="mx-auto">
                <ul id="navbar7"
                    class="absolute inset-x-0 z-20 items-center hidden py-3 bg-white shadow-lg dark:bg-zink-600 dark:md:bg-transparent md:z-0 navbar-menu rounded-b-md md:shadow-none md:flex top-full ltr:ml-auto rtl:mr-auto md:relative md:bg-transparent md:rounded-none md:top-auto md:py-0">
                    <li>
                        <a href="#home"
                            class="block md:inline-block px-4 md:px-3 py-2.5 md:py-0.5 text-15 font-medium text-slate-800 transition-all duration-300 ease-linear hover:text-custom-500 [&.active]:text-custom-500 dark:text-zink-100 dark:hover:text-custom-500 dark:[&.active]:text-custom-500 active">Inicio</a>
                    </li>

                    <li>
                        <a href="#about"
                            class="block md:inline-block px-4 md:px-3 py-2.5 md:py-0.5 text-15 font-medium text-slate-800 transition-all duration-300 ease-linear hover:text-custom-500 [&.active]:text-custom-500 dark:text-zink-100 dark:hover:text-custom-500 dark:[&.active]:text-custom-500">Acerca
                            de Nosotros</a>
                    </li>

                    <li>
                        <a href="#contact"
                            class="block md:inline-block px-4 md:px-3 py-2.5 md:py-0.5 text-15 font-medium text-slate-800 transition-all duration-300 ease-linear hover:text-custom-500 [&.active]:text-custom-500 dark:text-zink-100 dark:hover:text-custom-500 dark:[&.active]:text-custom-500">Contacto</a>
                    </li>
                </ul>
            </div>
            <div class="flex gap-2">
                <div class="ltr:ml-auto rtl:mr-auto md:hidden navbar-toggale-button">
                    <button type="button"
                        class="flex items-center  justify-center size-[37.5px] p-0 text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"><i
                            data-lucide="menu"></i></button>
                </div>
                <a href="{{ route('login') }}"
                    class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"><span
                        class="align-middle">Iniciar sesión</span> <i data-lucide="log-in"
                        class="inline-block size-4 ltr:ml-1 rtl:mr-1"></i></a>
            </div>
        </div>
    </nav>

<section class="relative pb-36 pt-44 bg-cover bg-center bg-no-repeat" id="home"
         style="background-image: linear-gradient(rgba(25, 21, 61, 0.94), rgba(25, 21, 61, 0.94)), url('{{ asset('uatf/FRONTIS-blanco.png') }}');">
    <div class="container 2xl:max-w-[87.5rem] px-4 mx-auto">
        <div class="grid grid-cols-12 2xl:grid-cols-2">
            <div class="col-span-12 lg:col-span-7 2xl:col-span-1">
                <h1 class="mb-8 !leading-relaxed md:text-5xl">
                    <span class="relative inline-block px-2 mx-2 before:block before:absolute before:-inset-1 before:-skew-y-6 before:bg-sky-50 dark:before:bg-sky-500/20 before:rounded-md before:backdrop-blur-xl">
                        <span class="relative text-sky-500">Universidad Autónoma </span>

                        <span class="relative text-sky-500">TOMÁS FRÍAS</span>
                    </span>
                </h1>
                <p class="mb-6 text-lg text-slate-500 dark:text-zink-200">
                    El área de personal de la Universidad Autónoma Tomás Frías (UATF) se encarga de gestionar y apoyar a los trabajadores administrativos.
                    Su objetivo es organizar y facilitar la administración de permisos y vacaciones, asegurando un proceso claro y eficiente.
                </p>
            </div>
        </div>
    </div>
</section>


    <section class="relative pb-32 bg-slate-50 dark:bg-zink-700/40">
        <div class="container 2xl:max-w-[87.5rem] px-4 mx-auto">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-12 gap-x-5">
                <div class="xl:col-span-4">
                    <div class="transition-all duration-300 ease-linear md:-mt-36 card hover:-translate-y-2 dark:bg-zink-600">
                        <div class="p-6">
                            <img src="{{asset('assets/images/personal-1.png')}}" alt="Imagen del administrador personal" class="rounded-md shadow">
                            <div class="mt-6">
                                <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border bg-purple-100 border-purple-200 text-purple-500 dark:bg-purple-500/20 dark:border-purple-500/20">
                                    Administración Personal
                                </span>
                                <h6 class="mt-3 mb-2 text-lg truncate">
                                    <a href="#!">Administrador General</a>
                                </h6>
                                <p class="mb-3 text-slate-500 dark:text-zink-200 text-16">
                                    Módulo encargado de supervisar, coordinar y administrar todas las áreas del sistema de forma centralizada.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </div><!--end col-->
                <div class="xl:col-span-4">
                    <div class="transition-all duration-300 ease-linear md:-mt-36 card hover:-translate-y-2 dark:bg-zink-600">
                        <div class="p-6">
                            <img src="{{asset('assets/images/personal-2.png')}}" alt="Sitio de solicitudes de permisos" class="rounded-md shadow">
                            <div class="mt-6">
                                <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border bg-purple-100 border-purple-200 text-purple-500 dark:bg-purple-500/20 dark:border-purple-500/20">
                                    Solicitud de Permisos
                                </span>
                                <h6 class="mt-3 mb-2 text-lg truncate">
                                    <a href="#!">Administra y Solicita tus Permisos</a>
                                </h6>
                                <p class="mb-3 text-slate-500 dark:text-zink-200 text-16">
                                    Plataforma que permite a los usuarios registrar, gestionar y dar seguimiento a sus solicitudes de permiso dentro del sistema.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </div><!--end col-->
                <div class="xl:col-span-4">
                    <div
                        class="transition-all duration-300 ease-linear xl:-mt-36 card hover:-translate-y-2 dark:bg-zink-600">
                        <div class="p-6">
                            <img src="{{asset('assets/images/personal-3.png')}}" alt="Gestión de vacaciones" class="rounded-md shadow">
                            <div class="mt-6">
                                <span class="px-2.5 py-0.5 text-xs inline-block font-medium rounded border bg-red-100 border-red-200 text-red-500 dark:bg-red-500/20 dark:border-red-500/20">
                                    Gestión de Vacaciones
                                </span>
                                <h6 class="mt-3 mb-2 text-lg truncate">
                                    <a href="#!">Administra y Solicita tus Vacaciones</a>
                                </h6>
                                <p class="mb-3 text-slate-500 dark:text-zink-200 text-16">
                                    Plataforma para que los usuarios puedan solicitar, gestionar y hacer seguimiento de sus periodos de vacaciones de forma eficiente.
                                </p>
                            </div>
                        </div>
                        
                    </div>
                </div><!--end col-->
            </div><!--end grid-->

        </div>
    </section><!--end -->

   <section class="relative py-32" id="about">
    <div class="container 2xl:max-w-[87.5rem] px-4 mx-auto">
        <div class="mx-auto text-center xl:max-w-3xl">
            <h1 class="mb-6 leading-normal capitalize">Departamento de<span
                    class="relative inline-block px-2 mx-2 before:block before:absolute before:-inset-1 before:-skew-y-6 before:bg-sky-50 dark:before:bg-sky-500/20 before:rounded-md before:backdrop-blur-xl">
                    <span class="relative text-sky-500">Personal</span></span></h1>
            <p class="text-lg text-slate-500 dark:text-zink-200">
                El Departamento de Personal de la UATF gestiona los permisos y vacaciones del personal administrativo,
                asegurando procesos claros y eficientes. También promueve su bienestar y desarrollo profesional,
                contribuyendo al buen funcionamiento de la universidad.
            </p>
        </div>

        <!-- Carrusel Swiper -->
        <div class="mt-10">
            <div class="swiper mySwiper rounded-xl overflow-hidden shadow-lg">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="{{ asset('assets/images/uatf.webp') }}" alt="UATF" class="w-full h-full object-cover">
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('assets/images/descarga.jpg') }}" alt="Descarga" class="w-full h-full object-cover">
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('assets/images/personal.jpg') }}" alt="Personal" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- If you want pagination or navigation -->
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
</section>

<!-- Agrega los estilos de Swiper JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Agrega los scripts necesarios -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    // Inicializa Swiper cuando el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        var swiper = new Swiper(".mySwiper", {
            // Configuración del carrusel
            loop: true, // Permite loop infinito
            autoplay: {
                delay: 3000, // Cambia de slide cada 3 segundos
                disableOnInteraction: false, // Continúa el autoplay después de interacción del usuario
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true, // Permite hacer clic en los puntos de paginación
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            // Efecto de transición (puedes cambiar a 'fade', 'cube', 'coverflow', etc.)
            effect: 'slide',
            // Velocidad de transición en ms
            speed: 600,
        });
    });
</script>

<style>
    /* Estilos modificados para el carrusel */
    .swiper {
        width: 100%;
        height: auto;
        max-height: 80vh; /* Opcional: limita la altura máxima */
    }
    
    .swiper-slide {
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f5f5f5; /* Fondo por si la imagen no cubre todo */
    }
    
    .swiper-slide img {
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* Muestra la imagen completa sin recortar */
    }
    
    /* Mantén los demás estilos igual */
    .swiper-button-next, .swiper-button-prev {
        color: white;
        background-color: rgba(0, 0, 0, 0.5);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .swiper-button-next:hover, .swiper-button-prev:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }
    
    .swiper-button-next::after, .swiper-button-prev::after {
        font-size: 1.2rem;
    }
    
    .swiper-pagination-bullet {
        background: white;
        opacity: 0.7;
    }
    
    .swiper-pagination-bullet-active {
        background: #0ea5e9;
        opacity: 1;
    }
</style>




    <section class="relative" id="contact">
        <footer class="relative pt-20 pb-12 bg-slate-800 dark:bg-zink-700">
            <div class="container 2xl:max-w-[87.5rem] px-4 mx-auto">
                <div class="relative z-10 grid grid-cols-12 gap-5 xl:grid-cols-12">
                    <div class="col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-4">
                        <h5 class="mb-4 font-medium tracking-wider text-slate-50 dark:text-zink-50">Enlaces Rápidos</h5>
                        <ul class="flex flex-col gap-3 text-15">
                            <li>
                                <a href="#!"
                                    class="relative inline-block transition-all duration-200 ease-linear text-slate-400 dark:text-zink-200 hover:text-slate-300 dark:hover:text-zink-50 before:absolute before:border-b before:border-slate-500 dark:before:border-zink-500 before:inset-x-0 before:bottom-0 before:w-0 hover:before:w-full before:transition-all before:duration-300 before:ease-linear">U.A.T.F. Página Web</a>
                            </li>
                            <li>
                                <a href="#!"
                                    class="relative inline-block transition-all duration-200 ease-linear text-slate-400 dark:text-zink-200 hover:text-slate-300 dark:hover:text-zink-50 before:absolute before:border-b before:border-slate-500 dark:before:border-zink-500 before:inset-x-0 before:bottom-0 before:w-0 hover:before:w-full before:transition-all before:duration-300 before:ease-linear">U.A.T.F. Portal Académico</a>
                            </li>
                            <li>
                                <a href="#!"
                                    class="relative inline-block transition-all duration-200 ease-linear text-slate-400 dark:text-zink-200 hover:text-slate-300 dark:hover:text-zink-50 before:absolute before:border-b before:border-slate-500 dark:before:border-zink-500 before:inset-x-0 before:bottom-0 before:w-0 hover:before:w-full before:transition-all before:duration-300 before:ease-linear">Convocatorias 2023</a>
                            </li>
                        </ul>
                    </div><!--end col-->
                    <div class="col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-4">
                        <h5 class="mb-4 font-medium tracking-wider text-slate-50 dark:text-zink-50">Contacto</h5>
                        <ul class="flex flex-col gap-3 text-15">
                            <li class="text-slate-400 dark:text-zink-200">
                                Serrudo s/n, esquina Av. Civica
                            </li>
                            <li>
                                <a href="mailto:informaciones@uatf.edu.bo"
                                    class="relative inline-block transition-all duration-200 ease-linear text-slate-400 dark:text-zink-200 hover:text-slate-300 dark:hover:text-zink-50 before:absolute before:border-b before:border-slate-500 dark:before:border-zink-500 before:inset-x-0 before:bottom-0 before:w-0 hover:before:w-full before:transition-all before:duration-300 before:ease-linear">informaciones@uatf.edu.bo</a>
                            </li>
                            <li>
                                <a href="tel:26227300"
                                    class="relative inline-block transition-all duration-200 ease-linear text-slate-400 dark:text-zink-200 hover:text-slate-300 dark:hover:text-zink-50 before:absolute before:border-b before:border-slate-500 dark:before:border-zink-500 before:inset-x-0 before:bottom-0 before:w-0 hover:before:w-full before:transition-all before:duration-300 before:ease-linear">26227300</a>
                            </li>
                            <li>
                                <a href="tel:72377346"
                                    class="relative inline-block transition-all duration-200 ease-linear text-slate-400 dark:text-zink-200 hover:text-slate-300 dark:hover:text-zink-50 before:absolute before:border-b before:border-slate-500 dark:before:border-zink-500 before:inset-x-0 before:bottom-0 before:w-0 hover:before:w-full before:transition-all before:duration-300 before:ease-linear">72377346</a>
                            </li>
                        </ul>
                    </div><!--end col-->
                   <!--end col-->
                    <div class="col-span-12 md:col-span-6 lg:col-span-12 xl:col-span-4">
                        <h5 class="mb-4 font-medium tracking-wider text-slate-50 dark:text-zink-50">UNIVERSIDAD AUTÓNOMA
                            TOMÁS FRÍAS
                            </h5>
                       
                    <img src="{{asset('uatf/FRONTIS-blanco.png')}}" alt="">
                        
                    </div>
                </div><!--end grid-->

                <div class="mt-12 text-center text-slate-400 dark:text-zink-200 text-16">
                    <p>
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © Universidad Autónoma "Tomás Frías". Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </footer>
    </section>

    <button id="back-to-top"
        class="fixed flex items-center justify-center w-10 h-10 text-white bg-purple-500 rounded-md bottom-10 right-10">
        <i data-lucide="chevron-up" class="animate animate-icons"></i>
    </button>

    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/tippy.js/tippy-bundle.umd.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/prismjs/prism.js') }}"></script>
    <script src="{{ asset('assets/libs/lucide/umd/lucide.js') }}"></script>
    <script src="{{ asset('assets/js/StarCode.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/pages/landing-onepage.init.js') }}"></script>

</body>

</html>
