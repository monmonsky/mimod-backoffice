import Toast from '../../../components/toast.js';
import Ajax from '../../../utils/ajax.js';

let currentFlashSaleId = null;

window.openCreateModal = function() {
    currentFlashSaleId = null;
    document.getElementById('flashSaleForm').reset();
    document.querySelector('#flashSaleModal h3').textContent = 'Create Flash Sale';
    flashSaleModal.showModal();
};

window.viewFlashSale = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/flash-sales/${id}`);

        if (response.success) {
            const { flash_sale, products } = response.data;

            let html = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/60">Name</div>
                            <div class="font-medium">${flash_sale.name}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Priority</div>
                            <div>${flash_sale.priority}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Start Time</div>
                            <div>${new Date(flash_sale.start_time).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">End Time</div>
                            <div>${new Date(flash_sale.end_time).toLocaleString('id-ID')}</div>
                        </div>
                    </div>

                    ${flash_sale.description ? `
                    <div>
                        <div class="text-sm text-base-content/60">Description</div>
                        <div class="mt-1">${flash_sale.description}</div>
                    </div>
                    ` : ''}

                    <div class="divider">Products in Flash Sale</div>

                    ${products.length > 0 ? `
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Discount</th>
                                        <th>Stock</th>
                                        <th>Sold</th>
                                        <th>Max/Customer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${products.map(p => `
                                        <tr>
                                            <td>${p.product_name}</td>
                                            <td>
                                                ${p.discount_type === 'percentage'
                                                    ? `${p.discount_value}%`
                                                    : `Rp ${parseFloat(p.discount_value).toLocaleString('id-ID')}`
                                                }
                                            </td>
                                            <td>${p.stock_limit || '∞'}</td>
                                            <td>${p.sold_count}</td>
                                            <td>${p.max_per_customer}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : `
                        <div class="text-center py-4 text-base-content/60">
                            No products added yet
                        </div>
                    `}
                </div>
            `;

            document.getElementById('flashSaleDetails').innerHTML = html;
            viewFlashSaleModal.showModal();
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

window.editFlashSale = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/flash-sales/${id}`);

        if (response.success) {
            currentFlashSaleId = id;
            const flashSale = response.data.flash_sale;
            const form = document.getElementById('flashSaleForm');

            form.querySelector('[name="name"]').value = flashSale.name;
            form.querySelector('[name="description"]').value = flashSale.description || '';
            form.querySelector('[name="start_time"]').value = new Date(flashSale.start_time).toISOString().slice(0, 16);
            form.querySelector('[name="end_time"]').value = new Date(flashSale.end_time).toISOString().slice(0, 16);
            form.querySelector('[name="priority"]').value = flashSale.priority;
            form.querySelector('[name="is_active"]').checked = flashSale.is_active;

            document.querySelector('#flashSaleModal h3').textContent = 'Edit Flash Sale';
            flashSaleModal.showModal();
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

window.deleteFlashSale = async function(id) {
    if (!confirm('Are you sure you want to delete this flash sale?')) return;

    try {
        const response = await Ajax.delete(`/api/marketing/flash-sales/${id}`);

        if (response.success) {
            Toast.showToast(response.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

window.manageProducts = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/flash-sales/${id}`);

        if (response.success) {
            const { products } = response.data;

            let html = `
                <div class="space-y-4">
                    <div class="alert alert-info">
                        <span class="iconify lucide--info size-4"></span>
                        <span>Add or remove products from this flash sale</span>
                    </div>

                    <button class="btn btn-primary btn-sm" onclick="addProductToFlashSale(${id})">
                        <span class="iconify lucide--plus size-4"></span>
                        Add Product
                    </button>

                    ${products.length > 0 ? `
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Discount</th>
                                        <th>Stock</th>
                                        <th>Sold</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${products.map(p => `
                                        <tr>
                                            <td>${p.product_name}</td>
                                            <td>
                                                ${p.discount_type === 'percentage'
                                                    ? `${p.discount_value}%`
                                                    : `Rp ${parseFloat(p.discount_value).toLocaleString('id-ID')}`
                                                }
                                            </td>
                                            <td>${p.stock_limit || '∞'}</td>
                                            <td>${p.sold_count}</td>
                                            <td>
                                                <button class="btn btn-xs btn-error" onclick="removeProductFromFlashSale(${id}, ${p.product_id})">
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : `
                        <div class="text-center py-4 text-base-content/60">
                            No products added yet
                        </div>
                    `}
                </div>
            `;

            document.getElementById('productsList').innerHTML = html;
            manageProductsModal.showModal();
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

window.addProductToFlashSale = function(flashSaleId) {
    Toast.showToast('Product management feature coming soon', 'info');
};

window.removeProductFromFlashSale = async function(flashSaleId, productId) {
    if (!confirm('Remove this product from flash sale?')) return;

    try {
        const response = await Ajax.delete(`/api/marketing/flash-sales/${flashSaleId}/products`, {
            product_id: productId
        });

        if (response.success) {
            Toast.showToast(response.message, 'success');
            manageProducts(flashSaleId);
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

document.getElementById('flashSaleForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        start_time: formData.get('start_time'),
        end_time: formData.get('end_time'),
        priority: parseInt(formData.get('priority')),
        is_active: formData.get('is_active') ? true : false,
    };

    try {
        const response = currentFlashSaleId
            ? await Ajax.put(`/api/marketing/flash-sales/${currentFlashSaleId}`, data)
            : await Ajax.post('/api/marketing/flash-sales', data);

        if (response.success) {
            Toast.showToast(response.message, 'success');
            flashSaleModal.close();
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
});
