<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            <strong>Important:</strong> Regular backups are recommended. Backups include your database and configuration files.
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light mb-4">
                    <div class="card-body text-center">
                        <h5><i class="fas fa-database"></i> Create Backup</h5>
                        <p>Download a complete backup of your database</p>
                        <a href="{{ route('settings.backup') }}" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Backup
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-light mb-4">
                    <div class="card-body text-center">
                        <h5><i class="fas fa-upload"></i> Restore Backup</h5>
                        <p>Restore database from a backup file</p>
                        <form action="{{ route('settings.restore') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="backup_file" class="form-control mb-2" accept=".sql,.sqlite,.zip" required>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-upload"></i> Restore Backup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Backup Settings</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update-backup') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Auto Backup Frequency</label>
                                <select name="auto_backup" class="form-control">
                                    <option value="daily" {{ ($autoBackup ?? getSetting('auto_backup', 'daily')) == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ ($autoBackup ?? getSetting('auto_backup', 'daily')) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ ($autoBackup ?? getSetting('auto_backup', 'daily')) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="disabled" {{ ($autoBackup ?? getSetting('auto_backup', 'daily')) == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Backup Retention (days)</label>
                                <input type="number" name="backup_retention" class="form-control" 
                                       value="{{ $backupRetention ?? getSetting('backup_retention', 30) }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="backup_include_files" class="form-check-input" id="includeFiles" 
                                           value="1" {{ (getSetting('backup_include_files', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="includeFiles">Include Files in Backup</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Backup Storage Path</label>
                                <input type="text" name="backup_path" class="form-control" 
                                       value="{{ $backupPath ?? getSetting('backup_path', 'storage/app/backups') }}">
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Backup Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Available Backups</h6>
            </div>
            <div class="card-body">
                <div id="backupList">
                    <div class="text-center text-muted">
                        <i class="fas fa-spinner fa-spin"></i> Loading backups...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function loadBackups() {
        $.ajax({
            url: '{{ route("settings.backups.list") }}',
            method: 'GET',
            success: function(backups) {
                if (backups.length === 0) {
                    $('#backupList').html('<div class="alert alert-info">No backups found. Create your first backup using the button above.</div>');
                    return;
                }
                
                let html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
                html += '<thead><tr><th>Filename</th><th>Size</th><th>Date</th><th>Action</th></tr></thead><tbody>';
                
                backups.forEach(function(backup) {
                    html += `<tr>
                        <td><i class="fas fa-file-archive"></i> ${backup.name}</td>
                        <td>${backup.size}</td>
                        <td>${backup.date}</td>
                        <td>
                            <a href="${backup.path}" class="btn btn-sm btn-primary" download>
                                <i class="fas fa-download"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteBackup('${backup.name}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                $('#backupList').html(html);
            },
            error: function() {
                $('#backupList').html('<div class="alert alert-danger">Error loading backups. Please refresh and try again.</div>');
            }
        });
    }
    
    function deleteBackup(filename) {
        if (confirm('Are you sure you want to delete this backup?')) {
            $.ajax({
                url: '/settings/backups/' + encodeURIComponent(filename),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        alert('Backup deleted successfully!');
                        loadBackups();
                    } else {
                        alert('Failed to delete backup: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function() {
                    alert('Error deleting backup. Please try again.');
                }
            });
        }
    }
    
    $(document).ready(function() {
        loadBackups();
    });
</script>
@endpush