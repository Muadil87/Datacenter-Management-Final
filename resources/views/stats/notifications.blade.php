<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
</head>
<body>

    @include('stats.sidebar')

    <div class="page-wrapper">
        @include('partials.navbar')

        <div class="main-content">
            <h1 class="page-title">Mes Notifications</h1>

        <?php foreach($notifications as $n): ?>
            <div class="notif-item <?php echo $n->is_read ? '' : 'notif-unread'; ?>">
                <div>
                    <strong><?php echo $n->title; ?></strong>
                    <p style="margin: 5px 0; color: #666; font-size: 14px;"><?php echo $n->message; ?></p>
                </div>
                <?php if(!$n->is_read): ?>
                    <button class="btn btn-light" onclick="markAsRead(<?php echo $n->id; ?>, '{{ csrf_token() }}')">
                        Marquer lu
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    </div> <!-- End page-wrapper -->

    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>