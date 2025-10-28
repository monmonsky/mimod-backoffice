# API-Driven Module Documentation

Dokumentasi untuk standardisasi module menjadi fully API-driven architecture.

---

## 📚 Available Documents

### 1. [QUICK_API_DRIVEN.md](./QUICK_API_DRIVEN.md)
**Quick Reference untuk Developer Berpengalaman**

✅ Use this when:
- Sudah familiar dengan API-driven pattern
- Butuh copy-paste template cepat
- Perlu refresher singkat

**Contains:**
- TL;DR checklist
- Copy-paste JavaScript template
- Common mistakes
- Troubleshooting table
- Time estimates

**Reading time:** 10-15 minutes

---

### 2. [STANDARDIZE_MODULE_TO_API_DRIVEN.md](./STANDARDIZE_MODULE_TO_API_DRIVEN.md)
**Complete Implementation Guide**

✅ Use this when:
- First time implementing API-driven module
- Perlu penjelasan detail
- Perlu referensi lengkap

**Contains:**
- Complete step-by-step guide
- Detailed explanations
- Multiple examples
- Testing checklist
- Troubleshooting section
- Common gotchas
- Success criteria

**Reading time:** 30-45 minutes

---

## 🚀 Quick Start

### For Experienced Developers
1. Read [QUICK_API_DRIVEN.md](./QUICK_API_DRIVEN.md)
2. Copy-paste template JavaScript
3. Modify for your module
4. Test with checklist

**Estimated time:** 2-3 hours

### For First-Time Implementation
1. Read [STANDARDIZE_MODULE_TO_API_DRIVEN.md](./STANDARDIZE_MODULE_TO_API_DRIVEN.md) completely
2. Study reference file: `resources/js/modules/marketing/coupons/index.js`
3. Follow step-by-step guide
4. Test with complete checklist

**Estimated time:** 4-6 hours (including learning)

---

## 🎯 Reference Implementation

**Primary Reference:**
- **Module:** Marketing - Coupons
- **Files:**
  - JS: `resources/js/modules/marketing/coupons/index.js`
  - Blade: `resources/views/pages/marketing/coupons/index.blade.php`
  - Controller: `app/Http/Controllers/Marketing/CouponsController.php`
  - API Controller: `app/Http/Controllers/Api/Marketing/CouponApiController.php`

**Copy these patterns untuk module baru!**

---

## ⚠️ Critical Points

### Response Structure
```javascript
// ❌ WRONG
if (response.success) { }

// ✅ CORRECT
if (response.status) { }
```

### Toast Notifications
```javascript
// ❌ WRONG
await Ajax.create('/api/items', data);

// ✅ CORRECT
await Ajax.create('/api/items', data, { showToast: true });
```

### Data Reload
```javascript
// ❌ WRONG
await Ajax.destroy(`/api/items/${id}`);

// ✅ CORRECT
await Ajax.destroy(`/api/items/${id}`, { showToast: true });
await loadData();
```

---

## 🔧 Troubleshooting

### Data tidak tampil?
→ Check `response.status` not `response.success`

### Action buttons hilang?
→ Remove permission checks atau pastikan syntax benar

### Toast tidak muncul?
→ Add `showToast: true` di Ajax options

### Data tidak reload?
→ Add `await loadData()` setelah CRUD operations

**More:** See [Troubleshooting section](./STANDARDIZE_MODULE_TO_API_DRIVEN.md#troubleshooting) in complete guide

---

## 📋 Implementation Checklist

Quick checklist untuk memastikan implementasi benar:

- [ ] Controller: Pure `return view()` only
- [ ] Blade: Empty tbody + statistics with IDs
- [ ] JavaScript: `loadData()` on page ready
- [ ] Response check: `response.status` NOT `response.success`
- [ ] CRUD operations: `showToast: true` + `await loadData()`
- [ ] No page refresh on any operation
- [ ] Loading indicator shows when filtering
- [ ] Toast appears on create/update/delete
- [ ] Action buttons visible
- [ ] Console has no errors

---

## 🎓 Learning Path

### Beginner
1. Read complete guide: [STANDARDIZE_MODULE_TO_API_DRIVEN.md](./STANDARDIZE_MODULE_TO_API_DRIVEN.md)
2. Study reference: `marketing/coupons/index.js`
3. Implement your first module
4. Review troubleshooting section when stuck

### Intermediate
1. Skim through complete guide for architecture understanding
2. Use quick reference: [QUICK_API_DRIVEN.md](./QUICK_API_DRIVEN.md)
3. Copy-paste template and modify
4. Refer to troubleshooting as needed

### Advanced
1. Use quick reference only: [QUICK_API_DRIVEN.md](./QUICK_API_DRIVEN.md)
2. Implement directly
3. Refer back only for edge cases

---

## 📞 Support

**Issues?**
1. Check [Troubleshooting section](./STANDARDIZE_MODULE_TO_API_DRIVEN.md#troubleshooting)
2. Compare dengan reference file: `marketing/coupons/index.js`
3. Review [Common Gotchas](./STANDARDIZE_MODULE_TO_API_DRIVEN.md#8-common-gotchas)

---

## 📝 Update History

- **2025-01-XX**: Initial documentation split (Quick Reference + Complete Guide)
- Based on marketing/coupons implementation
- Added comprehensive troubleshooting section
