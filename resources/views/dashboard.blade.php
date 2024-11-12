<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom card styles */
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);  
        }

        .product-card h5 {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        .product-card p {
            font-size: 1rem;
            color: #555;
        }

        .btn-logout {
            margin-top: 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn-logout:hover {
            background-color: #c82333;
        }

        .btn-edit,
        .btn-delete {
            margin-top: 10px;
            margin-right: 5px;
        }

        #edit-product-image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Product List</h1>

        <!-- Add Product Button -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>

        <!-- Product List -->
        <div id="product-list" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 mt-4">
            <!-- Product items will be dynamically added here -->
        </div>

        <!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="add-product-form" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="product-name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="product-name" required>
                            </div>
                            <div class="mb-3">
                                <label for="product-detail" class="form-label">Product Detail</label>
                                <textarea class="form-control" id="product-detail" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="product-image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="product-image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-product-form" action="{{ url('api/products') }}" enctype="multipart/form-data">
                            <input type="hidden" id="edit-product-id">
                            <div class="mb-3">
                                <label for="edit-product-name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="edit-product-name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-product-detail" class="form-label">Product Detail</label>
                                <textarea class="form-control" id="edit-product-detail" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="edit-product-image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="edit-product-image" accept="image/*">
                                <small class="form-text text-muted">Leave blank if you don't want to change the image.</small>
                            </div>
                            <img id="edit-product-image-preview" src="" alt="Image Preview">
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const token = localStorage.getItem('token');
                if (!token) {
                    window.location.href = '/login';
                    return;
                }

                fetchProducts();

                // Handle form submission to add a new product
                document.getElementById('add-product-form').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const productName = document.getElementById('product-name').value;
                    const productDetail = document.getElementById('product-detail').value;

                    addProduct(productName, productDetail);
                });


                document.getElementById('edit-product-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Form submit event triggered');

                    const productId = document.getElementById('edit-product-id').value;
                    const name = document.getElementById('edit-product-name').value;
                    const detail = document.getElementById('edit-product-detail').value;
                    const productImage = document.getElementById('edit-product-image').files[0];

                    console.log('Product ID:', productId);
                    console.log('Name:', name);
                    console.log('Detail:', detail);
                    console.log('Product Image:', productImage);

                    // Create FormData object
                    const formData = new FormData();
                    formData.append('name', name);
                    formData.append('detail', detail);

                    // Append the image only if it is provided
                    if (productImage) {
                        formData.append('image', productImage);
                    }

                    const token = localStorage.getItem('token');

                    // Perform the PUT request using FormData
                    fetch(`{{ url('api/products') }}/${productId}`, {
                        method: 'PUT', // Update method
                        headers: {
                            'Authorization': 'Bearer ' + token,
                              'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            detail: detail,
                            image_url: productImage
                        })// Use formData instead of JSON.stringify
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Product updated successfully');
                            window.location.reload(); // Optionally refresh the page or handle a UI update
                        } else {
                            alert('Error updating product: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating product:', error);
                    });
                });
            });
                   

                    

                //     if(name && detail && productImage){
                //         fetch(`{{ url('api/products') }}/${productId}`, {
                //             method: 'PUT',
                //             headers: {
                //                   'Authorization': 'Bearer ' + token,
                //                   'Content-Type': 'application/json'
                //             },
                //             body: Json.stringify({
                //                 name: name,
                //                 detail: detail,
                //                 productImage: productImage
                //             })
                //         })
                //         .then(response => response.json())
                //         .then(data => {
                //         if (data.success) {
                //             alert('Product updated successfully');
                //             window.location.reload();
                //         } else {
                //             alert('Error updating product: ' + data.message);
                //         }
                //     })
                //     //.catch(error => console.error('Error: ' error));

                //     // const formData = new FormData();
                //     // formData.append('name', name);
                //     // formData.append('detail', detail);
                //     // if (productImage) {
                //     //     formData.append('image', productImage);
                //     // }

                //     // updateProduct(productId, formData);
                // });
           

            function fetchProducts() {
                const token = localStorage.getItem('token');
                fetch('{{ url("api/products") }}', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const productListDiv = document.getElementById('product-list');
                    if (data.success) {
                        const products = data.data;
                        productListDiv.innerHTML = '';
                        products.forEach(product => {
                            const productCard = document.createElement('div');
                            productCard.classList.add('col');
                            productCard.innerHTML = `
                                <div class="product-card">
                                    ${product.image_url ? `<img src="${product.image_url}" class="card-img-top" alt="${product.name}">` : ''} 
                                    <div class="card-body">
                                        <h5 class="card-title">${product.name}</h5>
                                        <p class="card-text">${product.detail}</p>
                                        <button class="btn btn-warning btn-edit" onclick="editProduct(${product.id})">Edit</button>
                                        <button class="btn btn-danger btn-delete" onclick="deleteProduct(${product.id})">Delete</button>
                                    </div>
                                </div>
                            `;
                            productListDiv.appendChild(productCard);
                        });
                    } else {
                        productListDiv.innerHTML = '<p>No products found.</p>';
                    }
                })
                .catch(error => {
                    // console.error('Error fetching products:', error);
                    document.getElementById('product-list').innerHTML = '<p>An error occurred while fetching the product list.</p>';
                });
            }

            function addProduct(name, detail) {
                const productImage = document.getElementById('product-image').files[0];
                const formData = new FormData();
                formData.append('name', name);
                formData.append('detail', detail);
                if (productImage) {
                    formData.append('image', productImage);
                }

                const token = localStorage.getItem('token');

                console.log(token);
                fetch('{{ url("api/products") }}', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        // Don't set Content-Type header when using FormData
                    },
                    body: formData // Send form data, not JSON
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchProducts();
                        const addModal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                        addModal.hide();
                    } else {
                        alert('Error adding product: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error adding product:', error);
                });
            }


            function editProduct(productId) {
                const token = localStorage.getItem('token');
                fetch(`{{ url('api/products') }}/${productId}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(product => {
                    if (product.success) {
                        document.getElementById('edit-product-id').value = product.data.id;
                        document.getElementById('edit-product-name').value = product.data.name;
                        document.getElementById('edit-product-detail').value = product.data.detail;
                        const imagePreview = document.getElementById('edit-product-image-preview');
                        imagePreview.src = product.data.image_url ? `${product.data.image_url}` : '';

                        const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                        editModal.show();
                    }
                })
            }

            // function updateProduct(productId, name, detail, image) {
            //     const formData = new FormData();
            //     formData.append('name', name);
            //     formData.append('detail', detail);
            //     if (image) {
            //         formData.append('image', image);
            //     }
                
            //     const token = localStorage.getItem('token');
            //     fetch(`{{ url('api/products') }}/${productId}`, {
            //         method: 'PUT',
            //         headers: {
            //             'Authorization': 'Bearer ' + token,
            //             'Accept': 'application/json',
            //         },
            //         body: formData
            //     })
            //     .then(response => response.json())
            //     .then(data => {
            //         if (data.success) {
            //             alert('Product updated successfully');
            //             fetchProducts();  // Reload the page to show updated data
            //         } else {
            //             alert('Error updating product');
            //         }
            //     })
            //     .catch(error => {
            //         console.error('Error updating product:', error);
            //     });
            // }

            function deleteProduct(productId) {
                const token = localStorage.getItem('token');
                fetch(`{{ url('api/products') }}/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchProducts();
                    }
                })
                .catch(error => {
                    console.error('Error deleting product:', error);
                });
            }
        </script>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
