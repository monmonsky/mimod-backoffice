import Ajax from '../../../utils/ajax.js';
import Toast from '../../../components/toast.js';

let currentCouponId = null;

window.openCreateModal = function() {
    currentCouponId = null;
    document.getElementById('couponForm').reset();
    document.querySelector('#couponModal h3').textContent = 'Create Coupon';
    couponModal.showModal();
};

window.viewCoupon = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/coupons/${id}`);

        if (response.success) {
            const { coupon, usage } = response.data;

            const typeLabels = {
                percentage: 'Percentage',
                fixed: 'Fixed Amount',
                free_shipping: 'Free Shipping'
            };

            const valueDisplay = coupon.type === 'percentage'
                ? `${coupon.value}%`
                : coupon.type === 'fixed'
                ? `Rp ${parseFloat(coupon.value).toLocaleString('id-ID')}`
                : 'Free';

            let html = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-base-content/60">Code</div>
                            <div class="font-bold text-lg text-primary">${coupon.code}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Name</div>
                            <div class="font-medium">${coupon.name}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Type</div>
                            <div>${typeLabels[coupon.type]}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Discount Value</div>
                            <div class="font-bold text-success">${valueDisplay}</div>
                        </div>
                        ${coupon.min_purchase ? `
                        <div>
                            <div class="text-sm text-base-content/60">Minimum Purchase</div>
                            <div>Rp ${parseFloat(coupon.min_purchase).toLocaleString('id-ID')}</div>
                        </div>
                        ` : ''}
                        ${coupon.max_discount ? `
                        <div>
                            <div class="text-sm text-base-content/60">Maximum Discount</div>
                            <div>Rp ${parseFloat(coupon.max_discount).toLocaleString('id-ID')}</div>
                        </div>
                        ` : ''}
                        <div>
                            <div class="text-sm text-base-content/60">Usage</div>
                            <div>${coupon.usage_count} / ${coupon.usage_limit || '∞'}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Per Customer Limit</div>
                            <div>${coupon.usage_limit_per_customer}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">Start Date</div>
                            <div>${new Date(coupon.start_date).toLocaleString('id-ID')}</div>
                        </div>
                        <div>
                            <div class="text-sm text-base-content/60">End Date</div>
                            <div>${new Date(coupon.end_date).toLocaleString('id-ID')}</div>
                        </div>
                    </div>

                    ${coupon.description ? `
                    <div>
                        <div class="text-sm text-base-content/60">Description</div>
                        <div class="mt-1">${coupon.description}</div>
                    </div>
                    ` : ''}

                    <div class="divider">Usage History</div>

                    ${usage.length > 0 ? `
                        <div class="overflow-x-auto max-h-64">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Discount Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${usage.map(u => `
                                        <tr>
                                            <td>${new Date(u.used_at).toLocaleString('id-ID')}</td>
                                            <td>Customer #${u.customer_id}</td>
                                            <td>Rp ${parseFloat(u.discount_amount).toLocaleString('id-ID')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : `
                        <div class="text-center py-4 text-base-content/60">
                            No usage history yet
                        </div>
                    `}
                </div>
            `;

            document.getElementById('couponDetails').innerHTML = html;
            viewCouponModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error')(error);
    }
};

window.editCoupon = async function(id) {
    try {
        const response = await Ajax.get(`/api/marketing/coupons/${id}`);

        if (response.success) {
            currentCouponId = id;
            const coupon = response.data.coupon;
            const form = document.getElementById('couponForm');

            form.querySelector('[name="code"]').value = coupon.code;
            form.querySelector('[name="name"]').value = coupon.name;
            form.querySelector('[name="description"]').value = coupon.description || '';
            form.querySelector('[name="type"]').value = coupon.type;
            form.querySelector('[name="value"]').value = coupon.value;
            form.querySelector('[name="min_purchase"]').value = coupon.min_purchase || '';
            form.querySelector('[name="max_discount"]').value = coupon.max_discount || '';
            form.querySelector('[name="usage_limit"]').value = coupon.usage_limit || '';
            form.querySelector('[name="usage_limit_per_customer"]').value = coupon.usage_limit_per_customer;

            // Format datetime-local
            form.querySelector('[name="start_date"]').value = new Date(coupon.start_date).toISOString().slice(0, 16);
            form.querySelector('[name="end_date"]').value = new Date(coupon.end_date).toISOString().slice(0, 16);
            form.querySelector('[name="is_active"]').checked = coupon.is_active;

            document.querySelector('#couponModal h3').textContent = 'Edit Coupon';
            couponModal.showModal();
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error')(error);
    }
};

window.deleteCoupon = async function(id) {
    if (!confirm('Are you sure you want to delete this coupon?')) return;

    try {
        const response = await Ajax.delete(`/api/marketing/coupons/${id}`);

        if (response.success) {
            Toast.showToast(response.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error')(error);
    }
};

document.getElementById('couponForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        code: formData.get('code'),
        name: formData.get('name'),
        description: formData.get('description'),
        type: formData.get('type'),
        value: parseFloat(formData.get('value')),
        min_purchase: formData.get('min_purchase') ? parseFloat(formData.get('min_purchase')) : null,
        max_discount: formData.get('max_discount') ? parseFloat(formData.get('max_discount')) : null,
        usage_limit: formData.get('usage_limit') ? parseInt(formData.get('usage_limit')) : null,
        usage_limit_per_customer: parseInt(formData.get('usage_limit_per_customer')),
        start_date: formData.get('start_date'),
        end_date: formData.get('end_date'),
        is_active: formData.get('is_active') ? true : false,
    };

    try {
        const response = currentCouponId
            ? await Ajax.put(`/api/marketing/coupons/${currentCouponId}`, data)
            : await Ajax.post('/api/marketing/coupons', data);

        if (response.success) {
            Toast.showToast(response.message, 'success');
            couponModal.close();
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        Toast.showToast('An error occurred', 'error')(error);
    }
});
