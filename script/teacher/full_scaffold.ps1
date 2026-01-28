# =========================
# Full Laravel Project Scaffold
# =========================

# Ensure we're in Laravel root
$root = Get-Location

Write-Host "Creating Laravel folder structure and placeholder files..." -ForegroundColor Green

# === Models ===
$models = @(
    "User.php","Election.php","Position.php","Candidate.php","Vote.php",
    "Pageantry.php","Donation.php","Project.php","Event.php","Announcement.php",
    "Blog.php","Comment.php","Chapter.php","Job.php","Message.php","PaymentLog.php"
)
$modelsPath = "$root\app\Models"
if (-Not (Test-Path $modelsPath)) { New-Item -ItemType Directory -Path $modelsPath }
foreach ($m in $models) { New-Item -ItemType File -Path "$modelsPath\$m" -Force }

# === Controllers ===
$controllerGroups = @{
    "Admin"      = @("ElectionController.php","PageantryController.php","DonationController.php","BlogController.php","ProjectController.php","EventController.php","UserController.php","ChapterRepController.php","JobController.php","MessageController.php","ReportController.php","SettingsController.php")
    "User"       = @("DashboardController.php","ElectionController.php","PageantryController.php","ProfileController.php","BlogController.php","ProjectController.php","MessageController.php")
    "ChapterRep" = @("ElectionController.php","UserController.php","ReportController.php","MessageController.php")
}
$controllersPath = "$root\app\Http\Controllers"
foreach ($group in $controllerGroups.Keys) {
    $path = "$controllersPath\$group"
    if (-Not (Test-Path $path)) { New-Item -ItemType Directory -Path $path }
    foreach ($file in $controllerGroups[$group]) { New-Item -ItemType File -Path "$path\$file" -Force }
}

# === Middleware ===
$middleware = @("SuperAdminMiddleware.php","AdminMiddleware.php","UserMiddleware.php","ChapterRepMiddleware.php")
$middlewarePath = "$root\app\Http\Middleware"
if (-Not (Test-Path $middlewarePath)) { New-Item -ItemType Directory -Path $middlewarePath }
foreach ($m in $middleware) { New-Item -ItemType File -Path "$middlewarePath\$m" -Force }

# === Services ===
$services = @("ElectionService.php","PageantryService.php","PaymentService.php","SMSService.php","EmailService.php","ReportingService.php")
$servicesPath = "$root\app\Services"
if (-Not (Test-Path $servicesPath)) { New-Item -ItemType Directory -Path $servicesPath }
foreach ($s in $services) { New-Item -ItemType File -Path "$servicesPath\$s" -Force }

# === Policies ===
$policiesPath = "$root\app\Policies"
if (-Not (Test-Path $policiesPath)) { New-Item -ItemType Directory -Path $policiesPath }

# === Requests ===
$requestsPath = "$root\app\Http\Requests"
if (-Not (Test-Path $requestsPath)) { New-Item -ItemType Directory -Path $requestsPath }

# === Notifications ===
$notificationsPath = "$root\app\Notifications"
if (-Not (Test-Path $notificationsPath)) { New-Item -ItemType Directory -Path $notificationsPath }

# === Observers ===
$observersPath = "$root\app\Observers"
if (-Not (Test-Path $observersPath)) { New-Item -ItemType Directory -Path $observersPath }

Write-Host "Laravel scaffold complete!" -ForegroundColor Cyan
