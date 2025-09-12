<extends:layout title="[[Stargazer - Open Source Stars Analytics]]"/>

<stack:push name="styles">
<style>
    :root {
        --gradient-from: #0f172a;
        --gradient-to: #1e293b;
        --card-bg: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --accent-primary: #475569;
        --accent-hover: #334155;
        --border-light: #e2e8f0;
    }

    body {
        background: var(--gradient-from) !important;
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
    }

    .hero-pattern {
        background-image:
            radial-gradient(circle at 25px 25px, rgba(255,255,255,0.1) 2px, transparent 0),
            radial-gradient(circle at 75px 75px, rgba(255,255,255,0.1) 2px, transparent 0);
        background-size: 100px 100px;
    }

    .main-card {
        background: var(--card-bg);
        border-radius: 1.5rem;
        box-shadow:
            0 25px 50px -12px rgba(0, 0, 0, 0.25),
            0 0 0 1px rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .title-text {
        color: var(--text-primary);
    }

    .feature-card {
        background: #f8fafc;
        border-radius: 0.5rem;
        padding: 0.75rem;
        border: 1px solid var(--border-light);
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .feature-icon {
        width: 1.8rem;
        height: 1.8rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        background: var(--accent-primary);
        color: white;
    }

    .btn-primary-solid {
        background: var(--accent-primary);
        border: none;
        border-radius: 0.75rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px 0 rgba(71, 85, 105, 0.2);
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary-solid:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px 0 rgba(71, 85, 105, 0.3);
        background: var(--accent-hover);
        color: white;
    }

    .input-modern {
        background: white;
        border: 1px solid var(--border-light);
        border-radius: 0.75rem;
        transition: all 0.3s ease;
        height: 58px;
        color: var(--text-primary);
    }

    .input-modern:focus {
        background: white;
        border-color: var(--accent-primary);
        box-shadow: 0 0 0 3px rgba(71, 85, 105, 0.1);
        outline: none;
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .text-white-80 {
        color: rgba(255, 255, 255, 0.8);
    }

    .text-white-60 {
        color: rgba(255, 255, 255, 0.6);
    }
</style>
</stack:push>

<define:body>
    <div class="min-vh-100 hero-pattern position-relative">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <!-- Main Card -->
                    <div class="main-card p-4">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <div class="me-2">
                                    <i class="bi bi-star-fill" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                                </div>
                                <h1 class="display-5 fw-bold title-text mb-0">Stargazer</h1>
                                <div class="ms-2">
                                    <i class="bi bi-star-fill" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                                </div>
                            </div>
                            <p class="text-secondary mb-0 mx-auto" style="max-width: 500px; font-size: 1.1rem;">
                                Мы создаем открытый исходный код и очень рассчитываем на ваши звезды!
                                Оцените наши проекты, а за это мы подарим вам мерч.
                            </p>
                        </div>

                        <!-- Features Grid -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="feature-card text-center">
                                    <div class="feature-icon mx-auto">
                                        <i class="bi bi-github"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-1" style="color: var(--text-primary); font-size: 0.875rem;">Open Source</h6>
                                    <p class="text-secondary mb-0" style="font-size: 0.75rem; line-height: 1.2;">Поддерживаем открытые проекты и сообщество</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-card text-center">
                                    <div class="feature-icon mx-auto">
                                        <i class="bi bi-star"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-1" style="color: var(--text-primary); font-size: 0.875rem;">Звезды важны</h6>
                                    <p class="text-secondary mb-0" style="font-size: 0.75rem; line-height: 1.2;">Каждая звезда помогает проектам расти</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-card text-center">
                                    <div class="feature-icon mx-auto">
                                        <i class="bi bi-gift"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-1" style="color: var(--text-primary); font-size: 0.875rem;">Мерч в подарок</h6>
                                    <p class="text-secondary mb-0" style="font-size: 0.75rem; line-height: 1.2;">Получайте призы за поддержку проектов</p>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Section -->
                        <div class="text-center">
                            <h2 class="h5 fw-bold mb-3" style="color: var(--text-primary);">Начните прямо сейчас!</h2>
                            <p class="text-secondary mb-3" style="font-size: 0.95rem;">Введите свой GitHub username и узнайте, какие проекты вы поддержали</p>

                            <form class="mb-4">
                                <div class="row justify-content-center">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <div class="flex-grow-1">
                                                <input type="text"
                                                       class="form-control input-modern form-control-lg"
                                                       placeholder="ваш-github-username">
                                            </div>
                                            <button class="btn btn-primary-solid px-4" type="submit">
                                                Поехали!
                                                <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Bottom Section -->
                    <div class="text-center mt-4">
                        <p class="text-white-60 small mb-0">
                            <i class="bi bi-heart-fill text-danger me-2"></i>
                            Сделано с любовью для open source сообщества
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
