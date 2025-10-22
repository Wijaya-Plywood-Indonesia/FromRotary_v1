<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>PT Plywood Indonesia</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
        />

        <style>
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            }

            /* Hero Section */
            .hero {
                background: url("https://via.placeholder.com/1920x800")
                    center/cover no-repeat;
                height: 90vh;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
            }

            .hero h1 {
                font-size: 3rem;
                font-weight: 700;
            }

            .hero p {
                font-size: 1.2rem;
            }

            /* Produk */
            .product-card img {
                height: 200px;
                object-fit: cover;
            }

            /* Tim */
            .team img {
                border-radius: 50%;
                width: 120px;
                height: 120px;
                object-fit: cover;
            }

            footer {
                background-color: #0d6efd;
                color: white;
                padding: 30px 0;
            }
        </style>
    </head>

    <body>
        <!-- Navbar -->
        <nav
            class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top"
        >
            <div class="container">
                <a class="navbar-brand fw-bold text-primary" href="#"
                    >PT. Plywood Indonesia</a
                >
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div
                    class="collapse navbar-collapse justify-content-end"
                    id="navbarNav"
                >
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="#profil">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#produk">Produk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tim">Tim</a>
                        </li>
                        <li class="nav-item">
                            <a
                                href="{{ route('filament.admin.auth.login') }}"
                                class="btn btn-primary ms-3"
                                >Login</a
                            >
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero text-center">
            <div>
                <h1>Solusi Plywood Berkualitas Tinggi</h1>
                <p class="mt-3 mb-4">
                    Mendukung kebutuhan industri dan konstruksi Anda dengan
                    produk plywood terbaik.
                </p>
                <a href="#produk" class="btn btn-light btn-lg">Lihat Produk</a>
            </div>
        </section>

        <!-- Profil Perusahaan -->
        <section id="profil" class="py-5 bg-light">
            <div class="container text-center">
                <h2 class="fw-bold mb-4">Tentang Kami</h2>
                <p class="text-muted mb-5">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Proin sed lorem et velit tincidunt luctus. Curabitur
                    ultrices massa vel lorem condimentum, vel gravida enim
                    gravida. Donec euismod libero nec eros fermentum, et dictum
                    ex eleifend.
                </p>

                <div class="row g-4">
                    <div class="col-md-4">
                        <img
                            src="https://via.placeholder.com/400x300"
                            class="img-fluid rounded shadow"
                            alt="Kantor"
                        />
                    </div>
                    <div class="col-md-4">
                        <img
                            src="https://via.placeholder.com/400x300"
                            class="img-fluid rounded shadow"
                            alt="Produksi"
                        />
                    </div>
                    <div class="col-md-4">
                        <img
                            src="https://via.placeholder.com/400x300"
                            class="img-fluid rounded shadow"
                            alt="Gudang"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Produk -->
        <section id="produk" class="py-5">
            <div class="container text-center">
                <h2 class="fw-bold mb-4">Produk Unggulan Kami</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card product-card shadow-sm">
                            <img
                                src="https://via.placeholder.com/400x200"
                                class="card-img-top"
                                alt="Plywood 1"
                            />
                            <div class="card-body">
                                <h5 class="card-title">Plywood Premium</h5>
                                <p class="card-text text-muted">
                                    Lorem ipsum dolor sit amet, consectetur
                                    adipiscing elit.
                                </p>
                                <a href="#" class="btn btn-primary"
                                    >Beli Sekarang</a
                                >
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card product-card shadow-sm">
                            <img
                                src="https://via.placeholder.com/400x200"
                                class="card-img-top"
                                alt="Plywood 2"
                            />
                            <div class="card-body">
                                <h5 class="card-title">Marine Plywood</h5>
                                <p class="card-text text-muted">
                                    Lorem ipsum dolor sit amet, consectetur
                                    adipiscing elit.
                                </p>
                                <a href="#" class="btn btn-primary"
                                    >Beli Sekarang</a
                                >
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card product-card shadow-sm">
                            <img
                                src="https://via.placeholder.com/400x200"
                                class="card-img-top"
                                alt="Plywood 3"
                            />
                            <div class="card-body">
                                <h5 class="card-title">Decorative Plywood</h5>
                                <p class="card-text text-muted">
                                    Lorem ipsum dolor sit amet, consectetur
                                    adipiscing elit.
                                </p>
                                <a href="#" class="btn btn-primary"
                                    >Beli Sekarang</a
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tim Profesional -->
        <section id="tim" class="py-5 bg-light">
            <div class="container text-center">
                <h2 class="fw-bold mb-5">Tim Profesional Kami</h2>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-3">
                        <div class="team">
                            <img
                                src="https://via.placeholder.com/150"
                                alt="CEO"
                            />
                            <h5 class="mt-3 mb-1">Andi Wijaya</h5>
                            <p class="text-muted">CEO & Founder</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="team">
                            <img
                                src="https://via.placeholder.com/150"
                                alt="Manager"
                            />
                            <h5 class="mt-3 mb-1">Sinta Prameswari</h5>
                            <p class="text-muted">Marketing Manager</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="team">
                            <img
                                src="https://via.placeholder.com/150"
                                alt="Engineer"
                            />
                            <h5 class="mt-3 mb-1">Budi Santoso</h5>
                            <p class="text-muted">Production Engineer</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="text-center">
            <div class="container">
                <p class="mb-0">
                    &copy; 2025 PT Plywood Indonesia. All rights reserved.
                </p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
