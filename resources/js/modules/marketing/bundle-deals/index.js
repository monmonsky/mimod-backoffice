import Toast from '../../../components/toast.js';
import Ajax from '../../../utils/ajax.js';

let currentBundleId = null;

window.openCreateModal = function() {
    currentBundleId = null;
    document.getElementById('bundleForm').reset();
    document.querySelector('#bundleModal h3').textContent = 'Create Bundle Deal';
    bundleModal.showModal();
};

window.viewBundle = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/bundle-deals/${id}`);

        if (response.success) {
            const { bundle, items } = response.data;

            const savings = bundle.original_price - bundle.bundle_price;
            const savingsPercent = bundle.original_price > 0 ? (savings / bundle.original_price * 100) : 0;

            let html = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/60">Name</div>
                            <div class="font-medium">${bundle.name}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Slug</div>
                            <div>${bundle.slug}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Bundle Price</div>
                            <div class="font-bold text-success text-lg">Rp ${parseFloat(bundle.bundle_price).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Original Price</div>
                            <div class="line-through">Rp ${parseFloat(bundle.original_price).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Savings</div>
                            <div class="text-success font-medium">
                                ${savingsPercent.toFixed(0)}% (Rp ${savings.toLocaleString('id-ID')})
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Stock</div>
                            <div>${bundle.sold_count} sold${bundle.stock_limit ? ` / ${bundle.stock_limit}` : ''}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Start Date</div>
                            <div>${new Date(bundle.start_date).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">End Date</div>
                            <div>${new Date(bundle.end_date).toLocaleString('id-ID')}</div>
                        </div>
                    </div>

                    ${bundle.description ? `
                    <div>
                        <div class="text-sm text-base-content/60">Description</div>
                        <div class="mt-1">${bundle.description}</div>
                    </div>
                    ` : ''}

                    <div class="divider">Bundle Items</div>

                    ${items.length > 0 ? `
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${items.map(item => `
                                        <tr>
                                            <td>${item.product_name}</td>
                                            <td>${item.quantity}</td>
                                            <td>Rp ${parseFloat(item.price).toLocaleString('id-ID')}</td>
                                            <td>Rp ${(parseFloat(item.price) * item.quantity).toLocaleString('id-ID')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                                <tfoot>
                                    <tr class="font-bold">
                                        <td colspan="3" class="text-right">Total:</td>
                                        <td>Rp ${parseFloat(bundle.original_price).toLocaleString('id-ID')}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    ` : `
                        <div class="text-center py-4 text-base-content/60">
                            No items added yet
                        </div>
                    `}
                </div>
            `;

            document.getElementById('bundleDetails').innerHTML = html;
            viewBundleModal.showModal();
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

window.editBundle = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/bundle-deals/${id}`);

        if (response.success) {
            currentBundleId = id;
            const bundle = response.data.bundle;
            const form = document.getElementById('bundleForm');

            form.querySelector('[name="name"]').value = bundle.name;
            form.querySelector('[name="slug"]').value = bundle.slug;
            form.querySelector('[name="description"]').value = bundle.description || '';
            form.querySelector('[name="bundle_price"]').value = bundle.bundle_price;
            form.querySelector('[name="original_price"]').value = bundle.original_price;
            form.querySelector('[name="stock_limit"]').value = bundle.stock_limit || '';
            form.querySelector('[name="image"]').value = bundle.image || '';
            form.querySelector('[name="start_date"]').value = new Date(bundle.start_date).toISOString().slice(0, 16);
            form.querySelector('[name="end_date"]').value = new Date(bundle.end_date).toISOString().slice(0, 16);
            form.querySelector('[name="is_active"]').checked = bundle.is_active;

            document.querySelector('#bundleModal h3').textContent = 'Edit Bundle Deal';
            bundleModal.showModal();
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

window.deleteBundle = async function(id) {
    if (!confirm('Are you sure you want to delete this bundle deal?')) return;

    try {
        const response = await Ajax.delete(`/api/marketing/bundle-deals/${id}`);

        if (response.success) {
            Toast.showToast(response.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

window.manageItems = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/bundle-deals/${id}`);

        if (response.success) {
            const { items } = response.data;

            let html = `
                <div class="space-y-4">
                    <div class="alert alert-info">
                        <span class="iconify lucide--info size-4"></span>
                        <span>Add or remove items from this bundle</span>
                    </div>

                    <button class="btn btn-primary btn-sm" onclick="addItemToBundle(${id})">
                        <span class="iconify lucide--plus size-4"></span>
                        Add Item
                    </button>

                    ${items.length > 0 ? `
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${items.map(item => `
                                        <tr>
                                            <td>${item.product_name}</td>
                                            <td>${item.quantity}</td>
                                            <td>Rp ${parseFloat(item.price).toLocaleString('id-ID')}</td>
                                            <td>Rp ${(parseFloat(item.price) * item.quantity).toLocaleString('id-ID')}</td>
                                            <td>
                                                <button class="btn btn-xs btn-error" onclick="removeItemFromBundle(${id}, ${item.product_id})">
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
                            No items added yet
                        </div>
                    `}
                </div>
            `;

            document.getElementById('itemsList').innerHTML = html;
            manageItemsModal.showModal();
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

window.addItemToBundle = function(bundleId) {
    Toast.showToast('Item management feature coming soon', 'info');
};

window.removeItemFromBundle = async function(bundleId, productId) {
    if (!confirm('Remove this item from bundle?')) return;

    try {
        const response = await Ajax.delete(`/api/marketing/bundle-deals/${bundleId}/items`, {
            product_id: productId
        });

        if (response.success) {
            Toast.showToast(response.message, 'success');
            manageItems(bundleId);
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
};

document.getElementById('bundleForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        slug: formData.get('slug') || null,
        description: formData.get('description'),
        bundle_price: parseFloat(formData.get('bundle_price')),
        original_price: parseFloat(formData.get('original_price')),
        stock_limit: formData.get('stock_limit') ? parseInt(formData.get('stock_limit')) : null,
        image: formData.get('image') || null,
        start_date: formData.get('start_date'),
        end_date: formData.get('end_date'),
        is_active: formData.get('is_active') ? true : false,
    };

    try {
        const response = currentBundleId
            ? await Ajax.put(`/api/marketing/bundle-deals/${currentBundleId}`, data)
            : await Ajax.post('/api/marketing/bundle-deals', data);

        if (response.success) {
            Toast.showToast(response.message, 'success');
            bundleModal.close();
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        Toast.showToast("An error occurred", "error");
    }
});
