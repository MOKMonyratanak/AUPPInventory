import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

$(document).ready(function () {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select a Device Type',
        allowClear: true,
    });

    // Sidebar toggle functionality
    const sidebarToggle = $('#sidebarToggle');
    const sidebar = $('#sidebar');
    const contentWrapper = $('#content-wrapper');

    sidebarToggle.on('click', function () {
        sidebar.toggleClass('collapsed');
        contentWrapper.toggleClass('collapsed');

        // Dynamically adjust the position of the toggle button
        if (sidebar.hasClass('collapsed')) {
            sidebarToggle.css('left', '63px'); // Align with collapsed sidebar
        } else {
            sidebarToggle.css('left', '250px'); // Align with expanded sidebar
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    // Sanitize user input
    const { index } = window.assetRoutes || {};
    const searchForm = document.querySelector(`form[action="${index}"]`);
    if (searchForm) {
        sanitizeSearchForm(searchForm);
    }

    // Add event listeners to delete buttons
    document.querySelectorAll('[data-delete-btn]').forEach((deleteButton) => {
        deleteButton.addEventListener('click', (event) => {
            const status = deleteButton.dataset.status;

            if (!handleDelete(event, status)) {
                event.preventDefault();
            }
        });
    });

    // Check if the dashboard container exists
    const dashboardContainer = document.getElementById('dashboard-container');
    if (dashboardContainer) {
        initDashboardCharts();
    }

    // Show/Hide Password input box
    const userCreateContainer = document.getElementById('user-create-container');
    const userEditContainer = document.getElementById('user-edit-container');
    if (userCreateContainer  || userEditContainer) {
        initUserForm();
    }

    // Check if we're on the issue_assets page
    const issueAssetsContainer = document.getElementById('issue-assets-container');
    if (issueAssetsContainer) {
        initIssueAssetsPage();
    }    

    // Check if on the users/index page
    const usersIndexContainer = document.getElementById('users-index-container');
    if (usersIndexContainer) {
        initUsersIndexPage();
    }

    const usersShowContainer = document.getElementById('users-show-container');
    if (usersShowContainer) {
        initUsersShowPage();
    }
});

// Handle delete logic
function handleDelete(event, status) {
    if (status === 'issued') {
        alert("You can't delete an asset with the status 'issued'.");
        return false;
    }

    const firstConfirm = confirm(
        'Are you sure you want to delete this record? This action cannot be undone.'
    );
    if (!firstConfirm) return false;

    const secondConfirm = confirm(
        'This is your final confirmation. Do you really want to delete this record?'
    );
    return secondConfirm;
}

// Dashboard
// Initialize the charts for the dashboard
function initDashboardCharts() {
    // Brand Distribution Pie Chart
    const brandChartElement = document.getElementById('brandChart');
    if (brandChartElement) {
        const ctxPie = brandChartElement.getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: JSON.parse(brandChartElement.dataset.labels),
                datasets: [{
                    data: JSON.parse(brandChartElement.dataset.counts),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', 
                        '#F7464A', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360', '#AC64AD',
                        '#2ECC71', '#1ABC9C', '#9B59B6', '#F1C40F', '#E67E22', '#E74C3C', 
                        '#3498DB', '#34495E', '#95A5A6', '#D35400', '#C0392B', '#7F8C8D', 
                        '#27AE60', '#16A085', '#2C3E50', '#BDC3C7', '#8E44AD', '#1F618D'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Asset Distribution by Brand' }
                }
            }
        });
    }

    // Issued Assets by Device Type Bar Chart
    const assetChartElement = document.getElementById('assetChart');
    if (assetChartElement) {
        const ctxBar = assetChartElement.getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: JSON.parse(assetChartElement.dataset.deviceTypeLabels),
                datasets: [
                    {
                        label: 'Remaining Assets',
                        data: JSON.parse(assetChartElement.dataset.remainingDeviceCounts),
                        backgroundColor: '#4CAF50',
                    },
                    {
                        label: 'Issued Assets',
                        data: JSON.parse(assetChartElement.dataset.issuedDeviceCounts),
                        backgroundColor: '#FF6384',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Issued vs Remaining Assets by Device Type' }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Device Types' },
                        ticks: {
                            autoSkip: false, // Show all labels
                            maxRotation: 45, // Rotate labels for better readability
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Number of Assets' }
                    }
                }
            }
        });
    }
}

function initUserForm() {
    const roleSelect = document.getElementById('role');
    const passwordFields = document.getElementById('passwordFields');
    const passwordInput = document.querySelector('input[name="password"]');
    const passwordConfirmationInput = document.querySelector('input[name="password_confirmation"]');

    // Function to toggle password fields
    function togglePasswordFields() {
        const role = roleSelect.value;
        if (role === 'user') {
            passwordFields.style.display = 'none'; // Hide password fields for "user"
            if (passwordInput) passwordInput.disabled = true;
            if (passwordConfirmationInput) passwordConfirmationInput.disabled = true;
        } else {
            passwordFields.style.display = 'block'; // Show password fields for other roles
            if (passwordInput) passwordInput.disabled = false;
            if (passwordConfirmationInput) passwordConfirmationInput.disabled = false;
        }
    }

    // Attach event listener for role changes
    roleSelect.addEventListener('change', togglePasswordFields);

    // Call the function initially to set the correct state on page load
    togglePasswordFields();
}

