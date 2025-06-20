<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFahrizaldi | Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
    <style>
        :root {
            --primary: #3b82f6;
            --secondary: #10b981;
            --dark: #0f172a;
            --light: #f8fafc;
            --accent: #8b5cf6;
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            overflow-x: hidden;
        }

        .gradient-text {
            background: linear-gradient(90deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .gradient-border {
            position: relative;
            border-radius: 0.5rem;
            background: var(--dark);
            padding: 0.25rem;
        }

        .gradient-border::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 0.5rem;
            padding: 2px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .card {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
        }

        .project-card {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .project-card img {
            transition: transform 0.5s ease;
        }

        .project-card:hover img {
            transform: scale(1.05);
        }

        .project-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.9), transparent);
            padding: 1.5rem;
            transform: translateY(20%);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .project-card:hover .project-overlay {
            transform: translateY(0);
            opacity: 1;
        }

        .animated-button {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .animated-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .animated-button:hover::before {
            opacity: 1;
        }

        .animated-button:hover {
            color: white;
            border-color: transparent;
        }

        .cursor {
            position: fixed;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: var(--primary);
            pointer-events: none;
            mix-blend-mode: difference;
            z-index: 9999;
            transform: translate(-50%, -50%);
            transition: transform 0.1s ease;
        }

        .cursor-follower {
            position: fixed;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--accent);
            pointer-events: none;
            z-index: 9998;
            transform: translate(-50%, -50%);
            transition: transform 0.3s ease, width 0.3s ease, height 0.3s ease;
        }

        .interactive:hover~.cursor {
            transform: translate(-50%, -50%) scale(1.5);
        }

        .interactive:hover~.cursor-follower {
            transform: translate(-50%, -50%) scale(0.8);
        }

        #hero {
            position: relative;
            height: 100vh;
            width: 100%;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0) translateX(-50%);
            }

            40% {
                transform: translateY(-20px) translateX(-50%);
            }

            60% {
                transform: translateY(-10px) translateX(-50%);
            }
        }

        .skill-pill {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .skill-pill:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
        }

        .timeline-item {
            position: relative;
            padding-left: 2rem;
            padding-bottom: 2rem;
            border-left: 2px solid rgba(59, 130, 246, 0.3);
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 0;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: var(--primary);
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
        }

        .contact-form input,
        .contact-form textarea {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light);
            transition: all 0.3s ease;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            outline: none;
        }

        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .loader-text {
            margin-top: 1rem;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.1em;
        }

        .loader-bar {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
            border-radius: 2px;
        }

        .loader-progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .nav-link {
            position: relative;
            padding: 0.5rem 0;
            margin: 0 1rem;
            color: var(--light);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            height: 100vh;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            z-index: 999;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .mobile-menu.active {
            right: 0;
        }

        .menu-toggle {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 2.5rem;
            height: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            z-index: 1000;
        }

        .menu-toggle span {
            width: 100%;
            height: 2px;
            background-color: var(--light);
            transition: all 0.3s ease;
        }

        .menu-toggle.active span:nth-child(1) {
            transform: translateY(9px) rotate(45deg);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: translateY(-9px) rotate(-45deg);
        }

        @media (max-width: 768px) {
            .desktop-nav {
                display: none;
            }

            .menu-toggle {
                display: flex;
            }
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }

            .mobile-menu {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Loader -->
    <div class="loader">
        <div class="loader-content">
            <div class="text-4xl font-bold gradient-text">MFahrizaldi</div>
            <div class="loader-text">LOADING</div>
            <div class="loader-bar">
                <div class="loader-progress"></div>
            </div>
        </div>
    </div>

    <!-- Custom Cursor -->
    <div class="cursor hidden md:block"></div>
    <div class="cursor-follower hidden md:block"></div>

    <!-- Navigation -->
    <header class="fixed top-0 left-0 w-full py-6 z-50">
        <div class="container mx-auto px-6">
            <nav class="flex justify-between items-center">
                <a href="#" class="text-2xl font-bold gradient-text interactive">MFahrizaldi</a>

                <div class="desktop-nav">
                    <ul class="flex">
                        <li><a href="#hero" class="nav-link interactive active">Home</a></li>
                        <li><a href="#about" class="nav-link interactive">About</a></li>
                        <li><a href="#skills" class="nav-link interactive">Skills</a></li>
                        <li><a href="#experience" class="nav-link interactive">Experience</a></li>
                        <li><a href="#projects" class="nav-link interactive">Projects</a></li>
                        <li><a href="#contact" class="nav-link interactive">Contact</a></li>
                    </ul>
                </div>

                <div class="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <ul class="flex flex-col items-center space-y-6">
            <li><a href="#hero" class="text-xl nav-link mobile-link">Home</a></li>
            <li><a href="#about" class="text-xl nav-link mobile-link">About</a></li>
            <li><a href="#skills" class="text-xl nav-link mobile-link">Skills</a></li>
            <li><a href="#experience" class="text-xl nav-link mobile-link">Experience</a></li>
            <li><a href="#projects" class="text-xl nav-link mobile-link">Projects</a></li>
            <li><a href="#contact" class="text-xl nav-link mobile-link">Contact</a></li>
        </ul>
    </div>

    <!-- Hero Section -->
    <section id="hero" class="relative flex items-center justify-center">
        <div id="vanta-bg"></div>
        <div class="container mx-auto px-6 z-10">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4 hero-title">
                        Hi, I'm <span class="gradient-text">MFahrizaldi</span>
                    </h1>
                    <h2 class="text-2xl md:text-3xl mb-6 hero-subtitle opacity-0">
                        Web Developer & IT Support
                    </h2>
                    <p class="text-gray-300 mb-8 max-w-lg hero-text opacity-0">
                        I create stunning web experiences with modern technologies.
                        Specializing in Laravel, CodeIgniter, and WordPress development
                        with a focus on beautiful, functional design.
                    </p>
                    <div class="flex space-x-4 hero-buttons opacity-0">
                        <a href="#contact"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 interactive">
                            Get in Touch
                        </a>
                        <a href="#projects"
                            class="px-6 py-3 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition duration-300 interactive">
                            View Projects
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <div class="relative w-64 h-64 md:w-80 md:h-80 hero-image opacity-0">
                        <svg class="floating" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#3B82F6"
                                d="M40.8,-68.7C51.9,-61.5,59.5,-48.4,65.6,-35C71.7,-21.6,76.3,-7.9,74.8,5.1C73.3,18.1,65.8,30.3,56.7,40.5C47.6,50.7,36.9,58.8,24.8,63.9C12.7,69,0.2,71,-12.4,69.7C-25,68.4,-37.6,63.8,-47.4,55.3C-57.1,46.8,-64,34.4,-68.9,21C-73.8,7.6,-76.8,-6.8,-73.3,-19.8C-69.8,-32.8,-59.8,-44.4,-47.7,-51.5C-35.6,-58.6,-21.4,-61.2,-6.9,-61.9C7.7,-62.6,29.7,-75.9,40.8,-68.7Z"
                                transform="translate(100 100)" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center section-title">
                About <span class="gradient-text">Me</span>
            </h2>
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <div class="gradient-border p-1 rounded-lg about-image">
                        <div class="bg-gradient-to-br from-blue-600/20 to-purple-600/20 rounded-lg p-8">
                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                                <path fill="#3B82F6"
                                    d="M47.7,-57.2C59.5,-47.3,65.5,-30.9,68.5,-14.2C71.4,2.6,71.3,19.6,64.1,32.6C56.9,45.6,42.8,54.5,28.1,59.8C13.5,65,-1.7,66.6,-18.4,64.4C-35.2,62.3,-53.6,56.3,-65.8,43.9C-78,31.4,-84,12.4,-81.9,-5.2C-79.8,-22.9,-69.6,-39.2,-55.8,-49.2C-42,-59.3,-24.5,-63,-6.9,-60.9C10.7,-58.8,35.9,-67.1,47.7,-57.2Z"
                                    transform="translate(100 100)" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 md:pl-10">
                    <h3 class="text-2xl font-bold mb-4 about-title">Web Developer & IT Support</h3>
                    <p class="text-gray-300 mb-6 about-text">
                        I'm a passionate Web Developer with expertise in developing websites using WordPress,
                        CodeIgniter, and Laravel, as well as managing databases like MySQL and Elasticsearch.
                        I have extensive experience in designing information systems for various industries,
                        including restaurants, farms, and bird's nest exports.
                    </p>
                    <p class="text-gray-300 mb-6 about-text">
                        Proficient in network configuration using Mikrotik, internet installation (including
                        fiber optics and Starlink), and system administration. As a Music Producer, I'm skilled
                        in creating electronic music using FL Studio, playing piano, and drumpad.
                    </p>
                    <div class="grid grid-cols-2 gap-4 mb-6 about-info">
                        <div>
                            <p class="text-gray-400">Name</p>
                            <p class="font-medium">MFahrizaldi</p>
                        </div>
                        <div>
                            <p class="text-gray-400">Email</p>
                            <p class="font-medium">aldimf26@gmail.com</p>
                        </div>
                        <div>
                            <p class="text-gray-400">Phone</p>
                            <p class="font-medium">(+62) 895-413111-053</p>
                        </div>
                        <div>
                            <p class="text-gray-400">Location</p>
                            <p class="font-medium">Banjarmasin, Indonesia</p>
                        </div>
                    </div>
                    <div class="flex space-x-4 about-social">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition duration-300 interactive">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-400 transition duration-300 interactive">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-700 transition duration-300 interactive">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-600 transition duration-300 interactive">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="py-20 bg-gray-900">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center section-title">
                My <span class="gradient-text">Skills</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="card p-6 rounded-lg skills-card">
                    <div class="w-16 h-16 bg-blue-600/20 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Programming</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">PHP</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Laravel</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">CodeIgniter</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">WordPress</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">HTML</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">CSS</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">JavaScript</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">C++</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Delphi</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Java</span>
                    </div>
                </div>

                <div class="card p-6 rounded-lg skills-card">
                    <div class="w-16 h-16 bg-green-600/20 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Database</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">MySQL</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Elasticsearch</span>
                    </div>
                </div>

                <div class="card p-6 rounded-lg skills-card">
                    <div class="w-16 h-16 bg-purple-600/20 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Networking</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Mikrotik</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Fiber Optic</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Starlink</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Proxy</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Local IP</span>
                    </div>
                </div>

                <div class="card p-6 rounded-lg skills-card">
                    <div class="w-16 h-16 bg-yellow-600/20 rounded-lg flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Music Production</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">FL Studio</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Piano</span>
                        <span class="skill-pill px-3 py-1 rounded-full text-sm">Drumpad</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experience Section -->
    <section id="experience" class="py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center section-title">
                Work <span class="gradient-text">Experience</span>
            </h2>
            <div class="max-w-3xl mx-auto">
                <div class="timeline-item experience-item">
                    <div class="mb-4">
                        <h3 class="text-xl font-semibold">Web Developer, IT Support, Programmer</h3>
                        <div class="flex items-center text-gray-400">
                            <span>PT Agrika Gatya Arum, CV Agrilaras, Restoran Takemori & Soondobu</span>
                        </div>
                        <p class="text-blue-500">2022 - Present</p>
                    </div>
                    <ul class="list-disc list-inside text-gray-300 space-y-2">
                        <li>Developed bird's nest sales system using Laravel 8 and MySQL, including payroll system based
                            on division (plucking, printing, sorting) with weight and shrinkage reporting.</li>
                        <li>Designed cashier application for restaurants using Laravel, including features for recording
                            cash advances, fines, server points, kitchen system, payroll, and sales reports.</li>
                        <li>Created information system for chicken farm at CV Agrilaras using Laravel and MySQL,
                            including calculation of chicken numbers, feed, eggs, and accounting for profit and loss
                            reports, balance sheets, and journals.</li>
                        <li>Managed network installation and configuration using Mikrotik for internet distribution
                            throughout office floors, with IndiHome as the main server.</li>
                        <li>Installed internet infrastructure in remote locations (forest) for CV Agrilaras, including
                            fiber optic cables, LHG antennas, and Starlink configuration.</li>
                        <li>Performed database and system maintenance to ensure fast and secure operations.</li>
                    </ul>
                </div>

                <div class="timeline-item experience-item">
                    <div class="mb-4">
                        <h3 class="text-xl font-semibold">Web Developer</h3>
                        <div class="flex items-center text-gray-400">
                            <span>Rukita.id</span>
                        </div>
                        <p class="text-blue-500">January 2020 - September 2020</p>
                    </div>
                    <ul class="list-disc list-inside text-gray-300 space-y-2">
                        <li>Developed e-commerce website using WordPress.</li>
                        <li>Built website using CodeIgniter 3 with Elasticsearch database for fast and efficient
                            searching.</li>
                    </ul>
                </div>

                <div class="timeline-item experience-item">
                    <div class="mb-4">
                        <h3 class="text-xl font-semibold">Education</h3>
                        <div class="flex items-center text-gray-400">
                            <span>Universitas Islam Kalimantan Muhammad Arsyad Al Banjari</span>
                        </div>
                        <p class="text-blue-500">2017 - 2021</p>
                    </div>
                    <ul class="list-disc list-inside text-gray-300 space-y-2">
                        <li>Bachelor of Information Systems</li>
                        <li>GPA: 3.60/4.00</li>
                        <li>Studied information systems management, basic programming (C++, Delphi, Java), and website
                            development (HTML, CSS, JavaScript, PHP, MySQL).</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="py-20 bg-gray-900">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center section-title">
                My <span class="gradient-text">Projects</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="project-card">
                    <div class="relative h-64 bg-blue-900 rounded-lg overflow-hidden">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
                            class="absolute inset-0 w-full h-full">
                            <path fill="#3B82F6"
                                d="M47.7,-57.2C59.5,-47.3,65.5,-30.9,68.5,-14.2C71.4,2.6,71.3,19.6,64.1,32.6C56.9,45.6,42.8,54.5,28.1,59.8C13.5,65,-1.7,66.6,-18.4,64.4C-35.2,62.3,-53.6,56.3,-65.8,43.9C-78,31.4,-84,12.4,-81.9,-5.2C-79.8,-22.9,-69.6,-39.2,-55.8,-49.2C-42,-59.3,-24.5,-63,-6.9,-60.9C10.7,-58.8,35.9,-67.1,47.7,-57.2Z"
                                transform="translate(100 100)" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                        </div>
                    </div>
                    <div class="project-overlay">
                        <h3 class="text-xl font-semibold mb-2">Circle English Course</h3>
                        <p class="text-gray-300 mb-4">Application for English language learning registration in Muara
                            Teweh.</p>
                        <a href="https://circleenglishcourse.com" target="_blank"
                            class="text-blue-400 hover:text-blue-300 inline-flex items-center interactive">
                            Visit Website
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="project-card">
                    <div class="relative h-64 bg-green-900 rounded-lg overflow-hidden">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
                            class="absolute inset-0 w-full h-full">
                            <path fill="#10B981"
                                d="M40.8,-68.7C51.9,-61.5,59.5,-48.4,65.6,-35C71.7,-21.6,76.3,-7.9,74.8,5.1C73.3,18.1,65.8,30.3,56.7,40.5C47.6,50.7,36.9,58.8,24.8,63.9C12.7,69,0.2,71,-12.4,69.7C-25,68.4,-37.6,63.8,-47.4,55.3C-57.1,46.8,-64,34.4,-68.9,21C-73.8,7.6,-76.8,-6.8,-73.3,-19.8C-69.8,-32.8,-59.8,-44.4,-47.7,-51.5C-35.6,-58.6,-21.4,-61.2,-6.9,-61.9C7.7,-62.6,29.7,-75.9,40.8,-68.7Z"
                                transform="translate(100 100)" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="project-overlay">
                        <h3 class="text-xl font-semibold mb-2">Kasir OK</h3>
                        <p class="text-gray-300 mb-4">POS system for SMEs with features for stock management, products,
                            and cashier system.</p>
                        <a href="https://alditeori.vercel.app" target="_blank"
                            class="text-blue-400 hover:text-blue-300 inline-flex items-center interactive">
                            Visit Website
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="project-card">
                    <div class="relative h-64 bg-purple-900 rounded-lg overflow-hidden">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
                            class="absolute inset-0 w-full h-full">
                            <path fill="#8B5CF6"
                                d="M47.7,-57.2C59.5,-47.3,65.5,-30.9,68.5,-14.2C71.4,2.6,71.3,19.6,64.1,32.6C56.9,45.6,42.8,54.5,28.1,59.8C13.5,65,-1.7,66.6,-18.4,64.4C-35.2,62.3,-53.6,56.3,-65.8,43.9C-78,31.4,-84,12.4,-81.9,-5.2C-79.8,-22.9,-69.6,-39.2,-55.8,-49.2C-42,-59.3,-24.5,-63,-6.9,-60.9C10.7,-58.8,35.9,-67.1,47.7,-57.2Z"
                                transform="translate(100 100)" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="project-overlay">
                        <h3 class="text-xl font-semibold mb-2">Catat Duit</h3>
                        <p class="text-gray-300 mb-4">Website for recording money transactions in and out simply and
                            easily.</p>
                        <a href="https://alditeori.vercel.app" target="_blank"
                            class="text-blue-400 hover:text-blue-300 inline-flex items-center interactive">
                            Visit Website
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="project-card">
                    <div class="relative h-64 bg-yellow-900 rounded-lg overflow-hidden">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
                            class="absolute inset-0 w-full h-full">
                            <path fill="#FBBF24"
                                d="M40.8,-68.7C51.9,-61.5,59.5,-48.4,65.6,-35C71.7,-21.6,76.3,-7.9,74.8,5.1C73.3,18.1,65.8,30.3,56.7,40.5C47.6,50.7,36.9,58.8,24.8,63.9C12.7,69,0.2,71,-12.4,69.7C-25,68.4,-37.6,63.8,-47.4,55.3C-57.1,46.8,-64,34.4,-68.9,21C-73.8,7.6,-76.8,-6.8,-73.3,-19.8C-69.8,-32.8,-59.8,-44.4,-47.7,-51.5C-35.6,-58.6,-21.4,-61.2,-6.9,-61.9C7.7,-62.6,29.7,-75.9,40.8,-68.7Z"
                                transform="translate(100 100)" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                        </div>
                    </div>
                    <div class="project-overlay">
                        <h3 class="text-xl font-semibold mb-2">Terjemahan Dayak</h3>
                        <p class="text-gray-300 mb-4">Website for translating Dayak language to Indonesian or vice
                            versa.</p>
                        <a href="https://alditeori.vercel.app" target="_blank"
                            class="text-blue-400 hover:text-blue-300 inline-flex items-center interactive">
                            Visit Website
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="project-card">
                    <div class="relative h-64 bg-red-900 rounded-lg overflow-hidden">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
                            class="absolute inset-0 w-full h-full">
                            <path fill="#EF4444"
                                d="M47.7,-57.2C59.5,-47.3,65.5,-30.9,68.5,-14.2C71.4,2.6,71.3,19.6,64.1,32.6C56.9,45.6,42.8,54.5,28.1,59.8C13.5,65,-1.7,66.6,-18.4,64.4C-35.2,62.3,-53.6,56.3,-65.8,43.9C-78,31.4,-84,12.4,-81.9,-5.2C-79.8,-22.9,-69.6,-39.2,-55.8,-49.2C-42,-59.3,-24.5,-63,-6.9,-60.9C10.7,-58.8,35.9,-67.1,47.7,-57.2Z"
                                transform="translate(100 100)" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <div class="project-overlay">
                        <h3 class="text-xl font-semibold mb-2">Aldi Teori</h3>
                        <p class="text-gray-300 mb-4">Website for several common theories that can be searched and will
                            be developed further.</p>
                        <a href="https://alditeori.vercel.app" target="_blank"
                            class="text-blue-400 hover:text-blue-300 inline-flex items-center interactive">
                            Visit Website
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="project-card">
                    <div class="relative h-64 bg-indigo-900 rounded-lg overflow-hidden">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
                            class="absolute inset-0 w-full h-full">
                            <path fill="#6366F1"
                                d="M40.8,-68.7C51.9,-61.5,59.5,-48.4,65.6,-35C71.7,-21.6,76.3,-7.9,74.8,5.1C73.3,18.1,65.8,30.3,56.7,40.5C47.6,50.7,36.9,58.8,24.8,63.9C12.7,69,0.2,71,-12.4,69.7C-25,68.4,-37.6,63.8,-47.4,55.3C-57.1,46.8,-64,34.4,-68.9,21C-73.8,7.6,-76.8,-6.8,-73.3,-19.8C-69.8,-32.8,-59.8,-44.4,-47.7,-51.5C-35.6,-58.6,-21.4,-61.2,-6.9,-61.9C7.7,-62.6,29.7,-75.9,40.8,-68.7Z"
                                transform="translate(100 100)" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="project-overlay">
                        <h3 class="text-xl font-semibold mb-2">Bird's Nest Sales System</h3>
                        <p class="text-gray-300 mb-4">System for managing bird's nest sales, including payroll and
                            reporting features.</p>
                        <a href="#"
                            class="text-blue-400 hover:text-blue-300 inline-flex items-center interactive">
                            Private Project
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center section-title">
                Get In <span class="gradient-text">Touch</span>
            </h2>
            <div class="flex flex-col md:flex-row gap-10">
                <div class="md:w-1/2">
                    <div class="card p-8 rounded-lg contact-info">
                        <h3 class="text-2xl font-semibold mb-6">Contact Information</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Email</p>
                                    <p class="font-medium">aldimf26@gmail.com</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Phone</p>
                                    <p class="font-medium">(+62) 895-413111-053</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Location</p>
                                    <p class="font-medium">Banjarmasin, Indonesia</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8">
                            <h4 class="text-lg font-medium mb-4">Follow Me</h4>
                            <div class="flex space-x-4">
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition duration-300 interactive">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-400 transition duration-300 interactive">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-700 transition duration-300 interactive">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-600 transition duration-300 interactive">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <form class="card p-8 rounded-lg contact-form">
                        <h3 class="text-2xl font-semibold mb-6">Send Me a Message</h3>
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Your
                                Name</label>
                            <input type="text" id="name" class="w-full px-4 py-3 rounded-lg"
                                placeholder="Enter your name">
                        </div>
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-400 mb-2">Your
                                Email</label>
                            <input type="email" id="email" class="w-full px-4 py-3 rounded-lg"
                                placeholder="Enter your email">
                        </div>
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-400 mb-2">Subject</label>
                            <input type="text" id="subject" class="w-full px-4 py-3 rounded-lg"
                                placeholder="Enter subject">
                        </div>
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-400 mb-2">Message</label>
                            <textarea id="message" rows="5" class="w-full px-4 py-3 rounded-lg" placeholder="Enter your message"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 interactive">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-10 bg-gray-900">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <a href="#" class="text-2xl font-bold gradient-text">MFahrizaldi</a>
                    <p class="text-gray-400 mt-2">Web Developer & IT Support</p>
                </div>
                <div class="flex flex-col items-center md:items-end">
                    <p class="text-gray-400">© 2024 MFahrizaldi. All rights reserved.</p>
                    <p class="text-gray-500 text-sm mt-1">Designed with ❤️ using modern web technologies</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize VANTA.NET background
        VANTA.NET({
            el: "#vanta-bg",
            mouseControls: true,
            touchControls: true,
            gyroControls: false,
            minHeight: 200.00,
            minWidth: 200.00,
            scale: 1.00,
            scaleMobile: 1.00,
            color: 0x3b82f6,
            backgroundColor: 0x0f172a,
            points: 10.00,
            maxDistance: 20.00,
            spacing: 15.00
        });

        // Loader Animation
        window.addEventListener('load', () => {
            const loader = document.querySelector('.loader');
            const loaderProgress = document.querySelector('.loader-progress');

            gsap.to(loaderProgress, {
                width: '100%',
                duration: 1.5,
                ease: 'power2.inOut',
                onComplete: () => {
                    gsap.to(loader, {
                        opacity: 0,
                        duration: 0.5,
                        onComplete: () => {
                            loader.style.display = 'none';
                        }
                    });
                }
            });
        });

        // Custom Cursor
        const cursor = document.querySelector('.cursor');
        const follower = document.querySelector('.cursor-follower');

        document.addEventListener('mousemove', (e) => {
            gsap.to(cursor, {
                x: e.clientX,
                y: e.clientY,
                duration: 0.1
            });
            gsap.to(follower, {
                x: e.clientX,
                y: e.clientY,
                duration: 0.3
            });
        });

        // GSAP Animations
        gsap.registerPlugin(ScrollTrigger);

        // Hero Section Animations
        gsap.from('.hero-title', {
            y: 50,
            opacity: 0,
            duration: 1,
            delay: 0.5,
            ease: 'power3.out'
        });

        gsap.from('.hero-subtitle', {
            y: 50,
            opacity: 0,
            duration: 1,
            delay: 0.7,
            ease: 'power3.out'
        });

        gsap.from('.hero-text', {
            y: 50,
            opacity: 0,
            duration: 1,
            delay: 0.9,
            ease: 'power3.out'
        });

        gsap.from('.hero-buttons', {
            y: 50,
            opacity: 0,
            duration: 1,
            delay: 1.1,
            ease: 'power3.out'
        });

        gsap.from('.hero-image', {
            scale: 0.8,
            opacity: 0,
            duration: 1,
            delay: 1.3,
            ease: 'power3.out'
        });

        // Section Title Animations
        document.querySelectorAll('.section-title').forEach(title => {
            gsap.from(title, {
                scrollTrigger: {
                    trigger: title,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                y: 50,
                opacity: 0,
                duration: 1,
                ease: 'power3.out'
            });
        });

        // Card Animations
        document.querySelectorAll('.card, .project-card, .experience-item, .contact-info, .contact-form').forEach((card,
            index) => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                y: 50,
                opacity: 0,
                duration: 1,
                delay: index * 0.1,
                ease: 'power3.out'
            });
        });

        // Mobile Menu Toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });

        // Smooth Scrolling
        document.querySelectorAll('.nav-link, .mobile-link').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });

                // Update active nav link
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Active Navigation Link on Scroll
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-link');

            let currentSection = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                if (window.scrollY >= sectionTop) {
                    currentSection = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${currentSection}`) {
                    link.classList.add('active');
                }
            });
        });

        // Form Submission (Basic Example)
        const contactForm = document.querySelector('.contact-form');
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;

            // Here you would typically send the form data to a server
            // This is just a console log for demonstration
            console.log('Form Submitted:', {
                name,
                email,
                subject,
                message
            });

            // Show success message (you can replace this with actual form submission logic)
            alert('Message sent successfully!');
            contactForm.reset();
        });
    </script>
