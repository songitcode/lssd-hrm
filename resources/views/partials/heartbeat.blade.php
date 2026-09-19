<script>
    (function () {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token || !window.fetch) return;

        const sendHeartbeat = () => fetch(@json(route('heartbeat')), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: ''
        }).catch(() => undefined);

        sendHeartbeat();
        window.setInterval(sendHeartbeat, 45000);
    })();
</script>