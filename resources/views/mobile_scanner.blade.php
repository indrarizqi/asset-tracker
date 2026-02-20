<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vodeco Asset Scanner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { background-color: #f3f4f6; }
        .mobile-container { max-width: 480px; margin: 0 auto; background: white; min-height: 100vh; position: relative; }
        #reader { width: 100%; background: black; }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="mobile-container shadow-lg">
    
    <div id="page-login" class="p-8 flex flex-col justify-center h-screen">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-700">VODECO</h1>
            <p class="text-gray-500">Asset Tracker</p>
        </div>
        
        <div id="login-error" class="bg-red-100 text-red-700 p-3 rounded mb-4 hidden"></div>

        <input type="email" id="email" placeholder="Email" class="w-full p-3 border rounded mb-3">
        <input type="password" id="password" placeholder="Password" class="w-full p-3 border rounded mb-6">
        
        <button onclick="doLogin()" class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700 transition">
            SIGN IN
        </button>
    </div>

    <div id="page-scanner" class="hidden">
        <div class="bg-blue-600 p-4 text-white flex justify-between items-center shadow">
            <div>
                <h2 class="font-bold text-lg">Scanner</h2>
                <p class="text-xs opacity-80" id="user-name">Hi, User</p>
            </div>
            <button onclick="doLogout()" class="text-xs bg-blue-800 px-3 py-1 rounded">Logout</button>
        </div>

        <div class="p-4">
            <div id="reader" class="rounded-lg overflow-hidden shadow-md"></div>
            <p class="text-center text-gray-500 text-sm mt-2">Arahkan kamera ke QR Code Aset</p>
            <div id="scan-result" class="mt-2 text-center text-xs text-gray-400">Menunggu scan...</div>
        </div>
    </div>

    <div id="modal-action" class="fixed inset-0 bg-black bg-opacity-50 flex items-end z-50">
        <div class="bg-white w-full rounded-t-2xl p-6 animate-bounce-up">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800" id="asset-name">Laptop Dell</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-mono" id="asset-tag">M-24-001</span>
                </div>
                <button onclick="closeModal()" class="text-gray-400 text-2xl">&times;</button>
            </div>

            <div class="space-y-3" id="action-buttons">
                </div>
            
            <div id="action-loading" class="hidden text-center py-4 text-blue-600">Memproses...</div>
        </div>
    </div>

</div>

<script>
    // --- KONFIGURASI ---
    const API_URL = '/api'; 
    let html5QrcodeScanner = null;
    let currentToken = localStorage.getItem('token');
    let currentAssetTag = null;

    // --- LOGIKA APLIKASI ---

    // Cek status login saat load
    if (currentToken) {
        showScanner();
    }

    async function doLogin() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorBox = document.getElementById('login-error');

        try {
            const res = await fetch(API_URL + '/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();

            if (res.ok) {
                localStorage.setItem('token', data.access_token);
                localStorage.setItem('name', data.message.split(',')[0].replace('Hi ', '')); // Ambil nama
                currentToken = data.access_token;
                showScanner();
            } else {
                errorBox.textContent = data.message || 'Login Gagal';
                errorBox.classList.remove('hidden');
            }
        } catch (err) {
            alert("Error koneksi: " + err);
        }
    }

    function doLogout() {
        localStorage.clear();
        location.reload();
    }

    function showScanner() {
        document.getElementById('page-login').classList.add('hidden');
        document.getElementById('page-scanner').classList.remove('hidden');
        document.getElementById('user-name').textContent = 'Hi, ' + (localStorage.getItem('name') || 'User');

        // Mulai Kamera
        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            
            // Gunakan kamera belakang (environment)
            html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess);
        }
    }

    // SAAT QR BERHASIL DI-SCAN
    async function onScanSuccess(decodedText, decodedResult) {
        if (currentAssetTag === decodedText) return; // Cegah scan berulang cepat
        
        // Pause kamera sementara
        html5QrcodeScanner.pause(); 
        currentAssetTag = decodedText;
        document.getElementById('scan-result').textContent = "Mendeteksi: " + decodedText;

        // Panggil API Scan
        try {
            const res = await fetch(API_URL + '/scan/' + decodedText, {
                headers: { 'Authorization': 'Bearer ' + currentToken, 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (res.ok) {
                showActionModal(data.data, data.available_actions);
            } else {
                alert("Error: " + data.message);
                html5QrcodeScanner.resume();
                currentAssetTag = null;
            }
        } catch (err) {
            alert("Gagal mengambil data aset.");
            html5QrcodeScanner.resume();
        }
    }

    function showActionModal(asset, actions) {
        document.getElementById('asset-name').textContent = asset.name;
        document.getElementById('asset-tag').textContent = asset.asset_tag;
        
        const btnContainer = document.getElementById('action-buttons');
        btnContainer.innerHTML = ''; // Reset tombol

        // Render Tombol sesuai Status
        if (actions.includes('check_out')) {
            btnContainer.innerHTML += `<button onclick="submitAction('check_out')" class="w-full bg-green-600 text-white p-3 rounded font-bold shadow">📤 AMBIL / PINJAM ASET</button>`;
        }
        if (actions.includes('check_in')) {
            btnContainer.innerHTML += `<button onclick="submitAction('check_in')" class="w-full bg-orange-500 text-white p-3 rounded font-bold shadow">📥 KEMBALIKAN ASET</button>`;
        }
        
        // Tombol Maintenance (selalu ada)
        btnContainer.innerHTML += `<button onclick="submitAction('maintenance')" class="w-full bg-gray-200 text-gray-700 p-3 rounded font-bold">🔧 Lapor Maintenance</button>`;

        document.getElementById('modal-action').classList.remove('hidden');
    }

    async function submitAction(actionType) {
        const loading = document.getElementById('action-loading');
        const btns = document.getElementById('action-buttons');
        
        loading.classList.remove('hidden');
        btns.classList.add('hidden');

        try {
            const res = await fetch(API_URL + '/asset/action', {
                method: 'POST',
                headers: { 
                    'Authorization': 'Bearer ' + currentToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    asset_tag: currentAssetTag,
                    action: actionType,
                    verified_by_scan: true // Bukti scan fisik
                })
            });
            const result = await res.json();

            alert(result.message);
            closeModal();

        } catch (err) {
            alert("Gagal update status.");
            closeModal();
        }
    }

    function closeModal() {
        document.getElementById('modal-action').classList.add('hidden');
        document.getElementById('action-loading').classList.add('hidden');
        document.getElementById('action-buttons').classList.remove('hidden');
        
        // Reset state scanner
        currentAssetTag = null;
        if(html5QrcodeScanner) html5QrcodeScanner.resume();
    }
</script>

</body>
</html>