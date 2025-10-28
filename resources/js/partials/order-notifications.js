/**
 * Order Notifications
 * Real-time notification system for pending orders
 */

let orderNotificationInterval;

async function fetchOrderNotifications() {
    try {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            return;
        }

        // Fetch pending orders count
        const countResponse = await fetch('/api/orders/pending/count', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!countResponse.ok) {
            console.error('Failed to fetch order count');
            return;
        }

        const countData = await countResponse.json();
        const count = countData.data?.count || 0;

        // Update badge
        const badge = document.getElementById('order-notification-badge');
        if (badge) {
            if (count > 0) {
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        // Fetch recent pending orders
        const ordersResponse = await fetch('/api/orders/pending/recent?limit=5', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!ordersResponse.ok) {
            console.error('Failed to fetch recent orders');
            return;
        }

        const ordersData = await ordersResponse.json();
        const orders = ordersData.data || [];

        // Update dropdown content
        const content = document.getElementById('order-notification-content');
        if (!content) return;

        if (orders.length === 0) {
            content.innerHTML = `
                <div class="flex flex-col items-center justify-center p-8 text-center">
                    <span class="iconify lucide--shopping-cart size-12 text-base-content/20 mb-2"></span>
                    <p class="text-sm text-base-content/60">No pending orders</p>
                </div>
            `;
        } else {
            content.innerHTML = orders.map(order => {
                const createdAt = new Date(order.created_at);
                const timeAgo = getTimeAgo(createdAt);
                const amount = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(order.total_amount);

                return `
                    <div class="hover:bg-base-200/20 flex items-start gap-3 p-4 transition-all border-b border-base-300 last:border-0">
                        <div class="flex size-10 items-center justify-center rounded-full bg-primary/10">
                            <span class="iconify lucide--shopping-bag size-5 text-primary"></span>
                        </div>
                        <div class="grow">
                            <p class="text-sm font-medium leading-tight">${order.order_number}</p>
                            <p class="text-xs text-base-content/60 mt-0.5">${amount}</p>
                            <p class="text-xs text-base-content/60 mt-1">${timeAgo}</p>
                            <div class="mt-2">
                                <a href="/orders/pending-orders" class="btn btn-xs btn-primary">
                                    View Order
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
    } catch (error) {
        console.error('Error fetching order notifications:', error);
    }
}

function getTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);

    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + ' tahun lalu';

    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + ' bulan lalu';

    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + ' hari lalu';

    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + ' jam lalu';

    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + ' menit lalu';

    return 'Baru saja';
}

// Load notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchOrderNotifications();

    // Refresh every 30 seconds
    orderNotificationInterval = setInterval(fetchOrderNotifications, 30000);
});

// Load notifications when dropdown is opened
const orderNotificationBtn = document.getElementById('order-notification-btn');
if (orderNotificationBtn) {
    orderNotificationBtn.addEventListener('click', function() {
        fetchOrderNotifications();
    });
}

export { fetchOrderNotifications, getTimeAgo };
