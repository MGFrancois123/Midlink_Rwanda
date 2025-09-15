document.addEventListener('DOMContentLoaded', function() {
    // Show loading state
    const showLoading = (show) => {
        const loadingElement = document.getElementById('loadingIndicator');
        if (loadingElement) {
            loadingElement.style.display = show ? 'block' : 'none';
        }
    };
    
    // Show error message
    const showError = (message) => {
        const errorDiv = document.getElementById('errorMessages');
        if (errorDiv) {
            errorDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
        }
    };
    
    // Load pharmacies and categories when the page loads
    showLoading(true);
    Promise.all([loadPharmacies(), loadCategories()])
        .catch(error => {
            console.error('Error initializing form:', error);
            showError('Failed to load required data. Please refresh the page.');
        })
        .finally(() => showLoading(false));
    
    // Function to load pharmacies into select dropdown
    function loadPharmacies() {
        return new Promise((resolve, reject) => {
            fetch('actions/fetch_data.php?fetch=pharmacies')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const select = document.getElementById('medicine_pharmacy');
                        if (!select) {
                            console.error('Pharmacy select element not found');
                            return resolve();
                        }
                        
                        select.innerHTML = '<option value="">Select Pharmacy</option>';
                        
                        if (data.data && data.data.length > 0) {
                            data.data.forEach(pharmacy => {
                                const option = document.createElement('option');
                                option.value = pharmacy.pharmacy_id;
                                option.textContent = pharmacy.name;
                                select.appendChild(option);
                            });
                            resolve();
                        } else {
                            showError('No pharmacies found. Please add pharmacies first.');
                            document.getElementById('submitBtn').disabled = true;
                            resolve();
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load pharmacies');
                    }
                })
                .catch(error => {
                    console.error('Error loading pharmacies:', error);
                    showError('Failed to load pharmacies. Please try again later.');
                    reject(error);
                });
        });
    }
    
    // Function to load categories into select dropdown
    function loadCategories() {
        return new Promise((resolve, reject) => {
            fetch('actions/fetch_data.php?fetch=categories')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const select = document.getElementById('medicine_category');
                        if (!select) {
                            console.error('Category select element not found');
                            return resolve();
                        }
                        
                        select.innerHTML = '<option value="">Select Category</option>';
                        
                        if (data.data && data.data.length > 0) {
                            data.data.forEach(category => {
                                const option = document.createElement('option');
                                option.value = category.category_id;
                                option.textContent = category.category_name;
                                select.appendChild(option);
                            });
                            resolve();
                        } else {
                            showError('No categories found. Please add categories first before adding medicines.');
                            document.getElementById('submitBtn').disabled = true;
                            resolve();
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load categories');
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    showError('Failed to load categories. Please try again later.');
                    reject(error);
                });
        });
    }
});
