jQuery(document).ready(function($) {
    
    // Initialize Chart.js
    if (typeof Chart !== 'undefined' && $('#taskChart').length) {
        initializeChart();
    }
    
    // Load Chart.js from CDN and initialize
    if ($('#taskChart').length) {
        loadChartJS();
    }
    
    // Filter tasks functionality
    $('#filter-tasks').on('click', function(e) {
        e.preventDefault();
        filterTasks();
    });
    
    // Update task status
    $(document).on('click', '.promanage-update-status', function(e) {
        e.preventDefault();
        updateTaskStatus($(this));
    });
    
    function loadChartJS() {
        if (typeof Chart === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = function() {
                initializeChart();
            };
            document.head.appendChild(script);
        } else {
            initializeChart();
        }
    }
    
    function initializeChart() {
        var ctx = document.getElementById('taskChart');
        if (!ctx) return;
        
        var chartData = window.promanage_chart_data || {
            done: 0,
            in_progress: 0,
            pending: 0
        };
        
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Done', 'In Progress', 'Pending'],
                datasets: [{
                    data: [chartData.done, chartData.in_progress, chartData.pending],
                    backgroundColor: ['#16A34A', '#F59E0B', '#EF4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }
    
    function filterTasks() {
        var status = $('#status-filter').val();
        var userId = $('#user-filter').val();
        var projectId = $('#project-filter').val();
        
        $('#promanage-tasks-container').addClass('promanage-loading');
        
        $.ajax({
            url: promanage_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'promanage_filter_tasks',
                status: status,
                user_id: userId,
                project_id: projectId,
                nonce: promanage_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#promanage-tasks-container').html(response.data.html);
                } else {
                    alert('Error filtering tasks');
                }
            },
            error: function() {
                alert('Error filtering tasks');
            },
            complete: function() {
                $('#promanage-tasks-container').removeClass('promanage-loading');
            }
        });
    }
    
    function updateTaskStatus($button) {
        var taskId = $button.data('task-id');
        var status = $button.data('status');
        
        $button.prop('disabled', true).addClass('promanage-loading');
        
        $.ajax({
            url: promanage_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'promanage_update_task_status',
                task_id: taskId,
                status: status,
                nonce: promanage_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update the task card status
                    var $taskCard = $button.closest('.promanage-task-card');
                    $taskCard.removeClass('promanage-status-pending promanage-status-in_progress promanage-status-done');
                    $taskCard.addClass('promanage-status-' + status);
                    
                    // Update status badge
                    var $statusBadge = $taskCard.find('.promanage-status-badge');
                    $statusBadge.removeClass('promanage-status-pending promanage-status-in_progress promanage-status-done');
                    $statusBadge.addClass('promanage-status-' + status);
                    $statusBadge.text(status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' '));
                    
                    // Show success message
                    showNotification('Task status updated successfully', 'success');
                    
                    // Refresh the page after a short delay to update the chart
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification('Error updating task status', 'error');
                }
            },
            error: function() {
                showNotification('Error updating task status', 'error');
            },
            complete: function() {
                $button.prop('disabled', false).removeClass('promanage-loading');
            }
        });
    }
    
    function showNotification(message, type) {
        var $notification = $('<div class="promanage-notification promanage-notification-' + type + '">' + message + '</div>');
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.addClass('show');
        }, 100);
        
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Add notification styles
    if (!$('#promanage-notification-styles').length) {
        var styles = `
            <style id="promanage-notification-styles">
                .promanage-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    border-radius: 6px;
                    color: white;
                    font-weight: 500;
                    z-index: 9999;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                }
                
                .promanage-notification.show {
                    transform: translateX(0);
                }
                
                .promanage-notification-success {
                    background: #16A34A;
                }
                
                .promanage-notification-error {
                    background: #EF4444;
                }
            </style>
        `;
        $('head').append(styles);
    }
});