// Function to initialize the issue assets page
function initIssueAssetsPage() {
    // Move assets between lists
    function moveSelected(fromId, toId) {
        const from = document.getElementById(fromId);
        const to = document.getElementById(toId);
        const selectedOptions = Array.from(from.selectedOptions);

        selectedOptions.forEach(option => {
            from.removeChild(option);
            to.appendChild(option);
        });
    }

    // Assign button click
    document.getElementById('assignAsset').addEventListener('click', () => {
        moveSelected('availableAssets', 'userAssets');
    });

    // Remove button click
    document.getElementById('removeAsset').addEventListener('click', () => {
        moveSelected('userAssets', 'availableAssets');
    });

    // Ensure all selected assets are submitted
    document.getElementById('assetAssignmentForm').addEventListener('submit', () => {
        document.querySelectorAll('select[multiple]').forEach(select => {
            Array.from(select.options).forEach(option => option.selected = true);
        });
    });

    // Initialize Select2 for the searchable dropdown
    const searchableAssets = $('#searchableAssets');
    if (searchableAssets.length > 0) {
        searchableAssets.select2({
            placeholder: "Search and select an asset",
            allowClear: true,
            width: '100%'
        });

        // Listen for changes in the searchable dropdown
        searchableAssets.on('change', function () {
            const selectedAsset = $(this).val();

            if (selectedAsset) {
                // Move selected asset to userAssets
                $('#availableAssets option[value="' + selectedAsset + '"]').prop('selected', true);
                moveSelected('availableAssets', 'userAssets');

                // Clear selection from Select2 after moving
                $(this).val(null).trigger('change');
            }
        });
    }
}

// Sanitize search form input
function sanitizeSearchForm(searchForm) {
    const searchInput = searchForm.querySelector('input[name="search"]');

    searchForm.addEventListener('submit', (event) => {
        const userInput = searchInput.value;

        // Create a temporary element to leverage the browser's HTML entity encoding
        const tempElement = document.createElement('div');
        tempElement.textContent = userInput; // Automatically encode special characters
        searchInput.value = tempElement.innerHTML; // Update input with encoded value
    });
}

// Initialize the users/index page
function initUsersIndexPage() {
    // Handle error messages from session
    const errorMessage = document.getElementById('users-index-container').dataset.errorMessage;    
    if (errorMessage) {
        alert(errorMessage);
    }

    // Add event listeners to resign buttons
    document.querySelectorAll('.resign-button').forEach((button) => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            handleResignButtonClick(this);
        });
    });

    // Add event listeners to delete buttons
    document.querySelectorAll('.delete-button').forEach((button) => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            handleDeleteButtonClick(this);
        });
    });

    // Handle the issue asset modal
    const purposeForm = document.getElementById('purposeForm');
    document.querySelectorAll('.issue-asset-btn').forEach((button) => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-user-id');
            if (userId) {
                purposeForm.setAttribute('action', `/users/${userId}/assets/issue`);
            } else {
                purposeForm.setAttribute('action', '#');
            }
        });
    });
}

// Initialize the users/index page
function initUsersShowPage() {
    // Handle error messages from session
    const errorMessage = document.getElementById('users-show-container').dataset.errorMessage;
    if (errorMessage) {
        alert(errorMessage);
    }

    // Add event listeners to resign buttons
    document.querySelectorAll('.resign-button').forEach((button) => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            handleResignButtonClick(this);
        });
    });

    // Add event listeners to delete buttons
    document.querySelectorAll('.delete-button').forEach((button) => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            handleDeleteButtonClick(this);
        });
    });

    // Handle the issue asset modal
    const purposeForm = document.getElementById('purposeForm');
    document.querySelectorAll('.issue-asset-btn').forEach((button) => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-user-id');
            if (userId) {
                purposeForm.setAttribute('action', `/users/${userId}/assets/issue`);
            } else {
                purposeForm.setAttribute('action', '#');
            }
        });
    });
}

// Handle resign button click
function handleResignButtonClick(button) {
    const userId = button.closest('.resign-form').getAttribute('action').split('/').slice(-2, -1)[0];
    const checkIssuedAssetsUrl = `/users/${userId}/check-issued-assets`;

    fetch(checkIssuedAssetsUrl, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.hasIssuedAssets) {
                alert('The user cannot resign until all issued assets are returned to the company.');
            } else {
                if (confirm('Have all the assets been returned?')) {
                    if (confirm('Are you sure all assets have been returned?')) {
                        button.closest('.resign-form').submit();
                    }
                }
            }
        })
        .catch((error) => console.error('Error checking issued assets:', error));
}

// Handle delete button click
function handleDeleteButtonClick(button) {
    const userId = button.closest('.delete-form').getAttribute('action').split('/').slice(-1)[0];
    const checkIssuedAssetsUrl = `/users/${userId}/check-issued-assets`;

    fetch(checkIssuedAssetsUrl, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.hasIssuedAssets) {
                alert('The user cannot be deleted until all issued assets are returned to the company.');
            } else {
                if (handleDelete()) {
                    button.closest('.delete-form').submit();
                }
            }
        })
        .catch((error) => console.error('Error checking issued assets:', error));
}
