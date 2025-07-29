(function($) {
    'use strict';

    // Check import status every 3 seconds
    let statusChecker;
    
    // Check logs every 2 seconds
    let logsChecker;
    
    // Initialize after page load
    $(document).ready(function() {
        console.log('MP Import: Admin script loaded');
        
        // Check if we're on the import page
        if ($('#mp-import-status').length) {
            console.log('MP Import: Import page detected');
            
            // Initialize automatic status checking if import is in progress
            if ($('#mp-import-status').hasClass('is-active')) {
                console.log('MP Import: Active import detected, starting status checker');
                startStatusChecker();
                // Also start logs checker when import is active
                startLogsChecker();
            }
            
            // Import form handler
            $('#mp-import-form').on('submit', function(e) {
                console.log('MP Import: Import form submitted');
            });
            
            // Stop button handler
            $(document).on('click', '#mp-stop-import', function(e) {
                e.preventDefault();
                console.log('MP Import: Stop button clicked');
                stopImport();
            });
            
            // Continue button handler
            $(document).on('click', '#mp-continue-import', function(e) {
                e.preventDefault();
                console.log('MP Import: Continue button clicked');
                continueImport();
            });
            
            // Auto import button handler
            $(document).on('click', '#mp-auto-import', function(e) {
                e.preventDefault();
                console.log('MP Import: Auto import button clicked');
                processNextBatch();
            });
            
            // Emergency stop button handler
            $(document).on('click', '#mp-emergency-stop, #mp-emergency-stop2', function(e) {
                e.preventDefault();
                console.log('MP Import: Emergency stop button clicked');
                if (confirm('Czy na pewno chcesz zatrzymać WSZYSTKIE procesy importu?')) {
                    emergencyStopImport();
                }
            });
            
            // Initial log fetch
            fetchImportLogs();
        }
    });
    
    // Start checking import status
    function startStatusChecker() {
        // Stop existing checker if it exists
        if (statusChecker) {
            clearInterval(statusChecker);
        }
        
        // Check status immediately
        checkImportStatus();
        
        // Set interval to check every 3 seconds
        statusChecker = setInterval(checkImportStatus, 3000);
        console.log('MP Import: Status checker started');
    }
    
    // Stop checking status
    function stopStatusChecker() {
        if (statusChecker) {
            clearInterval(statusChecker);
            statusChecker = null;
            console.log('MP Import: Status checker stopped');
        }
    }

    // Start checking logs
    function startLogsChecker() {
        // Stop existing checker if it exists
        if (logsChecker) {
            clearInterval(logsChecker);
        }
        
        // Set interval to check every 2 seconds
        logsChecker = setInterval(fetchImportLogs, 2000);
        console.log('MP Import: Logs checker started');
    }
    
    // Stop checking logs
    function stopLogsChecker() {
        if (logsChecker) {
            clearInterval(logsChecker);
            logsChecker = null;
            console.log('MP Import: Logs checker stopped');
        }
    }
    
    // Fetch import logs via AJAX
    function fetchImportLogs() {
        $.ajax({
            url: mp_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'mp_get_import_logs',
                nonce: mp_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateLogsUI(response.logs, response.empty);
                }
            },
            error: function(xhr, status, error) {
                console.error('MP Import: Error fetching logs:', error);
            }
        });
    }
    
    // Update logs UI
    function updateLogsUI(logs, isEmpty) {
        if (isEmpty) {
            $('#mp-no-logs').show();
            $('#mp-logs-content').parent('.mp-logs-wrapper').hide();
        } else {
            $('#mp-no-logs').hide();
            
            // Update logs content
            $('#mp-logs-content').text(logs);
            
            // Make sure the wrapper is visible
            $('#mp-logs-content').parent('.mp-logs-wrapper').show();
            
            // Scroll to bottom
            const logsContainer = document.querySelector('.mp-logs-wrapper');
            if (logsContainer) {
                logsContainer.scrollTop = logsContainer.scrollHeight;
            }
        }
    }
    
    // Check import status via AJAX
    function checkImportStatus() {
        console.log('MP Import: Checking status...');
        
        $.ajax({
            url: mp_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'mp_check_import_status',
                nonce: mp_admin.nonce
            },
            success: function(response) {
                console.log('MP Import: Status received', response);
                
                if (response.status === 'in_progress') {
                    // If import is in progress, update UI and automatically process next batch
                    updateProgressUI(response);
                    
                    // Start logs checker if not already running
                    if (!logsChecker) {
                        startLogsChecker();
                    }
                    
                    // If we have auto-import button, click it
                    if ($('#mp-auto-import').length && $('#mp-auto-import').is(':visible')) {
                        $('#mp-auto-import').trigger('click');
                    } else {
                        // Add button if it doesn't exist
                        if ($('#mp-auto-import').length === 0) {
                            $('.mp-actions').append('<button id="mp-auto-import" class="button button-primary" style="margin-left: 10px;">Przetwórz następną partię</button>');
                        }
                    }
                } else if (response.status === 'running') {
                    // Status "running" - update UI
                    updateProgressUI(response);
                    
                    // Start logs checker if not already running
                    if (!logsChecker) {
                        startLogsChecker();
                    }
                } else if (response.status === 'completed') {
                    // Import completed - show message
                    stopStatusChecker();
                    // Keep logs checker running for a bit longer to show final logs
                    setTimeout(stopLogsChecker, 5000);
                    showCompletedMessage(response.imported);
                } else if (response.status === 'stopped') {
                    // Import stopped - show message
                    stopStatusChecker();
                    // Keep logs checker running for a bit longer to show final logs
                    setTimeout(stopLogsChecker, 5000);
                    showStoppedMessage(response.imported);
                } else {
                    // Import not running
                    stopStatusChecker();
                    stopLogsChecker();
                }
            },
            error: function(xhr, status, error) {
                console.error('MP Import: Error checking import status:', error);
                $('#mp-error-message').text('Error checking import status: ' + error);
                $('#mp-import-error').show();
                stopStatusChecker();
                stopLogsChecker();
            }
        });
    }
    
    // Update progress UI
    function updateProgressUI(data) {
        if (data.status === 'in_progress' || data.status === 'running') {
            // Make sure progress bar is visible
            $('#mp-import-status').addClass('is-active');
            
            // Make sure numbers are properly formatted as numbers
            var imported = parseInt(data.imported) || 0;
            var total = parseInt(data.total) || 1; // Avoid division by zero
            
            // Convert to text before placing in HTML
            $('#mp-imported').text(imported.toString());
            $('#mp-total').text(total.toString());
            
            // Update progress bar
            const progressPercent = total > 0 
                ? Math.min(100, Math.round((data.current / total) * 100)) 
                : 0;
            
            console.log('MP Import: Progress updated to ' + progressPercent + '%');
            $('.mp-progress-bar-inner').css('width', progressPercent + '%');
        }
    }
    
    // Process next batch of imports
    function processNextBatch() {
        console.log('MP Import: Processing next batch');
        
        $.ajax({
            url: mp_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'mp_process_next_batch',
                nonce: mp_admin.nonce
            },
            success: function(response) {
                console.log('MP Import: Batch processed', response);
                
                if (response.status === 'in_progress') {
                    // Update interface
                    updateProgressUI(response);
                    
                    // Start status checking if it's not already running
                    if (!statusChecker) {
                        startStatusChecker();
                    }
                    
                    // Start logs checker if not already running
                    if (!logsChecker) {
                        startLogsChecker();
                    }
                } else if (response.status === 'completed') {
                    // Import completed
                    updateProgressUI(response);
                    stopStatusChecker();
                    // Keep logs checker running for a bit longer to show final logs
                    setTimeout(stopLogsChecker, 5000);
                    showCompletedMessage(response.imported);
                } else if (response.status === 'stopped') {
                    // Import stopped
                    stopStatusChecker();
                    // Keep logs checker running for a bit longer to show final logs
                    setTimeout(stopLogsChecker, 5000);
                    showStoppedMessage(response.imported);
                }
            },
            error: function(xhr, status, error) {
                console.error('MP Import: Błąd podczas przetwarzania partii:', error);
                $('#mp-error-message').text('Błąd podczas przetwarzania partii: ' + error);
                $('#mp-import-error').show();
            }
        });
    }
    
    // Stop import
    function stopImport() {
        console.log('MP Import: Zatrzymywanie importu...');
        
        $.ajax({
            url: mp_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'mp_stop_import',
                nonce: mp_admin.nonce
            },
            success: function(response) {
                console.log('MP Import: Import zatrzymany', response);
                stopStatusChecker();
                
                // Show stop message
                showStoppedMessage(response.status.imported);
            },
            error: function(xhr, status, error) {
                console.error('MP Import: Błąd podczas zatrzymywania importu:', error);
                $('#mp-error-message').text('Błąd podczas zatrzymywania importu: ' + error);
                $('#mp-import-error').show();
            }
        });
    }
    
    // Continue import
    function continueImport() {
        console.log('MP Import: Kontynuacja importu...');
        
        $.ajax({
            url: mp_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'mp_continue_import',
                nonce: mp_admin.nonce
            },
            success: function(response) {
                console.log('MP Import: Kontynuowanie importu', response);
                
                // Hide stop message
                $('#mp-import-stopped').hide();
                
                // Show progress bar
                $('#mp-import-status').addClass('is-active');
                
                // Update interface
                updateProgressUI(response);
                
                // Start status checking
                startStatusChecker();
            },
            error: function(xhr, status, error) {
                console.error('MP Import: Error continuing import:', error);
                $('#mp-error-message').text('Error continuing import: ' + error);
                $('#mp-import-error').show();
            }
        });
    }
    
    // Emergency stop all imports
    function emergencyStopImport() {
        console.log('MP Import: Awaryjne zatrzymanie wszystkich importów...');
        
        $.ajax({
            url: mp_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'mp_emergency_stop',
                nonce: mp_admin.nonce
            },
            success: function(response) {
                console.log('MP Import: Wszystkie importy wstrzymane', response);
                
                // Reload page
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.error('MP Import: Błąd podczas zatrzymywania importu:', error);
                alert('Wystąpił błąd podczas zatrzymywania importu: ' + error);
            }
        });
    }
    
    // Show import completion message
    function showCompletedMessage(importedCount) {
        console.log('MP Import: Import zakończony, zaimportowano: ' + importedCount);
        
        $('#mp-completed-count').text(importedCount);
        $('#mp-import-completed').show();
        $('#mp-import-status').removeClass('is-active');
        
        // Hide Stop Import and Process Next Batch buttons
        $('#mp-stop-import, #mp-auto-import').hide();
    }
    
    // Show import stopped message
    function showStoppedMessage(importedCount) {
        console.log('MP Import: Import zatrzymany, zaimportowano: ' + importedCount);
        
        $('#mp-stopped-count').text(importedCount);
        $('#mp-import-stopped').show();
        $('#mp-import-status').removeClass('is-active');
    }
    
})(jQuery);
