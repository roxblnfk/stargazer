<!DOCTYPE html>
<html lang="@{locale|en}">
<head>
    <title>${title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #3C4964;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
    </style>
    <block:head>
        <stack:collect name="styles" level="2"/>
    </block:head>
</head>
<body class="d-flex flex-column min-vh-100">
<header class="py-3">
    <!-- Header content if needed -->
</header>

<main class="flex-grow-1">
    <div class="container-fluid">
        <block:body/>
    </div>
</main>

<footer class="py-3 text-center">
    <!-- Footer content if needed -->
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<stack:collect name="scripts" level="1"/>
</body>
<hidden>${context}</hidden>
</html>
